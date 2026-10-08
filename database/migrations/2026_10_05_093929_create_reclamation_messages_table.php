<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclamation_messages', function (Blueprint $table) {
            $table->id('id_message');

            $table->unsignedBigInteger('id_reclamation');
            $table->unsignedBigInteger('id_auteur');

            $table->text('contenu');

            $table->timestamps();

            $table->foreign('id_reclamation')
                ->references('id_reclamation')
                ->on('reclamations');

            $table->foreign('id_auteur')
                ->references('id_user')
                ->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclamation_messages');
    }
};