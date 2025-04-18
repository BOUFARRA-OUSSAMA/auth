<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $table = 'attributes';

    protected $fillable = [
        'code',
        'name',
        'type',
        'entity_type_id',
        'is_required',
        'description',
    ];

    public function entityType()
    {
        return $this->belongsTo(EntityType::class, 'entity_type_id');
    }

    public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id');
    }
}
