<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $fillable = ['key', 'value'];

    public function getValueAttribute($value)
    {
        if (in_array($this->key, [
            'allow_create_unassigned_task',
        ])) {
            return (int)$value === 1;
        }

        return $value;
    }
}
