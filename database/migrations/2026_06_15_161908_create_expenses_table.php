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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_number')->unique();
            $table->foreignId('expense_category_id')->constrained('expense_categories');
            $table->foreignId('user_id')->constrained('users');
            $table->date('date');
            $table->decimal('amount', 15, 2);
            $table->text('note')->nullable();
            $table->string('proof_file_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
