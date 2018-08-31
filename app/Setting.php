<?php

namespace App;

class Setting extends Parameter
{
    public function getValue($name, $default = null)
    {
        if (is_string($this->attributes['value'])) {
            $this->attributes['value'] = json_decode($this->attributes['value'], true);
        }

        return array_get($this->attributes['value'], $name, $default);
    }

    public function setValue($value)
    {
        if (!is_array($this->attributes['value'])) {
            $this->attributes['value'] = json_decode($this->attributes['value'], true);
        }
        $this->attributes['value'] = json_encode(
            array_set(
                $this->attributes['value'], 'value', $value
            )
        );

        return $this;
    }
}
