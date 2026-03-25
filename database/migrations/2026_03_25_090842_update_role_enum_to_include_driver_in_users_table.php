<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Altering the ENUM column to include 'driver'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'customer', 'driver') DEFAULT 'customer'");
    }
    public function down(): void
    {
        // Reverting back if needed
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'customer') DEFAULT 'customer'");
    }
};
