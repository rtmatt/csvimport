- [ ] Complete Documentation
- [ ] Acceptance Tests [?]
- [ ] Unit Tests
- [ ] Custom Route Config
- [ ] Add artisan command



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

### Directory Readable to MYSQL User
If the absolute path to the folder you have configured for your mysql user is differet than `/data`, change the "sql_directory" option to your folder (do not include trailing slashes).

### Authentication
If you would like to restrict access to non-authenticated users, change the "auth" option to "true".  You will need to add a public method "can_import" to your user model that contains your authentication logic.


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


### Run Imports
	* Navigate to the route of your importer (either /csv-imports  or the custom route you defined earlier);
	* Fill out the form and be done.
  
  ## Configuration
  You can configure the importer by modifying `config/csvimport.php`.  
  
  ``` php 
  
  <?php
  
  return [
      'import_order'=>[],
      'auth'=>false,
      'sql_directory'=>'/data'
  ];
  
  ```
  
  ### Import Order
  By default, CSVImporter will run multiple imports in a randomized order. 
  
  If your imports must be run in a certain order, you can configure the order in which they run. Simply add an array with keys that are the importer name in  snake_case with "Importer" removed, eg `AdminUsersImporter=>admin_users` and a value of the 0-based order in which it should run.
  
  For example, if you have a UserTypesImporter and a UsersImporter that need to run in that order, your config file will look like:
  
  ``` php 
  
  // ..
	import_order'=>[
		'user_types'=>0,
		'users'=>1
	],
  // ..
  ```
  
  ### Authentication
  If you would like to restrict access to the import area, simply change the auth config to `true`.  You will need to add a method  your User model containing your autentication logic.
  
  ``` php 
  
  public function can_import(){
	 // your authentication logic here
  }
  
  ```
  ### SQL Directory
  The CSVImporter moves uploaded CSV files to a directory the mysql user can read from.  By default, CSVImporter will try to use the directory "/data"
   You can easily change this to your preferred directory by modifying this config. 
  
  
  

  
  
  
  
## Advanced Usage
If you have a long list of imports to run and some depend on others, you can define the order in which they run. In `config/csvimport.php`, modify the import_order array with the your importer name in snake_case with "Importer" removed, eg `AdminUsersImporter=>admin_users`


  
