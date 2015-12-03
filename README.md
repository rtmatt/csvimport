# CSVImport for Laravel 5
[![Build Status](https://travis-ci.org/rtmatt/csvimport.svg?branch=master)](https://travis-ci.org/rtmatt/csvimport) 
[![Latest Stable Version](https://poser.pugx.org/rtmatt/csvimport/v/stable)](https://packagist.org/packages/rtmatt/csvimport) [![Total Downloads](https://poser.pugx.org/rtmatt/csvimport/downloads)](https://packagist.org/packages/rtmatt/csvimport) [![Latest Unstable Version](https://poser.pugx.org/rtmatt/csvimport/v/unstable)](https://packagist.org/packages/rtmatt/csvimport) [![License](https://poser.pugx.org/rtmatt/csvimport/license)](https://packagist.org/packages/rtmatt/csvimport)
<!---
## ISSUES
- [ ] Currenty if there are errors in postSqlUpdate method, they are not handled.]
-->
Speed up the process of importing initial client information into a MySQL Database-driven CMS.  This package contains everything you need to start importing CSVs into your database out of the box.  It is configurable and extendable to allow for complex import logic.  

## Requirements
- php >=5.5
- Laravel 5.1 Application
- MySQL database
- directory that is writable by the server and readable by mysql user.  

## Installation
Install the package

``` bash
$ composer require rtmatt/csvimport
```

Add the service provider in `config/app.php` BEFORE the application route service provider
 
```  php
//...
RTMatt\CSVImport\Providers\CSVImportServiceProvider::class,
//...
App\Providers\RouteServiceProvider::class,
```
 
Publish the package provider
 
``` bash
$ php artisan vendor:publish --provider="RTMatt\CSVImport\Providers\CSVImportServiceProvider"
```
 
## Usage
### Basic Usage Example
#### Background
You have a database table `users` with `first_name`, `last_name`, and `email` fields.  You have a csv file that reflects the following spreadsheet:

![alt tag](https://raw.github.com/rtmatt/csvimport/master/examplecsv.png)


#### Step 1 - Create Importer

Create an importer.  The following will create the importer stub  `app/CSVImports/UsersImporter.php`
``` bash
$ php artisan csvimport:make Users
```
#### Step 2 - Configure Importer
Configure importer stub.  You will need to define the following methods in your stub.
  
  
``` php
<?php

namespace App\CSVImports;

use RTMatt\CSVImport\CSVImportImporter;

class UsersImporter extends CSVImportImporter {
	protected function setResourceName()
	{
		return "users"; //this string defines the name for files generated by the importer.
	}

	protected function setTableName()
	{
		return "users"; //this need to be the name of the table the importer imports into
	}
	
	
	protected function setFieldString()
	{
		return "first_name,last_name,email"; //the corresponding database fields for each column in the csv
	}
}
```

#### Step 3 - Upload Importer and Run
Navigate to `/csv-imports`.  You should see an input that corresponds with the importer you made. Upload your csv and click submit. 

#### Step 4 - Have a drink
You earned it.

### Configuration options
During installation, the package will create a config file `config/csvimport.php` with the following options

``` php
  	'import_order'=>[],  //define order in which importers run
	'auth'=>false, // require authentication
	'sql_directory'=>'/data', // directory that csv files are written to and read from
	'override_layout_view'=> false, // override layout with custom view
	'importer_directory'=>app_path('CSVImports'), // directory in which new importers are saved
	'importer_namespace'=>"\\App\\CSVImports\\", // namespace of importers for creation and running
	'custom_route'=>false //custom route for Import manager
```

#### Import Order
If you have importers that need to run in a certain order, set this configuration option.

For example,  you have the following in your importers directory:

```
AffiliatesImporter.php
UsersImporter.php
UserPhotos.php
```

If you need the UserPhotosImporter to run before the AffiliatesImporter, you would define the `import_order` config as follows:

``` php
	'import_order'=>[
		'user_photos'=>0
		'affiliates'=>1
	],
```

- 	key is snake_case name of your importer with 'Importer' removed
-	value is ascending order of operation
-	importers that don't require ordering will run after ordered importers.

#### Authentication
If you want to require authentication to access the Importers, set the `auth` config to `true`

In order to use authentication, you will need to add the following to your User model:

``` php
	public function can_import()
    {
        // your permission logic here
        // return true|false
    }
```

- When an unauthenticated user tries to access the import area, a Unauthorized HttpException will be thrown.
- When a authenticated user for which the can_import method returns false attempts access, a Forbidden HttpException will be thrown.

#### SQL Directory
You can configure the directory to which the importer writes and reads files from.  This directory needs to be writable by the server and readable by the mysql user.  If you override this, use an absolute path.


``` php
'sql_directory'=>'/home/users/jondoe/sql_store',
```

#### Override Views
If you have an admin layout that you would like the importer to load within, simply change the `override_layout_view` config to a string representing how you would load the view in a controller:


``` php
'override_layout_view'=> 'layouts.admin'
```

The layout will load through the package's controller. If you have layout variables or logic that are not handled properly, you will likely encounter errors.

#### Override Importer Directory
If you would like to store your importers anywhere other than `app/CSVImports`, you can do so by changing the `importer_directory` config.

Make sure you also update the `importer_namespace` config to ensure proper Importer Loading.

``` php
    'importer_directory'=>app_path('IWantMyImportersHereForReasons'),
   'importer_namespace'=>"\\App\\IWantMyImportersHereForReasons\\",
```

#### Custom Routes
If you don't like the route /csv-imports and would like to define your own routes, you can do so by modifying your app's `routes.php` file with a controller route using the package's controller.
 
 
 ``` php
 Route::controller([your preferred route here],'\RTMatt\CSVImport\CSVImportController');
 ```
You should also set the `custom_route` config to  `true` to ensure the default package routes are not registered.  
 

### Advanced Usage
There are some predefined points at which you can extend the functionality of your importers. 
#### Override Import Command
You can completely override the MySQL command run by the importer by defining this method:

``` php
	protected function overrideImportCommand()
   {
		$statement = //Your sql statement 
	   return $statement;
   }
```

Example: 
You are importing properties into your database.  The CSV you are working with has the following issues:
- Name column sometimes has carriage returns in it ('\r')
- The transaction_date field has a date data type, but the CSV contains a date string
- The column Property Type corresponds to a sub resource.  You need to get the id of a row in the table property_types that matches your property type name.
- The value data type is an integer, but the CSV contains '$xxx,xxx,xxx' string values. 

``` php
	protected function overrideImportCommand()
    {
        return "load data infile '/data/properties.csv'  into table properties
			   FIELDS TERMINATED BY ','
			   optionally ENCLOSED BY '\"'
			   ESCAPED BY '\"'
			   LINES TERMINATED BY '\n'
			   IGNORE 2 LINES
			   (@name,city,state,@transaction_date,@property_type,description,proposition,solution,@value)
			   set
			   transaction_date = STR_TO_DATE(@transaction_date,'%c/%e/%Y'),
			   property_type_id = (Select id from property_types where name = Replace(@property_type,'\r','')),
			   value = cast((Replace(Replace(@value,'$',''),',','')) as unsigned),
			   name=Replace(@name,'\r','')
			   ;";
    }
```

#### Post-Import Logic
Sometimes you need to run some logic after the sql statement has been executed. You do this by adding the following method to your importer:
``` php
protected function postSQLImport()
    {
        // post import logic here
    }
```

Example: 
- You have a Users.csv which contains first_name and last_name columns.  
- You have a full_name column in your database, and would like it to be auto-populated with the imported users' first and last names.

``` php
protected function postSQLImport()
    {
        $users = \App\User::all();
        foreach($users as $user){
        	$user->full_name = $user->first_name.' '.$user->last_name;
		 	$user->save;
        }
    }
```

## Troubleshooting
### Routing Doesn't Work

You may have cached routes.  Clear the route cache by running

``` bash
$ php artisan route:clear
```

## Photos
Currently, you need to write your own photo import logic and call it from the `postSQLImport` method in your importer.  Photo import support is scheduled for future versions.