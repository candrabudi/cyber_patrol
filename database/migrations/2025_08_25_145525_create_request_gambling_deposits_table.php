<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('request_gambling_deposits', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('website_id');
            $table->bigInteger('channel_id');
            $table->bigInteger('requested_by')->nullable();
            $table->bigInteger('to_user')->nullable();
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'process', 'completed', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_gambling_deposits');
    }
};
