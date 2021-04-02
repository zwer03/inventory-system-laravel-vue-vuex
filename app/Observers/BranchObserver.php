<?php

namespace App\Observers;

use App\Models\Branch;

class BranchObserver
{
    public function creating(Branch $model)
    {
        $model->user_id = auth()->user()->id;
        $model->enabled = 1;
    }

    public function updating(Branch $model)
    {
        $model->user_id = auth()->user()->id;
    }
}
