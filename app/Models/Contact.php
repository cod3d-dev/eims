<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\Gender;
use App\Enums\MaritialStatus;

class Contact extends Model
{
    /** @use HasFactory<\Database\Factories\ContactFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'gender' => Gender::class,
        'marital_status' => MaritialStatus::class,
        'is_lead' => 'boolean',
        'is_eligible_for_coverage' => 'boolean',
        'is_tobacco_user' => 'boolean',
        'is_pregnant' => 'boolean',
        'last_contact_date' => 'datetime',
        'next_follow_up_date' => 'datetime',
        'preferred_contact_time' => 'datetime',
        'ssn_issue_date' => 'date',
        'green_card_expiration_date' => 'date',
        'work_permit_expiration_date' => 'date',
        'driver_license_expiration_date' => 'date',
        'weight' => 'decimal:2',
        'height' => 'decimal:2',
        'annual_income_1' => 'decimal:2',
        'annual_income_2' => 'decimal:2',
        'annual_income_3' => 'decimal:2',
    ];

    public function getFullNameAttribute(): string
    {
        $names = [$this->first_name];

        if ($this->middle_name) {
            $names[] = $this->middle_name;
        }

        $names[] = $this->last_name;

        if ($this->second_last_name) {
            $names[] = $this->second_last_name;
        }

        return implode(' ', $names);
    }

    public function getFullAddressLinesAttribute(): string
    {
        $address = [];

        if ($this->address_line_1) {
            $address[] = $this->address_line_1;
        }

        if ($this->address_line_2) {
            $address[] = $this->address_line_2;
        }

        return implode(', ', $address);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ContactNote::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ContactDocument::class);
    }
}
