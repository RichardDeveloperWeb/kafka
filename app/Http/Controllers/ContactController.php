<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('public/Contact');
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        ContactMessage::create($request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Gracias por escribirnos. Tu mensaje fue registrado y te responderemos pronto.',
        ]);

        return to_route('contact');
    }
}
