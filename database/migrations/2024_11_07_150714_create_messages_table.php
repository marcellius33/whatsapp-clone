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
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('chat_room_id');
            $table->uuid('sender_id');
            $table->text('content');
            $table->text('attachment_url')->nullable();
            $table->timestampTz('sent_at', 6);
            $table->boolean('read');

            $table->foreign('chat_room_id')->references('id')->on('chat_rooms');
            $table->foreign('sender_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
