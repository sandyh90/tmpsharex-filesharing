<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploadFilesData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upload_files_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('type_uploader', ['registered', 'anonymous']);
            $table->string('delete_password');
            $table->string('name_file');
            $table->string('filename');
            $table->string('file_size');
            $table->string('file_extension');
            $table->json('hash_file');
            $table->string('unique_id_file');
            $table->uuid('uuid_file')->unique();
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
        Schema::dropIfExists('upload_files_data');
    }
}
