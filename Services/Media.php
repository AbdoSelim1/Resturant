<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class Media
{
    public static function delete(Model $model, string $collection)
    {
        $media = $model->getMedia($collection);
        foreach ($media as $index => $item) {
            $item->delete();
        }
        return 0;
    }
}
