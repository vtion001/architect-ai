<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Content;
use Illuminate\Support\Facades\Log;

/**
 * Observer for Content model.
 *
 * Handles side effects without cluttering controllers (Observer Pattern).
 * This keeps business logic clean and makes events reusable.
 */
class ContentObserver
{
    /**
     * Handle the Content "created" event.
     */
    public function created(Content $content): void
    {
        // Log content creation for analytics
        Log::info('ContentObserver: New content created', [
            'id' => $content->id,
            'tenant_id' => $content->tenant_id,
            'type' => $content->type,
            'topic' => $content->topic,
        ]);

        // Future: Index for search, update tenant stats, etc.
    }

    /**
     * Handle the Content "updated" event.
     */
    public function updated(Content $content): void
    {
        // Track status changes
        if ($content->isDirty('status')) {
            $oldStatus = $content->getOriginal('status');
            $newStatus = $content->status;

            Log::info('ContentObserver: Content status changed', [
                'id' => $content->id,
                'from' => $oldStatus,
                'to' => $newStatus,
            ]);
        }
    }

    /**
     * Handle the Content "deleted" event.
     */
    public function deleted(Content $content): void
    {
        Log::info('ContentObserver: Content deleted', [
            'id' => $content->id,
            'tenant_id' => $content->tenant_id,
        ]);

        // Future: Cleanup related resources, update stats
    }

    /**
     * Handle the Content "forceDeleted" event.
     */
    public function forceDeleted(Content $content): void
    {
        Log::info('ContentObserver: Content force deleted', [
            'id' => $content->id,
        ]);
    }
}
