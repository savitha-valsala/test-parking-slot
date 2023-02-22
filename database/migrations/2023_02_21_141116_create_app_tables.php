<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() 
    {
        
        Schema::create('tb_parking_req_det', function (Blueprint $table) {
            $table->increments('id');
            $table->string('customer_name', 200);    
            $table->bigInteger('customer_mobile');    
            $table->string('vehicle_no', 15);  
            $table->dateTime('request_time');
            $table->dateTime('proposed_time_in');
            $table->dateTime('proposed_time_out');            
            $table->string('slot',10); 
            $table->string('ap_no',20); 
            $table->string('license');  
            $table->float('proposed_fee',8,2);  
            $table->smallInteger('iny_status');
            $table->dateTime('status_time');            
            $table->timestamps();
            
        });
        Schema::create('tb_parking_checkout', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('req_id');
            $table->dateTime('actual_time_in');
            $table->dateTime('actual_time_out');  
            $table->float('actual_fee',8,2);  
            $table->timestamps();
            $table->foreign('req_id')->references('id')->on('tb_parking_req_det');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_parking_checkout', function (Blueprint $table) {            
            $table->dropForeign(['req_id'])->references('id')->on('tb_parking_req_det')->onDelete('cascade'); 
          });
        Schema::dropIfExists('tb_parking_checkout');
        Schema::dropIfExists('tb_parking_req_det');
    }
}
