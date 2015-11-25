<?php


Route::controller('csv-import','\RTMatt\CSVImport\CSVImportController');

Route::get('',function(){
	abort(404);
});
