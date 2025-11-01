<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeaderAnalysis extends Model
{
    protected $fillable = [
        'user_id',
        'url',
        'screenshot_url',
        'headers',
        'recommendations',
        'missing_security_features',
        'security_score',
    ];

    protected $casts = [
        'headers' => 'array',
        'recommendations' => 'array',
        'missing_security_features' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
