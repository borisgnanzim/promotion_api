<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Filters\ActivityLogFilter;
use App\Http\Resources\ActivityLogResource;
use App\Models\ActivityLog;
use App\Traits\ActivityLogger;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group ActivityLog Management
 *
 * APIs to manage activity logs
 **/
class ActivityLogController extends Controller
{
    use JsonResponseTrait, ActivityLogger;
    /**
     * Display a listing of the activity logs.
     *
     * @queryParam ref string Filter by activity log reference
     * @queryParam user_ref string Filter by user reference
     * @queryParam action string Filter by action type (e.g., 'create', 'update', 'delete')
     * @queryParam created_at date Filter by creation date
     * This route allows you to list activity logs with dynamic filters.
     */
    public function index(ActivityLogFilter $filter)
    {
        // If 'all' query parameter is set, return all activity logs without pagination
        if (request()->boolean('all')) {
            $activityLogs = ActivityLog::filter($filter)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();
            return $this->successResponse(ActivityLogResource::collection($activityLogs), 'activity_logs', 200);
        }
        $activityLogs = ActivityLog::filter($filter)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return $this->successResponseWithPaginate(ActivityLogResource::class, $activityLogs, 'activity_logs');
    }
    /**
     * Display the specified activity log.
     */
    public function show(string $ref)
    {
        $activityLog = ActivityLog::with('user')->where('ref', $ref)->first();
        if (!$activityLog) {
            return $this->errorResponse('Activity log not found.', 404);
        }
        return $this->successResponse(ActivityLogResource::make($activityLog), 'Activity log retrieved successfully.');
    }
    /**
     * Delete the specified activity log.
     */
    public function destroy(string $ref)
    {
        $activityLog = ActivityLog::where('ref', $ref)->first();
        if (!$activityLog) {
            return $this->errorResponse('Activity log not found.', 404);
        }
        // Log Activity
        $this->logActivity([
            'action' => 'delete',
            'target_type' => 'ActivityLog',
            'target_ref' => $activityLog->ref,
        ]);
        $activityLog->delete();
        return $this->successResponse(null, 'Activity log deleted successfully.', 204);
    }

    /**
     * Clear all activity logs.
     */
    public function clear()
    {
        ActivityLog::truncate();
        // Log Activity
        $this->logActivity([
            'action' => 'clear',
            'target_type' => 'ActivityLog',
            'target_ref' => null,
        ]);
        return $this->successResponse(null, 'All activity logs cleared successfully.', 204);
    }

    public function getMyActivityLogs()
    {
        $activityLogs = ActivityLog::where('user_ref', Auth::user()->ref)
            ->orderBy('created_at', 'desc')
            ->paginate(30);
        return $this->successResponseWithPaginate(ActivityLogResource::class, $activityLogs, 'my_activity_logs');
    }

    public function getActivityLogsByUser(string $user_ref)
    {
        $activityLogs = ActivityLog::where('user_ref', $user_ref)
            ->orderBy('created_at', 'desc')
            ->paginate(30);
        return $this->successResponseWithPaginate(ActivityLogResource::class, $activityLogs, 'user_activity_logs');
    }
}