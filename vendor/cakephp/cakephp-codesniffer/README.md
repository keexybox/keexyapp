# CakePHP Code Sniffer

[![Build Status](https://img.shields.io/travis/cakephp/cakephp-codesniffer/master.svg?style=flat-square)](https://travis-ci.org/cakephp/cakephp-codesniffer)
[![Coverage Status](https://img.shields.io/codecov/c/github/cakephp/cakephp-codesniffer.svg?style=flat-square)](https://codecov.io/github/cakephp/cakephp-codesniffer)
[![Total Downloads](https://img.shields.io/packagist/dt/cakephp/cakephp-codesniffer.svg?style=flat-square)](https://packagist.org/packages/cakephp/cakephp-codesniffer)
[![Latest Stable Version](https://img.shields.io/packagist/v/cakephp/cakephp-codesniffer.svg?style=flat-square)](https://packagist.org/packages/cakephp/cakephp-codesniffer)

This code works with [phpcs](http://pear.php.net/manual/en/package.php.php-codesniffer.php)
and checks code against the coding standards used in CakePHP.

:warning: The `master` branch contains codesniffer rules that are based on the
PSR2 standard. If you want to check against the historical CakePHP coding
standard use any of the `1.x` releases.

## Which version should I use?

| Sniffer version | CakePHP version | PHP min |
| -------- | ------- | ------- |
| 1.x | 2.x | PHP 5.4  |
| 2.x | 3.x | PHP 5.5 |
| 3.x | 3.x | PHP 5.6 |
| 4.x | 4.x | PHP 7.1 |

## Installation

You should install this codesniffer with composer:

	composer require --dev "cakephp/cakephp-codesniffer"
	vendor/bin/phpcs --config-set installed_paths /path/to/your/app/vendor/cakephp/cakephp-codesniffer

The second command lets `phpcs` know where to find your new sniffs. Ensure that
you do not overwrite any existing `installed_paths` value.

## Usage

:warning: Warning when these sniffs are installed with composer, ensure that
you have configured the CodeSniffer `installed_paths` setting.

Depending on how you installed the code sniffer changes how you run it. If you have
installed phpcs, and this package with PEAR, you can do the following:

	vendor/bin/phpcs --colors -p -s --standard=CakePHP /path/to/code

You can also copy the `phpcs.xml.dist` file to your project's root folder as `phpcs.xml`.
This file will import the CakePHP Coding Standard. From there you can edit it to
include/exclude as needed. With this file in place, you can run:

	vendor/bin/phpcs --colors -p -s /path/to/code

If you are using Composer to manage your CakePHP project, you can also add the below to your composer.json file:

```json
{
    "scripts": {
        "cs-check": "vendor/bin/phpcs --colors -p -s --extensions=ctp,php ./src ./tests"
    }
}
```

## Running Tests

You can run tests with composer. Because of how PHPCS test suites work, there is
additional configuration state in `phpcs` that is required.

```bash
composer test
```

Once this has been done once, you can use `phpunit --filter CakePHP` to run the
tests for the rules in this repository.

## Contributing

If you'd like to contribute to the Code Sniffer, you can fork the project add
features and send pull requests.

## Releasing CakePHP Code Sniffer

* Create a signed tag
* Write the changelog in the tag commit
