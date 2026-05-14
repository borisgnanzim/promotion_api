<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait ActivityLogger
{
    /**
     * Log an activity.
     */
    public function logActivity(array $data)
    {
        ActivityLog::create([
            'user_ref'    => $data['user_ref'] ?? (auth()->user()->ref ?? null),
            'role'        => $data['role'] ?? (auth()->user()->roles->first()->name ?? null),
            'action'      => $data['action'],
            'target_type' => $data['target_type'] ?? null,
            'target_ref'  => $data['target_ref'] ?? null,
            'description' => $data['description'] ?? null,
            'changes'     => $data['changes'] ?? null,
        ]);
    }
}
