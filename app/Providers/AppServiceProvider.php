<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Product;
use App\Models\Storage;
use App\Models\Location;
use App\Models\Warehouse;
use App\Observers\UserObserver;
use App\Observers\BranchObserver;
use App\Observers\CompanyObserver;
use App\Observers\ProductObserver;
use App\Observers\StorageObserver;
use App\Observers\LocationObserver;
use App\Observers\WarehouseObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Storage::observe(StorageObserver::class);
        Company::observe(CompanyObserver::class);
        Location::observe(LocationObserver::class);
        Branch::observe(BranchObserver::class);
        Product::observe(ProductObserver::class);
        User::observe(UserObserver::class);
        Warehouse::observe(WarehouseObserver::class);
    }
}
