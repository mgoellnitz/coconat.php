<?php

/*
 *
 * Copyright 2015-2019 Martin Goellnitz
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

use lf4php\LoggerFactory;
use coconat\internal\CoconatContentRepository;
use PHPUnit\Framework\TestCase;

/**
 * Small test of repository access.
 *
 * This test uses a sqlite database not supported by the original system.
 * It is derived from the hsqldb used in the Java flavour ov this library.
 */
class CoconatRepositoryTest extends TestCase {

  public function testRepository() {
    $repository = new CoconatContentRepository('sqlite:test/unittest.sqlite3', '', '');

    $home = $repository->getChild("CoConAT/Home");
    $this->assertNotNull($home);
    $this->assertNotNull($home, "root topic 'Home' not found");
    $this->assertFalse($home->isEmpty(), "root topic must not have an empty property set");
    $this->assertEquals(29, count($home->entries()), "Unexpected number of properties for root topic");
    $this->assertEquals("CoConAT", $home->get("title"), "Unexpected title found");
    $this->assertEquals("<div xmlns=\"http://www.coremedia.com/2003/richtext-1.0\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"><p>CoreMedia Content Access Tool. A far too simple library to access basic CoreMedia CMS content objects directly from the database using different languages for integration purposes.</p></div>", $home->get("teaser"), "Unexpected teaser found");
    $this->assertTrue($home->hasProperty("keywords"), "root topic should contain property keywords");
    $logos = $home->get("logo");
    $this->assertNotNull($logos, "no logo list found in root topic");
    $this->assertEquals(1, count($logos), "Expected to find exactly one logo");
    $logo = $logos[0];
    $this->assertEquals(33, $logo->size(), "Unexpected number of properties for logo");
    $this->assertEquals("200", $logo->get("width"), "Unexpected width in logo");
    $this->assertEquals("94", $logo->get("height"), "Unexpected height in logo");
    $this->assertEquals("10", $logo->getId(), "Unexpected id for logo");
    $this->assertEquals("9", $repository->getParentIdFromChildId($logo->getId()), "Unexpected parent id for logo");
    $blob = $logo->get("data");
    $this->assertNotNull($blob, "no blob found in logo object");
    $this->assertEquals(10657, $blob->getLen(), "Unexpected number of bytes in blob");
    $this->assertEquals("image/png", $blob->getMimeType(), "Unexpected mime type in blob");
    $this->assertEquals("10", $blob->getContentId(), "Unexpected content id reference in blob");
    $this->assertEquals("data", $blob->getPropertyName(), "Unexpected property name reference in blob");
    $s = $home->get("subTopics");
    $this->assertNotNull($s, "no subtopics found in root topic");
  }

}
