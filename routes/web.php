<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\ContactAnalyticsController;
use App\Http\Controllers\Site\ContactClickTrackingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\SeoPageController;
use App\Http\Controllers\Admin\SeoPlanController;
use App\Http\Controllers\Admin\ServicePageController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Site\BlogController;
use App\Http\Controllers\Site\ContactController;
use App\Http\Controllers\Site\FaqController as SiteFaqController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\SeoController;
use App\Http\Controllers\Site\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
Route::post('/tracking/contact-click', [ContactClickTrackingController::class, 'store'])->middleware('throttle:60,1')->name('tracking.contact-click');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/faq', SiteFaqController::class)->name('faq');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.attempt');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/contact-analytics', ContactAnalyticsController::class)->name('contact-analytics');

        Route::resource('services', ServicePageController::class)->except('show');
        Route::post('/services/{service}/toggle', [ServicePageController::class, 'toggle'])->name('services.toggle');
        Route::post('/services/{service}/move/{direction}', [ServicePageController::class, 'move'])->name('services.move');

        Route::resource('posts', PostController::class)->except('show');
        Route::resource('faqs', FaqController::class)->except('show');

        Route::get('/messages', [ContactMessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
        Route::patch('/messages/{message}', [ContactMessageController::class, 'update'])->name('messages.update');
        Route::delete('/messages/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');

        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

        Route::get('/seo', [SeoPageController::class, 'index'])->name('seo.index');
        Route::put('/seo/{seoPage}', [SeoPageController::class, 'update'])->name('seo.update');

        Route::prefix('seo-plan')->name('seo-plan.')->group(function () {
            Route::get('/', [SeoPlanController::class, 'index'])->name('index');

            Route::post('/keywords', [SeoPlanController::class, 'storeKeyword'])->name('keywords.store');
            Route::post('/keywords/import/preview', [SeoPlanController::class, 'previewImport'])->name('keywords.import.preview');
            Route::post('/keywords/import', [SeoPlanController::class, 'import'])->name('keywords.import');
            Route::get('/keywords/export', [SeoPlanController::class, 'exportKeywords'])->name('keywords.export');
            Route::put('/keywords/{keyword}', [SeoPlanController::class, 'updateKeyword'])->name('keywords.update');
            Route::patch('/keywords/{keyword}/inline', [SeoPlanController::class, 'inlineKeyword'])->name('keywords.inline');
            Route::delete('/keywords/{keyword}', [SeoPlanController::class, 'destroyKeyword'])->name('keywords.destroy');
            Route::post('/keywords/{keyword}/task', [SeoPlanController::class, 'createTask'])->name('keywords.task');

            Route::post('/tasks', [SeoPlanController::class, 'storeTask'])->name('tasks.store');
            Route::post('/tasks/import/preview', [SeoPlanController::class, 'previewTaskImport'])->name('tasks.import.preview');
            Route::post('/tasks/import', [SeoPlanController::class, 'importTasks'])->name('tasks.import');
            Route::put('/tasks/{task}', [SeoPlanController::class, 'updateTask'])->name('tasks.update');
            Route::patch('/tasks/{task}/status', [SeoPlanController::class, 'updateTaskStatus'])->name('tasks.status');
            Route::delete('/tasks/{task}', [SeoPlanController::class, 'destroyTask'])->name('tasks.destroy');
        });
    });
});
