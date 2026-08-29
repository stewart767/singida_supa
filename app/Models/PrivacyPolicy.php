<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrivacyPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'version',
        'title',
        'content',
        'file_path',
        'effective_date',
        'status',
        'published_by',
        'language',
    ];

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function consents(): HasMany
    {
        return $this->hasMany(ApplicationConsent::class);
    }
}
