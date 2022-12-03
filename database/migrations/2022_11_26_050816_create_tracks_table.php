<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('album_id')->constrained()->cascadeOnDelete();
            $table->string('spotify_id');
            $table->string('name');
            $table->text('preview_url');
            $table->string('duration_ms');
            $table->integer('track_number');
            $table->string('type');
            $table->timestamp('mail_sended_at')->nullable();
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
        Schema::dropIfExists('tracks');
    }
};
