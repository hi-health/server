<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Parameter extends Model
{
    public $table = 'parameters';

    protected $fillable = [
        'type',
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'json',
    ];
    
    public function __get($key)
    {
        $value = parent::__get($key);
        if ($value) {
            return $value;
        }

        return array_get($this->value, $key);
    }
}
