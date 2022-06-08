<?php

namespace App\Providers;

use App\Adapters\Clients\CommonService;
use App\Adapters\Clients\CommonServiceImpl;
use App\Adapters\Clients\StorageClient;
use App\Adapters\Clients\StorageClientImpl;
use App\Adapters\Clients\ZipFileCsvService;
use App\Adapters\Clients\ZipFileCsvServiceImpl;
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
        $this->app->bind(StorageClient::class, StorageClientImpl::class);
        $this->app->bind(ZipFileCsvService::class, ZipFileCsvServiceImpl::class);
        $this->app->bind(CommonService::class, CommonServiceImpl::class);
    
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
