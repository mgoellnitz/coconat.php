<?php

/*
 * 
 * Copyright 2015 Martin Goellnitz
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * 
 */

namespace coconat\internal;

use lf4php\LoggerFactory;

/**
 * Text converter from text and data segments in the database to xml.
 * 
 * This is a more or less verbatim transpation of its Java counterpart
 * without using string buffers but strings. So, this can most likely be done 
 * in a more efficient way.
 */
class CoconatTextConverter {

  private $log;
  private $text;
  private $data;
  private $result;
  private $textPosition;
  private $dataPosition;

  function __construct($text, $data) {
    $this->log = LoggerFactory::getLogger(__CLASS__);
    $this->text = $text;
    $this->data = $data;

    $this->dataPosition = 0;
    $this->textPosition = 0;
    $this->result = "";
  }

  private function readHex($buf, $pos) {
    $this->log->debug("readHex({}) buf={}", array($pos, $buf));
    $this->log->debug("readHex() {} {} {} {}", array(hexdec($buf[$pos]), hexdec($buf[$pos+1]), hexdec($buf[$pos+2]), hexdec($buf[$pos+3])));
    $result = (hexdec($buf[$pos]) << 12) + (hexdec($buf[$pos + 1]) << 8) + (hexdec($buf[$pos + 2]) << 4) + hexdec($buf[$pos + 3]);
    $this->log->debug("readHex({}) result={}", array($pos, $result));
    return $result;
  }

  private function readStringLength() {
    $result = -1;
    if ($this->dataPosition + 3 < strlen($this->data)) {
      $result = $this->readHex($this->data, $this->dataPosition);
      $this->log->debug("readStringLength({}) result={}", array($this->dataPosition, $result));
    } // if
    return $result;
  }

  private function getStringFromData() {
    $length = $this->readStringLength();
    $this->log->debug("getStringFromData({}) length={}", array($this->dataPosition, $length));
    $this->dataPosition += 4;
    $result = null;
    if ($length >= 0 && $this->dataPosition + $length <= strlen($this->data)) {
      $result = "";
      if ($length > 0) {
        $result = substr($this->data, $this->dataPosition, $length);
      }
      $this->dataPosition += $length;
    }
    return $result;
  }

  private function issueElementStart() {
    $this->log->debug("issueElementStart()");
    $name = $this->getStringFromData();
    $this->log->debug("issueElementStart({}) name={}", array($this->dataPosition, $name));
    if ($name == null) {
      return;
    }

    $this->result = $this->result . "<" . $name;
    while (true) {
      if ($this->dataPosition >= strlen($this->data)) {
        return;
      }
      $flag = $this->data[$this->dataPosition];
      if ($flag != 'a') {
        break;
      }

      $this->dataPosition++;
      $attributeName = $this->getStringFromData();
      if ($attributeName == null) {
        return;
      }

      $attributeValue = $this->getStringFromData();
      if ($attributeValue == null) {
        return;
      }

      $hasValue = (strlen($attributeValue) > 0);
      if ($hasValue) {
        $attributeValue = substr($attributeValue, 0, strlen($attributeValue) - 1);
      }
      $this->result = $this->result . " " . $attributeName;
      if ($hasValue) {
        $this->result = $this->result . "=\"" . $attributeValue . "\"";
      }
    }
    $this->result = $this->result . ">";
  }

  private function issueElementEnd() {
    $name = $this->getStringFromData();
    if ($name != null) {
      $this->result = $this->result . "</" . $name . ">";
    }
  }

  private function writeText() {
    $length = $this->readStringLength();
    if ($length < 0) {
      $this->dataPosition--;
      return;
    } // if
    $this->dataPosition += 4;

    $buffersize = 0;
    if ($length > 0) {
      if ($length <= (strlen($this->text) - $this->textPosition)) {
        $buffersize = $length;
      } else {
        $buffersize = (strlen($this->text) - $this->textPosition);
      } // if
      if ($buffersize > 0) {
        $buffer = substr($this->text, $this->textPosition, $buffersize);
        $this->textPosition += $buffersize;
        $this->result = $this->result . $buffer;
      }
    }
  }

  private function mergeTextAndData() {
    while ($this->dataPosition < strlen($this->data)) {
      $flag = $this->data[$this->dataPosition++];
      switch ($flag) {
        case '(':
          $this->issueElementStart();
          break;
        case '-':
          $this->writeText();
          break;
        case ')':
          $this->issueElementEnd();
          break;
        default:
          throw new \Exception("Unknown code (" . $this->dataPosition . ", " . $flag . ": " . $this->result . ")");
      }
    }
    $this->log->debug("mergeTextAndData() {}", array($this->result));
    return $this->result;
  }

  public static function convert($text, $data) {
    $converter = new CoconatTextConverter($text, $data);
    $result = $converter->mergeTextAndData();
    return $result;
  }

}
