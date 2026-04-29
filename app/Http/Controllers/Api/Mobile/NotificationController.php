<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\MobileDeviceToken;
use Illuminate\Http\Request;

class NotificationController extends BaseMobileController
{
    public function index(Request $request)
    {
        $notifications = auth()->user()
            ->mobileNotifications()
            ->latest()
            ->paginate((int) $request->get('per_page', 20));

        return $this->success([
            'items' => $notifications->getCollection()->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'body' => $notification->body,
                    'payload' => $notification->payload_json ?? [],
                    'read_at' => optional($notification->read_at)->toIso8601String(),
                    'created_at' => optional($notification->created_at)->toIso8601String(),
                ];
            })->values(),
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    public function markRead(Request $request)
    {
        $validated = $request->validate([
            'notification_ids' => 'nullable|array',
            'notification_ids.*' => 'integer',
            'all' => 'nullable|boolean',
        ]);

        $query = auth()->user()->mobileNotifications()->whereNull('read_at');

        if (!empty($validated['all'])) {
            $query->update(['read_at' => now()]);
        } elseif (!empty($validated['notification_ids'])) {
            $query->whereIn('id', $validated['notification_ids'])->update(['read_at' => now()]);
        }

        return $this->success([], 'Notifications updated successfully.');
    }

    public function storeDeviceToken(Request $request)
    {
        $validated = $request->validate([
            'device_token' => 'required|string|max:255',
            'platform' => 'nullable|string|max:20',
            'app_version' => 'nullable|string|max:50',
        ]);

        $token = MobileDeviceToken::updateOrCreate(
            ['device_token' => $validated['device_token']],
            [
                'user_id' => auth()->id(),
                'platform' => $validated['platform'] ?? null,
                'app_version' => $validated['app_version'] ?? null,
                'last_used_at' => now(),
            ]
        );

        return $this->success([
            'device_token' => [
                'id' => $token->id,
                'platform' => $token->platform,
                'app_version' => $token->app_version,
            ],
        ], 'Device token stored successfully.', 201);
    }
}
