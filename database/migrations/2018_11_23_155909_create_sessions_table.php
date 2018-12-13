<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')
                ->unsigned()
                ->index();
            $table->foreign('user_id')
                ->on('viber_users')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->integer('drug_id')
                ->unsigned()
                ->nullable()
                ->index();
            $table->foreign('drug_id')
                ->on('drugs')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->integer('last_message_id')->default(0);
            $table->integer('stage_num')->default(0);
            $table->timestamp('procedure_at')
                ->nullable()
                ->index();
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
        Schema::dropIfExists('sessions');
    }
}
