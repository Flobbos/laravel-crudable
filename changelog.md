## Version History

### v. 3.0.21

-   removed support for Laravel 8 in favor of new version
-   moved Laravel 8 support to version 4.x

### v. 3.0.20

-   fixed function naming collision

### v. 3.0.19

-   added automated slug processing to non-translated data

### v. 3.0.18

-   fixed PSR-4 typo in exceptions

### v. 3.0.17

-   replaced is_null with empty in Translatable to account for empty arrays
-   added support for Laravel 8.x

### v. 3.0.16

-   added Slugify as a dependency for locale based slug creation

### v. 3.0.15

-   fixed camel case conversion in slugger

### v. 3.0.14

-   finished Slugger integration for easy slug based resource retrieval
-   Removed unnecessary code

### v. 3.0.13

-   updateTranslation can now be used for any type of translation
-   updated php docs formatting and parameters
-   transition to PSR-2 started

### v. 3.0.12

-   added slugs and translated slugs
-   fixed issues from previous release
-   removed stray contract function declaration
-   updated stubs for better Bootstrap 4 output
-   removed unnecessary config settings
-   updated documentation with new info on handling slugs

### v. 3.0.11

-   added support for Laravel 7

### v. 3.0.10

-   fixed wrong variable name in Translatable

### v. 3.0.9

-   removed stray function from Translation contract

### v. 3.0.8

-   removed dependency for Laravel helpers
-   added slug contract and functionality
-   set auto binding to true in config
-   fixed required translations evaluation bug

### v. 3.0.7

-   added laravel helpers dependency

### v. 3.0.6

-   added support for Laravel 6
-   switched upload randomizer to the end of the original filename

### v. 3.0.5

-   removed obsolete exceptions

### v. 3.0.4

-   another attempt at fixing auto discover

### v. 3.0.3

-   notifications partial is now part of the package

### v. 3.0.2

-   fixed auto discovery again

### v. 3.0.1

-   fixed missing auto discover in composer.json

### v. 3.0.0

-   switched to Bootstrap 4

### v. 2.5.14

-   added first() to Crudable
-   updated readme

### v. 2.5.13

-   fixed typo in index.stub (edit instead of destroy)
-   fixed typo in edit view
-   changed translation handler to allow deletion of translations when empty data is sent.

### v. 2.5.12

-   removed fix for L5.3 because it didn't work
-   added fix for deleting content of existing translation fields

### v. 2.5.11

-   fixed missing ::class attribute in example config

### v. 2.5.10

-   fixed typo/pluralisation in custom contract controller stub

### v. 2.5.9

-   fixed typos
-   fixed pluralisation

### v. 2.5.8

-   option for including custom contract
-   option for generating model translation
-   updated index view with delete button in button group
-   changed view path/route path to be kebab case

### v. 2.5.7

-   fixed typo in delete method

### v. 2.5.6

-   fixed a problem with form request arrays containing null entries

### v. 2.5.5

-   updated delete method to handle soft deletes
-   added 'restore()' method for soft deleted models

### v. 2.5.4

-   added with() function as synonym for setRelation() accepts string|array
-   added null check in translation handler

### v. 2.5.3

-   added null check for file uploads

### v. 2.5.2

-   removed old code that created problems with translations

### v. 2.5.1

-   fixed relationship validation

### v. 2.5.0

-   added trait for handling translations
-   added basic custom exceptions
-   updated crudable trait for new exception
-   added new contract for translations
-   updated documentation

### v. 2.4.4

-   added a new command for generating contracts
-   updated documentation

### v. 2.4.3

-   added a new command for generating views

### v. 2.4

-   added fixed bindings via configuration

### v. 2.3

-   added generator commands

### v. 2.2

-   a few fixes

### v. 2.0

-   new and improved code base
-   added new features like where statements
-   ordering implemented

### v. 1.2

-   added orderBy method to include in your query

### v. 1.1

-   fixed major version release

### v. 0.7.0

-   added custom contracts in auto binding
-   extended language file
-   removed clutter
-   updated readme file

### v. 0.6

-   added standard translation file
-   fixed udpate to update method

### v. 0.5

-   lowered minimum requirements for Laravel

### v. 0.41

-   fixed update function

### v. 0.4

-   added automated binding feature
-   fixed issues with automated binding

### v. 0.32

-   fixed issue with config

### v. 0.31

-   added contract
-   added config file
-   updated service provider

### v. 0.3

-   added pagination feature

### v. 0.2

-   added features
-   fixed major bugs

### v. 0.1

-   initial release
