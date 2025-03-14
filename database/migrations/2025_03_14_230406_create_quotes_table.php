<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\QuoteStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('policy_id')->nullable();
            $table->foreignId('insurance_company_id')->nullable();
            $table->foreignId('insurance_account_id')->nullable();
            $table->foreignId('policy_type_id')->constrained();
            $table->decimal('premium_amount', 10, 2)->nullable();
            $table->decimal('coverage_amount', 12, 2)->nullable();
            $table->integer('year')->nullable();

            // Applicants Information
            $table->json('main_applicant')->nullable(); // Will store the main applicant
            $table->json('additional_applicants')->nullable(); // Will store additional applicants
            $table->integer('total_family_members')->default(1);
            $table->integer('total_applicants')->default(1);

            // Additional Information
            $table->decimal('estimated_household_income', 12, 2)->nullable();
            $table->string('preferred_doctor')->nullable();
            $table->json('prescription_drugs')->nullable(); // Will store medications info

            // Quote Status and Dates
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('valid_until')->nullable();
            $table->string('status')->default(QuoteStatus::Pending);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
