<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('customers', 'is_member')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->boolean('is_member')->default(true);
            });
        }

        if (!Schema::hasColumn('customers', 'member_discount_percent')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->unsignedTinyInteger('member_discount_percent')->default(10);
            });
        }
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['is_member', 'member_discount_percent']);
        });
    }
};
