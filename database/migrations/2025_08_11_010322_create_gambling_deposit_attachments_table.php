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
        Schema::create('gambling_deposit_attachments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('gambling_deposit_id')->constrained('gambling_deposits')->onDelete('cascade');

            $table->enum('attachment_type', ['website_proof', 'account_proof', 'qris_proof']);

            $table->string('file_path');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gambling_deposit_attachments');
    }
};
