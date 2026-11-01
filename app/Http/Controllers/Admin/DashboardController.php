<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\ServicePage;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'newMessages' => ContactMessage::new()->count(),
            'totalMessages' => ContactMessage::count(),
            'servicesCount' => ServicePage::count(),
            'postsCount' => Post::count(),
            'latestMessages' => ContactMessage::latest()->limit(6)->get(),
        ]);
    }
}
