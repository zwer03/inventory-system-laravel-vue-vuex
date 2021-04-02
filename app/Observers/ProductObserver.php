<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    public function creating(Product $model)
    {
        $model->user_id = auth()->user()->id;
        $model->enabled = 1;
    }

    public function updating(Product $model)
    {
        $model->user_id = auth()->user()->id;
    }
}
