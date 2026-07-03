<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tipo',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_confirmed_at',
        'last_login_at',
        'last_login_ip',
        'failed_attempts',
        'locked_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'two_factor_enabled' => 'boolean',
        'two_factor_confirmed_at' => 'datetime',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
    ];

    public function personalAccessTokens(){
        return $this->hasMany(PersonalAccessToken::class);
    }

    public function trustedDevices(){
        return $this->hasMany(TrustedDevice::class);
    }

    public function isDev(): bool{
        return $this->tipo === 'dev';
    }

    public function isAdmin(): bool{
        return $this->tipo === 'admin';
    }

    public function isVisualizador(): bool{
        return $this->tipo === 'visualizador';
    }

    public function isPagador(): bool{
        return $this->tipo === 'pagador';
    }

    public function isCobranca(): bool{
        return $this->tipo === 'cobranca';
    }

    public function needsTwoFactorSetup(): bool
    {
        return is_null($this->two_factor_secret) || is_null($this->two_factor_confirmed_at);
    }

    public function twoFactorSecret(): ?string
    {
        if (is_null($this->two_factor_secret)) {
            return null;
        }

        return \Illuminate\Support\Facades\Crypt::decryptString($this->two_factor_secret);
    }

    public function tipoLabel(): string
    {
        return match ($this->tipo) {
            'dev' => 'Desenvolvedor',
            'admin' => 'Administrador',
            'visualizador' => 'Visualizador',
            'pagador' => 'Pagador',
            'cobranca' => 'Cobrança',
            default => $this->tipo ?? '—',
        };
    }
}
