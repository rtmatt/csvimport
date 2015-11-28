<?php
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tests', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('type');
            $table->string('time');
            $table->string('name_time');

        });
        $now = Carbon::now();
        DB::table('tests')->insert([
            'name'      => 'Migration Testing',
            'type'   => 'integration',
            'time' => '666'
        ]);
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tests');
    }
}