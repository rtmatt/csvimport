- [ ] Complete Documentation
- [ ] Acceptance Tests [?]
- [ ] Unit Tests
- [ ] Custom Route Config
- [ ] Add artisan command
- [ ] Refine auth check to handle instance when user is not logged in at all
- [ ] 



# csvimport
CSV Import Tool

##Features
* Imports single or multiple .csv files into mysql database
* Optional User Authorization
* Ready to Roll Out of the Box
* Configurable Views, URLs and Directories
* Ability to Define Import Order 

## Prerequisites
* Laravel 5 project
* MYSQL database
* Folder on your system readable by mysql user (this can be configured, but "/data/" is used by default).
* Existing database table(s) with defined schema.
 
## Installation
### Install Package

``` bash
$ composer require rtmatt/csvimport

```

### Add Service Provider
In app/config.php, add the following to the providers list ABOVE the application Route Service Provider

```  php
//    [...]
RTMatt\CSVImport\CSVImportServiceProvider::class,
//    [...]
App\Providers\RouteServiceProvider::class,

```

If your routes are cached, make sure to clear the route cache.


``` bash 

$ php artisan route:clear

```

### Publish Dependencies

``` bash 

$ php artisan vendor:publish --provider="RTMatt\CSVImport\CSVImportServiceProvider"

```

## Usage
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
 * setResourceName - this defines field labels and messages created by the package
 * setTableName - this needs to match the database table you will be importing into
 * setFieldString - comma separated list of the database columns that correspond to the columns in your csv file. 

### Prepare CSV
CSVImport ignores the first two lines of a .csv file.  Ensure the first two lines of your csv do not contain any information to import. 

### Run Imports
Navigate to 'csv-import' in your browser.
  
Upload your csv in the appropriate input and click submit.



## Configuration
When you publish the package dependencies, a file will be created at `config/csvimport.php`
You can configure various options by changing this file.

``` php 

return [
    'import_order'=>[],
    'auth'=>false,
    'sql_directory'=>'/data',
    'override_layout_view'=> false,
    'importer_namespace'=>"\\App\\CSVImports\\",
    'importer_directory'=>app_path('CSVImports')
];

```

### Import Order
Sometimes you have inter dependencies between the tables you import content into and need the imports to run in a specified order.  
To accomplish this, simply add an array with keys that are the importer name in  snake_case with "Importer" removed, eg `AdminUsersImporter=>admin_users` and a value of the 0-based order in which it should run.
                                                                                                                                      
For example, if you have a UserTypesImporter and a UsersImporter that need to run in that order, your config file will look like:

``` php 

import_order'=>[
	'user_types'=>0,
	'users'=>1
],

```

### Authentication
If you would like to restrict access to the import area, simply change the auth config to `true`.  You will need to add a method  your User model containing your autentication logic.

``` php 

public function can_import(){
 // your authentication logic here
}

```
  
  
### Directory Readable to MYSQL User
If the absolute path to the folder you have configured for your mysql user is different than `/data`, change the "sql_directory" option to your folder (do not include trailing slashes).

### Layout Overrides
If you have a master layout you would like the importer to extend, you can change the `override_layout_view` config with a string in the same manner as you would load a view.  
For example, if the layout you would like to extend exists in `[..]/resources/views/layouts/admin.blade.php` you would set the config as follows:


``` php 

'override_layout_view'=> 'layouts.admin,

```

### Importer Directory
When you run `php artisan vendor:publish`, a directory called CSVImports is created in the app_path for your importers.  If you would like keep your importers elsewhere, add  the directory and its namespace to this configs.

``` php 

 'importer_namespace'=>"\\New\\Namespace\\",
 'importer_directory'=>base_path('Some/Other/Directory/CSVImports')

```

### Routing
If you would like the import tool to be accessible at a route different from the default, you can manually define a controller route in your application routes file

``` php
 
// [...]
Route::controller([your route here],'\RTMatt\CSVImport\CSVImportController');
// [...]

```