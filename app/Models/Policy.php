<?php

namespace App\Models;

use App\Casts\ApplicantCast;
use App\Casts\ApplicantCollectionCast;
use App\Enums\DocumentStatus;
use App\Enums\PolicyStatus;
use App\Enums\RenewalStatus;
use App\Enums\UsState;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Policy extends Model
{
    use HasFactory;

    protected $guarded = [];

    // protected $hidden = [
    //     'payment_card_number',
    //     'payment_card_cvv',
    //     'payment_bank_account_number',
    // ];

    protected $casts = [
        'main_applicant' => ApplicantCast::class,
        'additional_applicants' => ApplicantCollectionCast::class,
        // 'family_members' => 'array',
        'prescription_drugs' => 'array',
        'life_insurance' => 'json',
        'contact_information' => 'array',
        'premium_amount' => 'decimal:2',
        'coverage_amount' => 'decimal:2',
        'estimated_household_income' => 'decimal:2',
        'policy_total_cost' => 'decimal:2',
        'policy_total_subsidy' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'valid_until' => 'date',
        'total_family_members' => 'integer',
        'total_applicants' => 'integer',
        'recurring_payment' => 'boolean',
        'initial_paid' => 'boolean',
        'autopay' => 'boolean',
        'aca' => 'boolean',
        'premium' => 'decimal:2',
        'is_renewal' => 'boolean',
        'renewed_at' => 'datetime',
        'renewal_status' => RenewalStatus::class,
        'status' => PolicyStatus::class,
        'document_status'   => DocumentStatus::class,
        'policy_us_state' => UsState::class,
        'requires_aca' => 'boolean',
    ];

    protected function casts(): array
    {
        return [
            'payment_card_number' => 'encrypted',
            'payment_card_cvv' => 'encrypted',
            'payment_card_holder' => 'encrypted',
            'payment_bank_account_number' => 'encrypted',
            'payment_bank_account_holder' => 'encrypted',
            'billing_address_1' => 'encrypted',
            'billing_address_2' => 'encrypted',
            'payment_card_exp_month' => 'encrypted',
            'payment_card_exp_year' => 'encrypted',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($policy) {
            Log::info('Saving policy...', [
                'id' => $policy->id,
                'attributes' => $policy->getDirty(),
            ]);
        });

        static::saved(function ($policy) {
            Log::info('Policy saved successfully', [
                'id' => $policy->id,
                'changes' => $policy->getChanges(),
            ]);
        });
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PolicyDocument::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function insuranceCompany(): BelongsTo
    {
        return $this->belongsTo(InsuranceCompany::class);
    }

    public function policyType(): BelongsTo
    {
        return $this->belongsTo(PolicyType::class);
    }

    public function initialVerificationPerformedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initial_verification_performed_by');
    }

    public function previousYearPolicyUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'previous_year_policy_user_id');
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    // Renewal relationships
    public function renewedFromPolicy(): BelongsTo
    {
        return $this->belongsTo(Policy::class, 'renewed_from_policy_id');
    }

    public function renewedToPolicy(): BelongsTo
    {
        return $this->belongsTo(Policy::class, 'renewed_to_policy_id');
    }

    public function renewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'renewed_by');
    }

    // Helper methods for renewal
    public function isRenewable(): bool
    {
        return !$this->renewed_to_policy_id &&
            $this->end_date &&
            $this->end_date->isFuture() &&
            $this->end_date->subMonths(3)->isPast();
    }

    public function getRenewalPeriod(): array
    {
        $startDate = $this->end_date?->addDay();
        return [
            'start_date' => $startDate,
            'end_date' => $startDate?->addYear()->subDay(),
        ];
    }

    // Helper methods for accessing applicants
    public function mainApplicant()
    {
        if (!isset($this->applicants) || !is_array($this->applicants)) {
            return null;
        }

        foreach ($this->applicants as $applicant) {
            if (isset($applicant['is_main']) && $applicant['is_main']) {
                return $applicant;
            }
        }

        return !empty($this->applicants) ? $this->applicants[0] : null;
    }

    public function additionalApplicants()
    {
        if (!isset($this->applicants) || !is_array($this->applicants)) {
            return [];
        }

        return array_filter($this->applicants, function ($applicant) {
            return !isset($applicant['is_main']) || !$applicant['is_main'];
        });
    }



}
