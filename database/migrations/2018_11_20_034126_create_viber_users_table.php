<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViberUsersTable extends
Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viber_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('viber_id', 50)->unique();
                        $table->string('name');
            $table->boolean('subscribed')->default(false);
                        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('viber_users');
    }
}
