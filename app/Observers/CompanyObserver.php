<?php

namespace App\Observers;

use App\Models\Company;

class CompanyObserver
{
    public function creating(Company $model)
    {
        $model->user_id = auth()->user()->id;
        $model->enabled = 1;
    }

    public function updating(Company $model)
    {
        $model->user_id = auth()->user()->id;
    }
}
