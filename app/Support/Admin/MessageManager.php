<?php

namespace App\Support\Admin;

use App\Models\ContactMessage;
use Illuminate\Support\Carbon;

final class MessageManager
{
    public function setRead(ContactMessage $message, ?string $readAt): void
    {
        $message->update([
            'read_at' => $readAt === null || $readAt === '' ? null : Carbon::parse($readAt),
        ]);
    }

    public function destroy(ContactMessage $message): void
    {
        $message->delete();
    }
}
