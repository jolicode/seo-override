# Changes between versions

## Not yet released

## 0.7.0 (2022-03-02)

* Add support for Symfony 6.0
* Drop support for PHP < 7.4
* Drop support for Symfony < 4.4

## 0.6.3 (2022-02-24)

* Allow PHP 8
* Fix error "Backtrack limit exhausted" with really big page

## 0.6.2 (2019-08-22)

* Fix SQL issue for some database when domain is null
* Fix deprecation messages and fix lowest package dependencies

## 0.6.1 (2018-04-24)

* Fix HTML overrides when tags contain new lines

## 0.6.0 (2018-03-26)

* Drop compatibility with Symfony < 3.0
* Add compatibility with Symfony 4.0
* [BC BREAK][Doctrine] Changed SeoOverride SQL index to use a hash of the path
(you should update your database schema due to SQL column and index changes)

## 0.5.1 (2017-12-28)

* Fix Symfony 3.4 / 4.0 compatibility with DataCollector

## 0.5.0 (2017-10-12)

* [BC BREAK] Remove SeoManagerInterface
* Add blacklist behaviour to avoid useless fetcher runs
* Add blacklisters checking for XHR or HTTP methods

## 0.4.0 (2017-09-12)

* [BC BREAK][Doctrine] Change the type of Seo Columns from string(255) to text
* [BC BREAK][Symfony] Remove Symfony Validator Asserts on Seo object

## 0.3.1 (2017-09-01)

* Fix support for BinaryFileResponse and other empty content Responses

## 0.3.0 (2017-08-18)

* Tweak tests
* Add Symfony Validator Asserts on Seo object

## 0.2.2 (2017-07-11)

* Fix Symfony data collector when listener did not run (after exception for example)

## 0.2.1 (2017-07-11)

* Allow to change encoding when converting HTML chars
* Fix Symfony services visibility

## 0.2.0 (2017-06-07)

* Add Web Profiler data collector
* Do not run fetchers on non 2XX responses

## 0.1.2 (2017-05-12)

* Fix return typehint on Doctrine entity getters

## 0.1.1 (2017-05-10)

* Force Doctrine column names in yaml mapping
* Fix deprecations in documentation

## 0.1 (2017-05-10)

* Initial release
