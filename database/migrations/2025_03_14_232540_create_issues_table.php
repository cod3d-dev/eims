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
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('issue_type_id')->constrained();
            $table->text('description');
            $table->enum('status', ['to_review', 'processing', 'to_send', 'sent', 'resolved', 'no_solution'])->default('to_review');
            $table->text('proposed_solution')->nullable();
            $table->text('email_message')->nullable();
            $table->date('verification_date')->nullable();
            $table->text('response')->nullable();
            $table->json('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
