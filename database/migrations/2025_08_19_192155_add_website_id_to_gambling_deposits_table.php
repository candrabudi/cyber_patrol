<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('gambling_deposits', function (Blueprint $table) {
            $table->unsignedBigInteger('website_id')->nullable()->after('customer_id');
            $table->foreign('website_id')
                ->references('id')
                ->on('websites')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('gambling_deposits', function (Blueprint $table) {
            $table->dropForeign(['website_id']);
            $table->dropColumn('website_id');
        });
    }
};
