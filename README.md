![CoConAT](http://coconat.divshot.io/coconat-small.png)

[![Dependency Status](https://www.versioneye.com/user/projects/554fbbfff7db0d2f07000242/badge.svg?style=flat)](https://www.versioneye.com/user/projects/554fbbfff7db0d2f07000242)
[![Build Status](https://travis-ci.org/mgoellnitz/coconat.php.svg?branch=master)](https://travis-ci.org/mgoellnitz/coconat.php)
[![Coverage Status](https://coveralls.io/repos/mgoellnitz/coconat.php/badge.svg?branch=master)](https://coveralls.io/r/mgoellnitz/coconat.php?branch=master)
[![License](https://poser.pugx.org/coconat/coconat.php/license)](https://packagist.org/packages/coconat/coconat.php)

[![Latest Unstable Version](https://poser.pugx.org/coconat/coconat.php/v/unstable)](https://packagist.org/packages/coconat/coconat.php)
[![Latest Stable Version](https://poser.pugx.org/coconat/coconat.php/version)](https://packagist.org/packages/coconat/coconat.php)

# CoConAT PHP flavour

This is the PHP flavour of the [CoConAT Content Access Tool](http://coconat.divshot.io/).  
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

## Usage example

A ReadContent.php example is included which relies on the same MySQL menusite database
to exist as its Java counter part.

## To Do

Add a decent unit test coverage like with the Java flavour. Get independent of
MySQL database example or provide a PHP like way to delivery it or create it.
