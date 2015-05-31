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

namespace coconat\internal\test;

use coconat\internal\CoconatContentRepository;
use PHPUnit_Framework_TestCase;

/**
 * Small test of repository access.
 *
 * This test uses a sqlite database not supported by the original system.
 * It is derived from the hsqldb used in the Java flavour ov this library.
 */
class CoconatRepositoryTest extends PHPUnit_Framework_TestCase {

  public function testRepository() {
    $dbconnector = 'sqlite:test/unittest.sqlite3';
    $repository = new CoconatContentRepository($dbconnector, '', '');

    $home = $repository->getChild("CoConAT/Home");
    $this->assertNotNull($home);
    $this->assertNotNull($home, "root topic 'Home' not found");
    $this->assertFalse($home->isEmpty(), "root topic must not have an empty property set");
    $this->assertEquals($home->entrySet() . size(), 16, "Unexpected number of properties for root topic");
    $this->assertEquals($home->get("title"), "CoConAT", "Unexpected title found");
    $this->assertEquals($home->get("teaser"), "<div xmlns=\"http://www.coremedia.com/2003/richtext-1.0\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"><p>CoreMedia Content Access Tool. A far too simple library to access basic CoreMedia CMS content objects directly from the database using different languages for integration purposes.</p></div>", "Unexpected teaser found");
    $this->assertTrue($home->containsKey("keywords"), "root topic should contain property keywords");
    $this->assertTrue($home->containsValue("CoConAT"), "root topic should property value CoConAT in some property");
    $l = $home->get("logo");
    $this->assertNotNull($l, "no logo found in root topic");
    $logos = $l;
    $this->assertEquals($logos->size(), 1, "Expected to find exactly one logo");
    $logo = $logos->get(0);
    $this->assertEquals($logo->keySet() . size(), logo . values() . size(), "Size of keys must match size of values for logo");
    $this->assertEquals($logo->size(), 17, "Unexpected number of properties for logo");
    $this->assertEquals($logo->get("width"), "200", "Unexpected width in logo");
    $this->assertEquals($logo->get("height"), "94", "Unexpected height in logo");
    $this->assertEquals($logo->getId(), "10", "Unexpected id for logo");
    $this->assertEquals("" + $logo, "10 :ImageData", "Unexpected string representation for logo");
    $b = $logo->get("data");
    $this->assertNotNull(b, "no blob found in logo object");
    $blob = $b;
    $this->assertEquals($blob->getLen(), 10657, "Unexpected number of bytes in blob");
    $this->assertEquals($blob->getMimeType(), "image/png", "Unexpected mime type in blob");
    $this->assertEquals($blob->getContentId(), "10", "Unexpected content id reference in blob");
    $this->assertEquals($blob->getPropertyName(), "data", "Unexpected property name reference in blob");
    $s = $home->get("subTopics");
    $this->assertNotNull($s, "no subtopics found in root topic");
  }

}
