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
use coconat\Repository;
use PDO;
use PDOException;

/**
 * Simple non caching implementation of content repository access.
 */
class CoconatContentRepository implements Repository {

  private $log;
  private $dbDescriptor;
  private $dbUser;
  private $dbPassword;
  private $dbConnection;

  function __construct($dbDescriptor, $dbUser, $dbPassword) {
    $this->log = LoggerFactory::getLogger(__CLASS__);
    $this->dbDescriptor = $dbDescriptor;
    $this->dbUser = $dbUser;
    $this->dbPassword = $dbPassword;

    $this->dbConnection = new PDO($dbDescriptor, $dbUser, $dbPassword);
  }

  private function getType($id) {
    $type = null;
    $query = "SELECT * FROM Resources WHERE id_ = '$id'";
    $this->log->debug("getType() query: {}", array($query));
    try {
      $statement = $this->dbConnection->query($query);
      while ($row = $statement->fetch()) {
        $type = $row['documentType_'];
        $this->log->debug("getType() document type {}", array($type));
      }

      if ($type == null) {
        $this->log->debug("getType() could be a folder");
        $type = ""; // Folder indication
      }
    } catch (PDOException $e) {
      $this->log->error("getType() id {}: {}", array($id, $e->getMessage()));
    }
    return $type;
  }

  private function getProperties($type, $id) {
    $properties = array();
    if ($type == null || strlen($type) == 0) {
      return $properties;
    }
    $query = "SELECT * FROM $type WHERE id_ = $id ORDER BY version_ DESC";
    try {
      $statement = $this->dbConnection->query($query);
      $row = $statement->fetch();
      if ($row) {
        $id = $row['id_'];
        $version = $row['version_'];
        $this->log->debug("getProperties() {}/{} :{}", array($id, $version, $type));

        foreach ($row as $key => $value) {
          $this->log->debug("getProperties() {} -> {}", array($key, $value));
          // TODO: only string keys to be used
          $properties[$key] = $value;
        }

        // select link lists
        $this->log->debug("getProperties() selecting link lists");
        $linkLists = array();
        try {
          $linkquery = "SELECT * FROM LinkLists WHERE sourcedocument = $id AND sourceversion = $version ORDER BY propertyName ASC, linkIndex ASC";
          $linkstatement = $this->dbConnection->query($linkquery);
          while ($row = $linkstatement->fetch()) {
            $propertyName = $row['propertyName'];
            $targetid = $row['targetDocument'];
            $linkIndex = $row['linkIndex'];
            $this->log->debug("getProperties() linklist {} -> {}", array($propertyName, $targetid));
            if (!array_key_exists($propertyName, $linkLists)) {
              $linkLists[$propertyName] = array();
            }
            $linkLists[$propertyName][$linkIndex] = $targetid;
          }
        } catch (PDOException $e) {
          $this->log->error("getProperties() (linklists) id {}: {}", array($id, $e->getMessage()));
        }
        foreach ($linkLists as $key => $idlist) {
          $this->log->debug("getProperties() linklist {} -> {}", array($key, count($idlist)));
          $list = array();
          foreach ($idlist as $linkid) {
            // TODO: lazy loading
            $t = $this->getType($linkid);
            $p = $this->getProperties($t, $linkid);
            $this->log->debug("getProperties() link {} :{}", array($linkid, $t));
            $list[] = new CoconatContent($t, $linkid, $p);
          }
          $properties[$key] = $list;
        }

        // select blobs
        $blobIds = array();
        $propertyNames = array();
        try {
          $blobidquery = "SELECT * FROM Blobs WHERE documentid = $id AND documentversion = $version ORDER BY propertyName ASC";
          $blobstatement = $this->dbConnection->query($blobidquery);
          while ($row = $blobstatement->fetch()) {
            $propertyName = $row['propertyName'];
            $blobId = $row['target'];
            $blobIds[] = $blobId;
            $propertyNames[] = $propertyName;
          }
        } catch (PDOException $e) {
          $this->log->error("getProperties() (blobs) id {}: {}", array($id, $e->getMessage()));
        }

        for ($i = 0; $i < count($blobIds); $i++) {
          $blobId = $blobIds[$i];
          $propertyName = $propertyNames[$i];
          try {
            $blobdataquery = "SELECT * FROM BlobData WHERE id = $blobId";
            $statement = $this->dbConnection->query($blobdataquery);
            while ($row = $statement->fetch()) {
              $mimeType = $row['mimeType'];
              $data = $row['data'];
              $len = $row['len'];
              $this->log->debug("getProperties() blob {}  at {} with {} bytes of mime type {}", array($propertyName, $id, $len, $mimeType));
              $blob = new CoconatBlob($id, $propertyName, $mimeType, $len, $data);
              $properties[$propertyName] = $blob;
            }
          } catch (PDOException $e) {
            $this->log->error("getProperties() blob {} of id {}: {}", array($propertyName, $id, $e->getMessage()));
          }
        }

        // select xml
        $xmlquery = "SELECT * FROM Texts WHERE documentid = $id AND documentversion = $version ORDER BY propertyName ASC";
        $xmlstatement = $this->dbConnection->query($xmlquery);
        while ($xmlrow = $xmlstatement->fetch()) {
          $propertyName = $xmlrow['propertyName'];
          $target = $xmlrow['target'];

          $text = "";
          $textquery = "SELECT * FROM SgmlText WHERE id = $target";
          $textstatement = $this->dbConnection->query($textquery);
          while ($textrow = $textstatement->fetch()) {
            $xmlText = $textrow['text'];
            $text = "$text$xmlText";
          }
          $data = "";
          $dataquery = "SELECT * FROM SgmlData WHERE id = $target";
          $datastatement = $this->dbConnection->query($dataquery);
          while ($datarow = $datastatement->fetch()) {
            $xmlData = $datarow['data'];
            $data = "$data$xmlData";
          }

          $this->log->debug("getProperties() xml {} text: {}", array($propertyName, $text));
          $this->log->debug("getProperties() xml {} data: {}", array($propertyName, $data));
          $properties[$propertyName] = CoconatTextConverter::convert($text, $data);
        }
      }
    } catch (PDOException $e) {
      $this->log->error("getProperties() id {}: {}", array($id, $e->getMessage()));
    }
    return $properties;
  }

