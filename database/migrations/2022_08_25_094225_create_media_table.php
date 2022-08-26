<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('media_id')->nullable()->references('id')->on('media');
        });

        Schema::table('authors', function (Blueprint $table) {
            $table->foreignId('media_id')->nullable()->references('id')->on('media');
        });

        Schema::table('books', function (Blueprint $table) {
            $table->foreignId('media_id')->nullable()->references('id')->on('media');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('media_id');
        });
        Schema::table('authors', function (Blueprint $table) {
            $table->dropColumn('media_id');
        });
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('media_id');
        });
    }
}
