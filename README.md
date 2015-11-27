- [ ] Complete Documentation
- [ ] Acceptance Tests [?]
- [ ] Unit Tests
- [ ] Add artisan command
- [ ] Don't require order


# csvimport
CSV Import Tool

##Features
* Automatically extends admin layout (resources/views/layouts/admin.blade.php)  if present.

## Installation
### Install Package

``` bash
$ composer require rtmatt/csvimport

```

### Add Service Provider
In app/config.php, add the following to the providers list:

```  php

RTMatt\CSVImport\CSVImportServiceProvider::class,

```
### Routing

#### Option 1 - Automatic 
If you would like to leverage automatic routing, make sure to add the above before the application routing service provider:

``` php 
// [...]
 RTMatt\CSVImport\CSVImportServiceProvider::class,
  /*
  * Application Service Providers...
  */
 App\Providers\AppServiceProvider::class,
 // [...]

```

If your routes are cached, make sure to clear the route cache.


``` bash 

$ php artisan route:clear

```

#### Option 2 - Manual
In your routes.php file, manually define the route you would like to use (make sure it's a controller route):

``` php
 
// [...]
Route::controller([your route here],'\RTMatt\CSVImport\CSVImportController');
// [...]

```

## Basic Usage
### Set Up Importer
Add a new file to `app/CSVImports` with the name of [ResourceName]Importer.php.  
  
  ``` php 
  
  <?php
  
  namespace App\CSVImports;
  
  use RTMatt\CSVImport\CSVImporter;
  
  class YourResourceNameImporter extends CSVImporter {
  
      protected function setResourceName()
      {
          return "resource_names";
      }
 
      protected function setTableName()
      {
          return "resource_names";
      }
  

      protected function setFieldString()
      {
          return "name,image,website_link,description";
      }
  }
  
  ```
 * setResourceName - this is largely arbitraty and will likely be removed in future versions
 * setTableName - this needs to match the database table you will be importing into
 * setFieldString - comma separated list of the database columns that correspond to the columns in your csv file. 
 
### Run Imports
	* Navigate to the route of your importer (either /csv-imports  or the custom route you defined earlier);
	* Fill out the form and be done.
  
  
