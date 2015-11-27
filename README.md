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


### Publish Vendor Directories - TODO - this is automated via composer install.  
In order for the package to work properly, you will need certain folders in your app directory.  This is automated by running the following:

``` bash 

$ php artisan vendor:publish --provider="RTMatt\CSVImport\CSVImportServiceProvider"

```

If you have previously done this and would like to overwrite previous files, add the --force switch:
``` bash 

$ php artisan vendor:publish  --provider="RTMatt\CSVImport\CSVImportServiceProvider" --force

```

## Usage

