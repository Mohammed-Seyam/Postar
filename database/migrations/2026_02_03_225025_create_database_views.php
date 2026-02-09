<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::statement("DROP VIEW IF EXISTS user_dashboard_stats_view");
        \Illuminate\Support\Facades\DB::statement("
            CREATE VIEW user_dashboard_stats_view AS
            SELECT 
                u.id as user_id,
                (SELECT COUNT(*) FROM videos v WHERE v.user_id = u.id AND v.deleted_at IS NULL) as total_videos,
                (SELECT COUNT(*) FROM videos v WHERE v.user_id = u.id AND v.status = 'scheduled' AND v.deleted_at IS NULL) as scheduled_videos,
                (SELECT COUNT(*) FROM videos v WHERE v.user_id = u.id AND v.status = 'published' AND v.deleted_at IS NULL) as published_videos,
                (SELECT COUNT(*) FROM videos v WHERE v.user_id = u.id AND v.status = 'failed' AND v.deleted_at IS NULL) as failed_videos,
                (SELECT COUNT(*) FROM scheduled_posts sp 
                 JOIN videos v ON sp.video_id = v.id 
                 WHERE v.user_id = u.id AND sp.status = 'pending' AND sp.deleted_at IS NULL AND v.deleted_at IS NULL) as upcoming_posts
            FROM users u
        ");

        \Illuminate\Support\Facades\DB::statement("DROP VIEW IF EXISTS scheduled_posts_calendar_view");
        \Illuminate\Support\Facades\DB::statement("
            CREATE VIEW scheduled_posts_calendar_view AS
            SELECT 
                sp.id as scheduled_post_id,
                v.user_id,
                sp.video_id,
                sp.platform,
                sp.publish_at,
                sp.caption,
                v.thumbnail_path as video_thumbnail,
                sp.status
            FROM scheduled_posts sp
            JOIN videos v ON sp.video_id = v.id
            WHERE sp.deleted_at IS NULL AND v.deleted_at IS NULL
        ");

        \Illuminate\Support\Facades\DB::statement("DROP VIEW IF EXISTS user_storage_usage_view");
        \Illuminate\Support\Facades\DB::statement("
            CREATE VIEW user_storage_usage_view AS
            SELECT 
                user_id,
                COUNT(*) as total_video_count,
                0 as total_storage_mb -- Placeholder as file size not in schema, assuming extraction later or update
            FROM videos
            GROUP BY user_id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("DROP VIEW IF EXISTS user_dashboard_stats_view");
        \Illuminate\Support\Facades\DB::statement("DROP VIEW IF EXISTS scheduled_posts_calendar_view");
        \Illuminate\Support\Facades\DB::statement("DROP VIEW IF EXISTS user_storage_usage_view");
    }
};
