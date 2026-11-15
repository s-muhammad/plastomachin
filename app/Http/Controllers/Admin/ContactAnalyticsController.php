<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactClickEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ContactAnalyticsController extends Controller
{
    public function __invoke(Request $request)
    {
        $days = (int) $request->query('days', 30);
        abort_unless(in_array($days, [7, 30, 90], true), 404);

        $from = now()->subDays($days - 1)->startOfDay();
        $events = ContactClickEvent::query()
            ->where('created_at', '>=', $from)
            ->latest()
            ->get();

        $daily = collect(range($days - 1, 0))
            ->mapWithKeys(fn (int $offset) => [now()->subDays($offset)->toDateString() => 0]);

        foreach ($events as $event) {
            $key = $event->created_at->toDateString();
            if ($daily->has($key)) {
                $daily->put($key, $daily->get($key) + 1);
            }
        }

        $maxDaily = max(1, (int) $daily->max());

        return view('admin.contact-analytics.index', [
            'days' => $days,
            'total' => $events->count(),
            'whatsappCount' => $events->where('type', 'whatsapp')->count(),
            'phoneCount' => $events->where('type', 'phone')->count(),
            'daily' => $daily,
            'maxDaily' => $maxDaily,
            'topPages' => $events->groupBy('page_url')
                ->map(fn ($items, $url) => ['url' => $url, 'total' => $items->count()])
                ->sortByDesc('total')
                ->take(8)
                ->values(),
            'latestEvents' => $events->take(12),
        ]);
    }
}
