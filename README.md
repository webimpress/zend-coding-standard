Zend Framework Coding Standard
==============================

> ## Repository abandoned 2019-12-31
>
> This repository has moved to laminas/laminas-coding-standard.

Repository with all coding standard ruleset for Zend Framework repositories.


Installation
------------

1. Install the module via composer by running:

   ```bash
   $ composer require --dev zendframework/zend-coding-standard
   ```

2. Add composer scripts into your `composer.json`:

   ```json
   "scripts": {
     "cs-check": "phpcs",
     "cs-fix": "phpcbf"
   }
   ```

3. Create file `phpcs.xml` on base path of your repository with content:

   ```xml
   <?xml version="1.0"?>
   <ruleset name="Zend Framework Coding Standard">
       <rule ref="./vendor/zendframework/zend-coding-standard/ruleset.xml"/>

       <!-- Paths to check -->
       <file>config</file>
       <file>src</file>
       <file>test</file>
   </ruleset>
   ```

You can add or exclude some locations in that file.
For a reference please see: https://github.com/squizlabs/PHP_CodeSniffer/wiki/Annotated-ruleset.xml


Usage
-----

* To run checks only:

  ```bash
  $ composer cs-check
  ```

* To automatically fix many CS issues:
 
  ```bash
  $ composer cs-fix
  ```