  private function getChildIdFromParentId($name, $parentId) {
    $id = null;
    try {
      $query = "SELECT * FROM Resources WHERE folderid_ = $parentId AND name_ = '$name'";
      $statement = $this->dbConnection->query($query);
      while ($row = $statement->fetch()) {
        print_r(array_keys($row));
        $id = $row['id_'];
        $this->log->info("getChildIdFromParentId() {}/{}: {}", array($parentId, $name, $id));
      }
    } catch (PDOException $e) {
      $this->log->error("getChildIdFromParentId() {} {}: {}", array($name, $parentId, $e->getMessage()));
    }
    return $id;
  }

  private function getChildId($path) {
    $arcs = explode("/", $path);
    $currentFolder = "1"; // root
    foreach ($arcs as $folder) {
      $this->log->info("getChildId() lookup {} in {}", array($folder, $currentFolder));
      if (strlen($folder) > 0) {
        $currentFolder = $this->getChildIdFromParentId($folder, $currentFolder);
      }
    }
    return $currentFolder;
  }

  public function getContent($id) {
    $result = null;

    $type = $this->getType($id);
    if ($type != null) {
      $properties = $this->getProperties($type, $id);
      // properties . putAll(additionalProperties);
      $result = new CoconatContent($type, $id, $properties);
    } // if

    return $result;
  }

  public function getChild($path) {
    $this->log->info("getChild() {}", array($path));
    return $this->getContent($this->getChildId($path));
  }

}
