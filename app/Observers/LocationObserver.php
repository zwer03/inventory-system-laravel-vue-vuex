<?php

namespace App\Observers;

use App\Models\Location;

class LocationObserver
{
    public function creating(Location $model)
    {
        $model->user_id = auth()->user()->id;
        $model->enabled = 1;
    }

    public function updating(Location $model)
    {
        $model->user_id = auth()->user()->id;
    }
}
