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

use coconat\Blob;

/**
 * Simple implementation of the Blob interface.
 */
class CoconatBlob implements Blob {

  private $contentId;
  private $propertyName;
  private $mimeType;
  private $len;
  private $bytes;

  function __construct($contentId, $propertyName, $mimeType, $len, $bytes) {
    $this->contentId = $contentId;
    $this->propertyName = $propertyName;
    $this->mimeType = $mimeType;
    $this->len = $len;
    $this->bytes = $bytes;
  }

  public function getContentId() {
    return $this->contentId;
  }

  public function getPropertyName() {
    return $this->propertyName;
  }

  public function getMimeType() {
    return $this->mimeType;
  }

  public function getLen() {
    return $this->len;
  }

  public function getBytes() {
    return $this->bytes;
  }

}
