<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Qdrant\Config;
use Qdrant\Http\Builder;
use Qdrant\Models\PointStruct;
use Qdrant\Models\Request\CreateCollection;
use Qdrant\Models\Request\SearchRequest;
use Qdrant\Models\Request\VectorParams;
use Qdrant\Models\PointsStruct;
use Qdrant\Models\VectorStruct;
use Qdrant\Qdrant;

class VectorService
{
    protected Qdrant $client;

    protected string $collection = 'knowledge_base';

    protected int $dimension = 1536; // OpenAI text-embedding-3-small

    // Config
    protected ?string $apiKey;

    public function __construct()
    {
        // Internal Docker network access
        $config = new Config('http://qdrant:6333');
        // Qdrant HTTP Transport Builder
        $transport = (new Builder)->build($config);
        $this->client = new Qdrant($transport);

        $this->apiKey = config('services.openai.key');
    }

    public function ensureCollection(): void
    {
        try {
            $response = $this->client->collections($this->collection)->info();
            if (! isset($response['result'])) {
                $this->createCollection();
            }
        } catch (\Exception $e) {
            // Likely 404 or connection error, try creating
            $this->createCollection();
        }
    }

    protected function createCollection(): void
    {
        try {
            $vectorParams = new VectorParams($this->dimension, VectorParams::DISTANCE_COSINE);
            $createCollection = (new CreateCollection())->addVector($vectorParams);
            $this->client->collections($this->collection)->create($createCollection);
            Log::info("Qdrant collection '{$this->collection}' created.");
        } catch (\Exception $e) {
            Log::error('Failed to create Qdrant collection: '.$e->getMessage());
        }
    }

    /**
     * Generate embedding using OpenAI API
     */
    public function embed(string $text): ?array
    {
        if (! $this->apiKey) {
            return null;
        }

        try {
            $embeddingModel = config('services.openai.embedding_model', 'text-embedding-3-small');

            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->retry(3, 10000, function ($exception, $request) {
                    return $exception->response->status() === 429;
                })
                ->post('https://api.openai.com/v1/embeddings', [
                    'model' => $embeddingModel,
                    'input' => $text,
                ]);

            if ($response->successful()) {
                return $response->json('data.0.embedding');
            }

            Log::error('OpenAI Embedding Error: '.$response->body());
        } catch (\Exception $e) {
            Log::error('OpenAI Embedding Exception: '.$e->getMessage());
        }

        return null;
    }

    public function upsert(string $id, string $content, array $payload = []): bool
    {
        $vector = $this->embed($content);
        if (! $vector) {
            return false;
        }

        // Ensure collection exists before first write
        $this->ensureCollection();

        $point = new PointStruct(
            id: $id,
            vector: new VectorStruct($vector),
            payload: array_merge(['content' => $content], $payload)
        );

        $pointsStruct = new PointsStruct();
        $pointsStruct->addPoint($point);

        try {
            $this->client->collections($this->collection)->points()->upsert($pointsStruct);

            return true;
        } catch (\Exception $e) {
            Log::error('Qdrant Upsert Error: '.$e->getMessage());

            return false;
        }
    }

    public function search(string $query, int $limit = 3): array
    {
        $vector = $this->embed($query);
        if (! $vector) {
            return [];
        }

        try {
            $searchRequest = (new SearchRequest(new VectorStruct($vector)))
                ->setLimit($limit)
                ->setWithPayload(true);

            $results = $this->client->collections($this->collection)->points()->search($searchRequest);

            return $results['result'] ?? [];
        } catch (\Exception $e) {
            Log::error('Qdrant Search Error: '.$e->getMessage());

            return [];
        }
    }
}
