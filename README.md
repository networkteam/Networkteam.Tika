Networkteam.Tika
================

A simple wrapper to use [Apache Tika](http://tika.apache.org/) inside Flow Framework applications:

- Detect the language of variouse resources
- Detect the content type of a resource
- Extract metadata information in XML or JSON format

It works by calling the Java binary for Tika and passes a Flow Resource (e.g. uploaded content)
to the command.

[![Build Status](https://travis-ci.org/networkteam/Networkteam.Tika.png?branch=master)](https://travis-ci.org/networkteam/Networkteam.Tika)

Installation
------------

Install the package and adjust the setting "Networkteam.Tika.javaCommand" which defaults to "java" if your
Java executable is installed in a special directory.

Usage
-----

Inject `Networkteam\Tika\TikaService` into your class and call any of the `get*` methods with a Resource
to get information using Apache Tika.

License
-------

This package is released under the [MIT license](http://opensource.org/licenses/MIT).
