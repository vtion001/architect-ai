<?php

namespace App\Console\Commands;

use App\Models\Content;
use App\Models\MediaAsset;
use Illuminate\Console\Command;

class CleanupExpiredImages extends Command
{
    protected $signature = 'media:cleanup-expired 
                            {--delete : Delete expired media assets instead of just marking them}
                            {--dry-run : Show what would be done without making changes}';

    protected $description = 'Find and cleanup media assets and content with expired Azure/OpenAI URLs';

    private const EXPIRED_PATTERNS = ['oaidalle', 'blob.core.windows'];

    private const PLACEHOLDER_URL = '/images/placeholder-expired.png';

    public function handle()
    {
        $this->info('🔍 Scanning for expired media URLs...');

        $totalFixed = 0;

        // 1. Clean up MediaAssets table
        $totalFixed += $this->cleanupMediaAssets();

        // 2. Clean up Contents table (social planner posts with embedded images)
        $totalFixed += $this->cleanupContents();

        if ($totalFixed === 0) {
            $this->info('✅ No expired Azure/OpenAI URLs found in the database.');
        } else {
            $this->info("🎉 Total cleaned up: {$totalFixed} records");
        }

        return 0;
    }

    private function cleanupMediaAssets(): int
    {
        $this->newLine();
        $this->info('📦 Checking MediaAssets table...');

        $expiredAssets = MediaAsset::where(function ($q) {
            foreach (self::EXPIRED_PATTERNS as $pattern) {
                $q->orWhere('url', 'like', "%{$pattern}%");
            }
        })->get();

        if ($expiredAssets->isEmpty()) {
            $this->line('   No expired URLs in media_assets table.');

            return 0;
        }

        $this->warn("   Found {$expiredAssets->count()} media assets with temporary Azure URLs.");

        $table = [];
        foreach ($expiredAssets as $asset) {
            $table[] = [
                'ID' => substr($asset->id, 0, 8).'...',
                'Name' => substr($asset->name ?? 'Unnamed', 0, 35).'...',
                'Created' => $asset->created_at?->format('Y-m-d H:i') ?? 'N/A',
            ];
        }
        $this->table(['ID', 'Name', 'Created'], $table);

        if ($this->option('dry-run')) {
            return 0;
        }

        if ($this->option('delete')) {
            $deleted = MediaAsset::where(function ($q) {
                foreach (self::EXPIRED_PATTERNS as $pattern) {
                    $q->orWhere('url', 'like', "%{$pattern}%");
                }
            })->delete();
            $this->info("   🗑️  Deleted {$deleted} expired media assets.");

            return $deleted;
        }

        $updated = 0;
        foreach ($expiredAssets as $asset) {
            $metadata = $asset->metadata ?? [];
            $metadata['expired'] = true;
            $metadata['original_url'] = $asset->url;
            $metadata['expired_at'] = now()->toIso8601String();

            $asset->update([
                'url' => self::PLACEHOLDER_URL,
                'metadata' => $metadata,
            ]);
            $updated++;
        }

        $this->info("   📝 Marked {$updated} media assets with placeholder.");

        return $updated;
    }

    private function cleanupContents(): int
    {
        $this->newLine();
        $this->info('📝 Checking Contents table (Social Planner posts)...');

        $expiredContents = Content::where(function ($q) {
            foreach (self::EXPIRED_PATTERNS as $pattern) {
                $q->orWhere('options', 'like', "%{$pattern}%");
            }
        })->get();

        if ($expiredContents->isEmpty()) {
            $this->line('   No expired URLs in contents table.');

            return 0;
        }

        $this->warn("   Found {$expiredContents->count()} content records with expired image URLs.");

        $table = [];
        foreach ($expiredContents as $content) {
            $table[] = [
                'ID' => substr($content->id, 0, 8).'...',
                'Title' => substr($content->title ?? 'Untitled', 0, 35).'...',
                'Type' => $content->type ?? 'N/A',
            ];
        }
        $this->table(['ID', 'Title', 'Type'], $table);

        if ($this->option('dry-run')) {
            $this->info('   🔸 Dry run - no changes made.');

            return 0;
        }

        $updated = 0;
        foreach ($expiredContents as $content) {
            $options = $content->options ?? [];
            $changed = false;

            // Fix image_url field
            if (! empty($options['image_url']) && $this->isExpiredUrl($options['image_url'])) {
                $options['original_image_url'] = $options['image_url'];
                $options['image_url'] = self::PLACEHOLDER_URL;
                $changed = true;
            }

            // Fix visuals array
            if (! empty($options['visuals']) && is_array($options['visuals'])) {
                foreach ($options['visuals'] as $key => $url) {
                    if ($this->isExpiredUrl($url)) {
                        $options['original_visuals'][$key] = $url;
                        $options['visuals'][$key] = self::PLACEHOLDER_URL;
                        $changed = true;
                    }
                }
            }

            if ($changed) {
                $options['images_expired_at'] = now()->toIso8601String();
                $content->update(['options' => $options]);
                $updated++;
            }
        }

        $this->info("   📝 Fixed {$updated} content records with placeholder images.");
        $this->warn('   💡 Tip: Users can regenerate images in Content Creator.');

        return $updated;
    }

    private function isExpiredUrl(?string $url): bool
    {
        if (empty($url)) {
            return false;
        }

        foreach (self::EXPIRED_PATTERNS as $pattern) {
            if (str_contains($url, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
