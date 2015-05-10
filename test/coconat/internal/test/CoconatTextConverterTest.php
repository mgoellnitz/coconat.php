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

use coconat\internal\CoconatTextConverter;
use PHPUnit_Framework_TestCase;

/**
 * Small test of the text converter .
 * 
 * Using a simple text data data segment to obtain the compound result.
 */
class CoconatTextConverterTest extends PHPUnit_Framework_TestCase {
  
  public function testConverter() {
    $text = "Enjoy the taste of a duck cutlet combined with caramelized onions. A dream!";
    $data = "(0003diva0005xmlns002Bhttp://www.coremedia.com/2003/richtext-1.0]a000Bxmlns:xlink001Dhttp://www.w3.org/1999/xlink](0001p-004B)0001p)0003div";
    $reference = "<div xmlns=\"http://www.coremedia.com/2003/richtext-1.0\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"><p>Enjoy the taste of a duck cutlet combined with caramelized onions. A dream!</p></div>";
    $result = CoconatTextConverter::convert($text, $data);
    $this->assertEquals($reference, $result, "Unexpected conversion result");
  }
  
}
