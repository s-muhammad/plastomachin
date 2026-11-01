<?php

namespace App\Providers;

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.basecoat');
        Paginator::defaultSimpleView('vendor.pagination.basecoat');

        View::composer('*', function ($view) {
            $view->with('settings', Setting::values());
        });

        View::composer(['admin.layouts.app', 'admin.partials.sidebar'], function ($view) {
            $view->with('sidebarNewMessages', ContactMessage::new()->count());
        });
    }
}
