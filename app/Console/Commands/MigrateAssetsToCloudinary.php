<?php

namespace App\Console\Commands;

use App\Models\MediaAsset;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MigrateAssetsToCloudinary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:migrate-cloudinary {--force : Force migration even if URL seems remote}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate local media assets to Cloudinary';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cloudName = config('services.cloudinary.cloud_name');
        $apiKey = config('services.cloudinary.api_key');
        $apiSecret = config('services.cloudinary.api_secret');

        if (! $cloudName || ! $apiKey || ! $apiSecret) {
            $this->error('Cloudinary credentials missing in configuration.');

            return 1;
        }

        $assets = MediaAsset::where('url', 'not like', '%cloudinary.com%')
            ->where('url', 'not like', 'http%') // Usually local paths start with /
            ->orWhere(function ($q) {
                // Also catch full local URLs if they exist
                $q->where('url', 'like', config('app.url').'%');
            })
            ->get();

        if ($this->option('force')) {
            $assets = MediaAsset::where('url', 'not like', '%cloudinary.com%')->get();
        }

        $count = $assets->count();
        $this->info("Found {$count} assets eligible for migration.");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($assets as $asset) {
            try {
                // Determine file path
                $localPath = null;

                if (str_starts_with($asset->url, '/')) {
                    $localPath = public_path($asset->url);
                } elseif (str_starts_with($asset->url, config('app.url'))) {
                    $relativePath = str_replace(config('app.url'), '', $asset->url);
                    $localPath = public_path($relativePath);
                }

                if (! $localPath || ! file_exists($localPath)) {
                    $this->warn("\nFile not found for asset ID {$asset->id}: {$asset->url}");
                    Log::warning("Migration: File not found for asset ID {$asset->id}");
                    $bar->advance();

                    continue;
                }

                // Upload to Cloudinary
                $timestamp = time();
                $params = ['timestamp' => $timestamp];
                ksort($params);
                $signString = http_build_query($params).$apiSecret;

                // Manual signature construction matches controller logic
                $stringToSign = 'timestamp='.$timestamp.$apiSecret;
                $signature = sha1($stringToSign);

                $response = Http::attach(
                    'file',
                    file_get_contents($localPath),
                    basename($localPath)
                )->post("https://api.cloudinary.com/v1_1/$cloudName/image/upload", [
                    'api_key' => $apiKey,
                    'timestamp' => $timestamp,
                    'signature' => $signature,
                ]);

                if ($response->successful()) {
                    $cloudinaryUrl = $response->json('secure_url');

                    $asset->update([
                        'url' => $cloudinaryUrl,
                        'source' => 'upload_migrated', // Mark as migrated
                    ]);

                    // Optional: Delete local file?
                    // unlink($localPath);
                } else {
                    $this->error("\nUpload failed for ID {$asset->id}: ".$response->body());
                    Log::error("Migration upload failed for ID {$asset->id}: ".$response->body());
                }

            } catch (
                Exception $e) {
                    $this->error("\nException for ID {$asset->id}: ".$e->getMessage());
                }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Migration completed.');

        return 0;
    }
}
