<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateMessageRequest;
use App\Models\ContactMessage;
use App\Support\Admin\AdminView;
use App\Support\Admin\MessageManager;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MessageController extends Controller
{
    public function __construct(private readonly MessageManager $messages) {}

    public function index(): Response
    {
        return Inertia::render('admin/messages/Index', [
            'messages' => ContactMessage::query()
                ->latest()
                ->get()
                ->map(fn (ContactMessage $message): array => AdminView::message($message))
                ->values()
                ->all(),
        ]);
    }

    public function show(ContactMessage $message): Response
    {
        return Inertia::render('admin/messages/Show', [
            'message' => AdminView::message($message),
        ]);
    }

    public function update(UpdateMessageRequest $request, ContactMessage $message): RedirectResponse
    {
        $readAt = $request->validated('read_at');

        $this->messages->setRead(
            $message,
            is_string($readAt) ? $readAt : null,
        );

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'El mensaje se actualizó correctamente.',
        ]);

        return to_route('admin.messages.show', $message);
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $this->messages->destroy($message);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'El mensaje se eliminó correctamente.',
        ]);

        return to_route('admin.messages.index');
    }
}
