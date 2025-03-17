<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolicyDocument extends Model
{
    /** @use HasFactory<\Database\Factories\PolicyDocumentFactory> */
    use HasFactory;

    protected $guarded = [];

    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
