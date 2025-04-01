<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Namu\WireChat\Traits\Chatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Chatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

    /**
     * Returns the URL for the user's cover image (avatar).
     */
    public function getCoverUrlAttribute(): ?string
    {
        return $this->avatar_url ?? null;
    }

    /**
     * Returns the URL for the user's profile page.
     */
    public function getProfileUrlAttribute(): ?string
    {
        return route('profile', ['id' => $this->id]);
    }

    /**
     * Returns the display name for the user.
     */
    public function getDisplayNameAttribute(): ?string
    {
        return $this->name ?? 'user';
    }

    /**
     * Search for users when creating a new chat or adding members to a group.
     */
    public function searchChatables(string $query): ?Collection
    {
        $searchableFields = ['name'];
        return User::where(function ($queryBuilder) use ($searchableFields, $query) {
            foreach ($searchableFields as $field) {
                $queryBuilder->orWhere($field, 'LIKE', '%' . $query . '%');
            }
        })
            ->limit(20)
            ->get();
    }

    /**
     * Determine if the user can create new groups.
     */
    public function canCreateGroups(): bool
    {
        return $this->hasVerifiedEmail();
    }

    /**
     * Determine if the user can create new chats.
     */
    public function canCreateChats(): bool
    {
        return $this->hasVerifiedEmail();
    }

}
