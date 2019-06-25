![CoConAT](https://raw.githubusercontent.com/mgoellnitz/coconat/master/template/coconat-small.png)

[![Latest Release](https://img.shields.io/github/release/mgoellnitz/coconat.php.svg)](https://github.com/mgoellnitz/coconat.php/releases/latest)
[![Build Status](https://travis-ci.org/mgoellnitz/coconat.php.svg?branch=master)](https://travis-ci.org/mgoellnitz/coconat.php)
[![Coverage Status](https://coveralls.io/repos/github/mgoellnitz/coconat.php/badge.svg?branch=master)](https://coveralls.io/github/mgoellnitz/coconat.php?branch=master)
[![License](https://poser.pugx.org/coconat/coconat.php/license)](https://packagist.org/packages/coconat/coconat.php)
[![Latest Unstable Version](https://poser.pugx.org/coconat/coconat.php/v/unstable)](https://packagist.org/packages/coconat/coconat.php)
[![Latest Stable Version](https://poser.pugx.org/coconat/coconat.php/version)](https://packagist.org/packages/coconat/coconat.php)

# CoConAT PHP flavour

This is the PHP flavour of the [CoConAT Content Access Tool](http://mgoellnitz.github.io/coconat/).
It is a small library to access the contents of a CoreMedia content repository through
direct access of a Content Server database (CMS, MLS, or RLS) in a structured way.

It is a direct rewrite of portions of the Java flavour to use PHP standard means
for database access.

CoConAT PHP ist available through [Packagist](https://packagist.org/packages/coconat/coconat-php).

## Building

Prerequisites are composer and phpunit.

```
composer update
```

The test run can be started via

```
phpunit --bootstrap vendor/autoload.php test
```

This unittest uses an sqlite3 database which can be create from the source with

```
sqlite3 test/unittest.sqlite3 < test/unittest.sql
```

## Usage example

A ReadContent.php example is included in the example directory which relies on
the same MySQL menusite database to exist as its Java counter part.

## Issue Reporting

Please use the [issue reporting facility at github](https://github.com/mgoellnitz/coconat.php/issues) 
to get in touch.
