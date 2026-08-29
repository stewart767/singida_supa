<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalApplicationRedirect extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vacancy_id',
        'provider',
        'tracking_token',
        'destination_url',
        'ip_address',
        'user_agent',
        'redirected_at',
    ];

    protected function casts(): array
    {
        return [
            'redirected_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }
}
