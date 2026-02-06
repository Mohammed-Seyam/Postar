<?php

namespace App\Interfaces;

use App\Models\ScheduledPost;

interface PlatformInterface
{
    public function publish(ScheduledPost $post): string; // Returns external ID
}
