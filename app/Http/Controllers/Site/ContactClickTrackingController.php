<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\ContactClickEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ContactClickTrackingController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(['whatsapp', 'phone'])],
            'page_url' => ['required', 'string', 'max:2048'],
            'link_url' => ['nullable', 'string', 'max:2048'],
            'referrer' => ['nullable', 'string', 'max:2048'],
            'utm_source' => ['nullable', 'string', 'max:255'],
            'utm_medium' => ['nullable', 'string', 'max:255'],
            'utm_campaign' => ['nullable', 'string', 'max:255'],
        ]);

        foreach (['page_url', 'referrer'] as $field) {
            if (! empty($data[$field]) && ! Str::startsWith($data[$field], ['http://', 'https://'])) {
                abort(422, 'Invalid URL.');
            }
        }

        if (! empty($data['link_url'])) {
            $validLink = $data['type'] === 'phone'
                ? Str::startsWith($data['link_url'], 'tel:')
                : Str::startsWith($data['link_url'], ['http://', 'https://']);

            if (! $validLink) {
                abort(422, 'Invalid contact link.');
            }
        }

        ContactClickEvent::create($data);

        return response()->json(['tracked' => true], 201);
    }
}
