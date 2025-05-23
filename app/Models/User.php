<?php

namespace App\Models;

use App\Enums\RolesEnum;
use App\Concerns\UserTrait;
use App\Events\RoleAssigned;
use Laravel\Sanctum\HasApiTokens;
use App\Events\PermissionAssigned;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles as SpatieHasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory;
    use HasProfilePhoto, Notifiable, TwoFactorAuthenticatable, SoftDeletes;
    use SpatieHasRoles {
        assignRole as originalAssignRole;
        givePermissionTo as originalGivePermissionTo;
    }
    use UserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invited_by',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
        'invited_by',
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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

    public function assignRole(...$roles)
    {
        $this->originalAssignRole(...$roles);

        foreach ($roles as $role) {
            event(new RoleAssigned($this, $role));
        }

        return $this;
    }

    public function givePermissionTo(...$permissions)
    {
        $this->originalGivePermissionTo(...$permissions);

        foreach ($permissions as $permission) {
            event(new PermissionAssigned($this, $permission));
        }

        return $this;
    }

    /**
     * Get the URL to the user's profile photo.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(function (): string {
            return $this->profile_photo_path
                ? url('storage/' . $this->profile_photo_path)
                : $this->defaultProfilePhotoUrl();
        });
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function defaultProfilePhotoUrl()
    {
        $name = trim(collect(explode(' ', $this->info->fullName() ?? $this->email))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return $this->hasRole('user') ? asset('/app_assets/images/avt/avt27.jpg') : 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=7F9CF5&background=EBF4FF';
    }

    public function account(): HasOne
    {
        return $this->hasOne(UserAccount::class);
    }

    public function asset(): HasMany
    {
        return $this->hasMany(UserAsset::class);
    }

    public function info(): HasOne
    {
        return $this->hasOne(UserInfo::class);
    }

    public function kyc(): HasOne
    {
        return $this->hasOne(KYC::class);
    }

    public function scopeSearch(Builder $query, $search)
    {
        $query->where('email', 'like', '%' . $search . '%')
            ->orWhereHas('info', function (Builder $query) use ($search) {
                $query->search($search);
            })
            ->orWhereHas('account', function (Builder $query) use ($search) {
                $query->search($search);
            });
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->search($search);
        })
            ->when($filters['sort'] ?? null, function ($query, $sort) {
                $query->orderBy($sort['field'], $sort['order']);
            })
            ->when($filters['trashed'] ?? null, function ($query, $trashed) {
                if ($trashed === 'with') {
                    $query->withTrashed();
                } elseif ($trashed === 'only') {
                    $query->onlyTrashed();
                }
            });
    }
}
