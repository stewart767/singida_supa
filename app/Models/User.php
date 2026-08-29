<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'ajira_linked',
        'password',
        'otp_code',
        'otp_expires_at',
        'is_active',
        'avatar',
        'is_locked',
        'password_force_change',
        'email_verified_at',
        'failed_login_attempts',
        'locked_until',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'ajira_linked' => 'boolean',
        ];
    }

    public function applicant(): HasOne
    {
        return $this->hasOne(Applicant::class);
    }

    public function careerProfile(): HasOne
    {
        return $this->hasOne(CareerProfile::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole(string|array $roles): bool
    {
        $currentRole = strtolower(str_replace([' ', '-'], '_', $this->role ?? ''));
        if (is_string($roles)) {
            $roles = [strtolower(str_replace([' ', '-'], '_', $roles))];
        } else {
            $roles = array_map(fn($r) => strtolower(str_replace([' ', '-'], '_', $r)), $roles);
        }

        return in_array($currentRole, $roles) || $this->roles->contains(function ($r) use ($roles) {
            return in_array(strtolower(str_replace([' ', '-'], '_', $r->name ?? '')), $roles);
        });
    }

    public function isSuperAdmin(): bool
    {
        $role = strtolower(str_replace([' ', '-'], '_', $this->role ?? ''));
        return in_array($role, ['super_admin', 'superadmin']);
    }

    public function isStaff(): bool
    {
        $role = strtolower(str_replace([' ', '-'], '_', $this->role ?? ''));
        return in_array($role, ['super_admin', 'superadmin', 'registrar', 'admission_officer', 'finance_officer']);
    }

    public function isApplicant(): bool
    {
        $role = strtolower(str_replace([' ', '-'], '_', $this->role ?? ''));
        return in_array($role, ['applicant', 'user']);
    }

    public function hasPermissionTo(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Direct check or role permission relations check
        $currentRole = strtolower(str_replace([' ', '-'], '_', $this->role ?? ''));
        if (in_array($currentRole, ['super_admin', 'superadmin'])) {
            return true;
        }

        return $this->roles()->whereHas('permissions', function ($q) use ($permission) {
            $q->where('name', $permission);
        })->exists();
    }

    public function jobApplications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function talentPools(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TalentPool::class);
    }
}
