<?php

namespace App\Console\Commands;

use App\Models\KnowledgeBaseAsset;
use App\Services\VectorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncVectorKnowledgeBase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'architect:sync-vectors {--force : Force re-sync even if exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Embed and sync all knowledge base assets to Qdrant Vector Database';

    /**
     * Execute the console command.
     */
    public function handle(VectorService $vectorService)
    {
        $this->info('Starting Vector Synchronization Protocol...');

        $query = KnowledgeBaseAsset::query();
        $count = $query->count();

        if ($count === 0) {
            $this->info('No assets found in Knowledge Base.');

            return;
        }

        $this->info("Found {$count} assets. Proceeding to embed...");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        // Use cursor to minimize memory usage
        foreach ($query->cursor() as $asset) {
            try {
                // Upsert to Qdrant
                // Payload includes tenant_id for isolation filtering
                $success = $vectorService->upsert(
                    (string) $asset->id,
                    $asset->content ?? '',
                    [
                        'title' => $asset->title,
                        'tenant_id' => $asset->tenant_id,
                        'type' => $asset->type ?? 'document',
                        'updated_at' => $asset->updated_at ? $asset->updated_at->toIso8601String() : null,
                    ]
                );

                if (! $success) {
                    $this->error("\nFailed to sync Asset ID: {$asset->id}");
                    Log::error("Vector Sync Failed for Asset ID: {$asset->id}");
                }

                // Rate limit protection (Gemini free tier allows 15 RPM)
                sleep(4);

            } catch (\Throwable $e) {
                $this->error("\nException for Asset ID: {$asset->id} - ".$e->getMessage());
                Log::error("Vector Sync Exception for Asset ID: {$asset->id} - ".$e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Vector Synchronization Complete.');
        $this->info('Your Knowledge Base is now semantically searchable.');
    }
}
