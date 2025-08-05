<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;



/**
 * @method bool hasRole(string|array $roles)
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes, HasApiTokens;

    

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'deactivation_reason',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
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

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function cancelledInvoices()
    {
        return $this->hasMany(Invoice::class, 'cancelled_by');
    }

    public function createToken(string $name, array $abilities = ['*'], bool $storePlain = false)
    {
        $plainText = Str::random(40);

        $token = $this->tokens()->create([
            'name'        => $name,
            'token'       => hash('sha256', $plainText),
            'plain_token' => $storePlain ? $plainText : null,
            'abilities'   => $abilities,
        ]);

        return new NewAccessToken($token, $token->id . '|' . $plainText);
    }
}
