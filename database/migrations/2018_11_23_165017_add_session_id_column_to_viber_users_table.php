<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSessionIdColumnToViberUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('viber_users', function (Blueprint $table) {
            $table->integer('session_id')
                ->unsigned()
                ->nullable()
                ->index();
            $table->foreign('session_id')
                ->on('sessions')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->integer('completed_session_id')
                ->unsigned()
                ->nullable()
                ->index();
            $table->foreign('completed_session_id')
                ->on('sessions')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('viber_users', function (Blueprint $table) {
                        $table->dropForeign('viber_users_session_id_foreign');
                        $table->dropIndex('viber_users_session_id_index');
                        $table->dropColumn('session_id');
                        $table->dropForeign('viber_users_completed_session_id_foreign');
                        $table->dropIndex('viber_users_completed_session_id_index');
                        $table->dropColumn('completed_session_id');
        });
    }
}
