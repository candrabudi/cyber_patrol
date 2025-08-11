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
        Schema::create('gambling_deposit_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('gambling_deposit_id')
                ->constrained('gambling_deposits')
                ->onDelete('cascade');

            // Info akun pembayaran
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('channel_name')->nullable();
            $table->string('channel_code')->nullable()->unique();
            $table->enum('channel_type', ['bank', 'ewallet', 'qris', 'virtual_account', 'pulsa']);

            // Flagging non-member
            $table->boolean('is_non_member')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gambling_deposit_accounts');
    }
};
