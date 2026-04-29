<?php

namespace App\Services;

use App\Models\MobileNotification;
use App\Models\User;

class MobileNotificationService
{
    public function notifyUser(User $user, string $title, string $body, string $type, array $payload = []): MobileNotification
    {
        return MobileNotification::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $body,
            'type' => $type,
            'payload_json' => $payload ?: null,
        ]);
    }

    public function notifyUserByEmail(?string $email, string $title, string $body, string $type, array $payload = []): ?MobileNotification
    {
        if (!$email) {
            return null;
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return null;
        }

        return $this->notifyUser($user, $title, $body, $type, $payload);
    }
}
