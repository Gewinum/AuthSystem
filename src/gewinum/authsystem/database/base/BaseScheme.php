<?php

namespace gewinum\authsystem\database\base;

abstract class BaseScheme
{
    public function exportFromArray(array $data): void
    {
        foreach ($data as $key => $value) {
            $key = lcfirst($key);
            $this->$key = $value;
        }
    }
}