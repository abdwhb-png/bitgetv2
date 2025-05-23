<?php

namespace App\Concerns;

trait FileTrait
{
    public function uploadFile($file, $path)
    {
        $file->storeAs($path, $file->hashName());
    }
}
