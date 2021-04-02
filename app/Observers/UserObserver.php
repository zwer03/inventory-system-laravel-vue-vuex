<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function creating(User $model)
    {
        $model->enabled = 1;
    }
}
