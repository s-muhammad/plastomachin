<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        return view('admin.messages.index', [
            'messages' => ContactMessage::query()
                ->when($status === 'new', fn ($query) => $query->where('status', 'new'))
                ->when($status === 'followed', fn ($query) => $query->where('status', 'followed'))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'status' => $status,
            'newCount' => ContactMessage::new()->count(),
        ]);
    }

    public function show(ContactMessage $message)
    {
        return view('admin.messages.show', compact('message'));
    }

    public function update(Request $request, ContactMessage $message)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:new,followed'],
        ]);

        $message->update($validated);

        return back()->with('success', 'وضعیت پیام تغییر کرد.');
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')->with('success', 'پیام حذف شد.');
    }
}
