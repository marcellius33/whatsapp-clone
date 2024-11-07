<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chat_room_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('chat_room_id');
            $table->uuid('user_id');
            $table->string('role');
            $table->timestampTz('joined_at', 6);

            $table->foreign('chat_room_id')->references('id')->on('chat_rooms');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_room_members');
    }
};
