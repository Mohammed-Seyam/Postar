<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class ReportingRepository
{
    public function getUserStats(string $userId): ?object
    {
        return DB::table('user_dashboard_stats_view')
            ->where('user_id', $userId)
            ->first();
    }

    public function getCalendar(string $userId): \Illuminate\Support\Collection
    {
        return DB::table('scheduled_posts_calendar_view')
            ->where('user_id', $userId)
            ->get();
    }

    public function getStorageUsage(string $userId): ?object
    {
        return DB::table('user_storage_usage_view')
            ->where('user_id', $userId)
            ->first();
    }
}
