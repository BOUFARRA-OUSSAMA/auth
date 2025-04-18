<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class EntityType extends Model
{
    protected $table = 'entity_types';

    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    public function attributes()
    {
        return $this->hasMany(Attribute::class, 'entity_type_id');
    }
}
