<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    public static function record(Model $subject, string $action, array $newValues = [], ?array $oldValues = null): void
    {
        $subject->activityLogs()->create([
            'user_id' => auth()->id(),
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 1000),
        ]);
    }
}
