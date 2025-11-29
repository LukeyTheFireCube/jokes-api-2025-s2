<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'suspended',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function jokes(): HasMany
    {
        return $this->hasMany(Joke::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function primaryRole()
    {
        return $this->roles()->orderByDesc('level')->first();
    }

    public function hasPermission(string $permission): bool
    {
        // super-user bypass
        if ($this->role->name === 'super-user') {
            return true;
        }

        return $this->hasPermissionTo($permission);
    }

    public function canForceLogout(User $target): bool
    {
        if ($this->id === $target->id) {
            return true;
        }

        $actor = $this->primaryRole();
        $targetRole = $target->primaryRole();

        if (!$actor || !$targetRole) {
            return false;
        }

        return $actor->level > $targetRole->level;
    }
}
