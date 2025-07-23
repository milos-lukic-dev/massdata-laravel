<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

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
     * Get users
     *
     * @return mixed
     */
    public function getUsers()
    {
        return $this->orderBy('id', 'DESC')->paginate(30);
    }

    /**
     * Get user by ID
     *
     * @param $userId
     * @return mixed
     */
    public function getUser($userId)
    {
        return $this->findOrFail($userId);
    }

    /**
     * Get users by email
     *
     * @param $term
     * @return mixed
     */
    public function getUsersByEmail($term)
    {
        return $this->select('id', 'email')
            ->where('email', 'like',  '%' . addcslashes($term, '%_') . '%')
            ->get();
    }
}
