<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id('idreviews');
            $table->text('review_text');
            $table->unsignedInteger('Users_idUser');
            $table->timestamps();
            $table->foreign('Users_idUser')->references('idUser')->on('users')->onDelete('cascade');
            $table->index('Users_idUser');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};