<?php

namespace App\Observers;

use App\Models\Warehouse;

class WarehouseObserver
{
    public function creating(Warehouse $model)
    {
        $model->user_id = auth()->user()->id;
        $model->enabled = 1;
    }

    public function updating(Warehouse $model)
    {
        $model->user_id = auth()->user()->id;
    }
}
