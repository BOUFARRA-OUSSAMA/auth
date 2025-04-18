<?php

namespace App\Infrastructure\Persistence\Models;

// Extend the existing User model to maintain JWT functionality
use App\Models\User as BaseUser;

class User extends BaseUser
{
    // Additional fillable fields beyond what the base User has
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'status',
    ];

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'user_roles',
            'user_id',
            'role_id'
        );
    }

    public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class, 'entity_id')
            ->where('entity_type', 'user');
    }
}
