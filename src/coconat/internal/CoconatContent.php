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

use coconat\Content;

/**
 * Internal implementation of content items from the CoconatContentRepository.
 */
class CoconatContent implements Content {

  private $documentType;
  private $id;
  private $properties = array();

  function __construct($type, $id, $properties) {
    $this->documentType = $type;
    $this->id = $id;
    $this->properties = $properties;
  }

  public function getId() {
    return $this->id;
  }

  public function getDocumentType() {
    return $this->documentType;
  }

}
