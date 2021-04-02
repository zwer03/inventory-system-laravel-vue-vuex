<?php

namespace App\Observers;

use App\Models\Storage;

class StorageObserver
{
    public function creating(Storage $model)
    {
        $model->user_id = auth()->user()->id;
        $model->enabled = 1;
    }

    public function updating(Storage $model)
    {
        $model->user_id = auth()->user()->id;
    }
}
