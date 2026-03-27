<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\KnowledgeBaseAsset;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Centralized RAG (Retrieval-Augmented Generation) service.
 * 
 * Consolidates all knowledge base context retrieval logic that was previously
 * duplicated across ReportService, ResearchService, and ContentService.
 */
class KnowledgeBaseService
{
    public function __construct(
        protected VectorService $vectorService
    ) {}

    /**
     * Retrieve relevant context using Hybrid Search (Vector + SQL Fallback).
     *
     * This is the primary method for RAG context retrieval across the application.
     */
    public function getContext(string $query, int $limit = 3, float $minRelevance = 0.65): ?string
    {
        $tenant = app(Tenant::class);
        if (!$tenant || empty($query) || !$tenant->id) {
            return null;
        }

        // 1. Check if query is a specific Asset ID (UUID format)
        if (Str::isUuid($query)) {
            return $this->getAssetContextById($query, $tenant->id);
        }

        // 2. Try Vector Search (Semantic)
        $vectorContext = $this->attemptVectorSearch($query, $tenant->id, $limit, $minRelevance);
        if ($vectorContext) {
            return $vectorContext;
        }

        // 3. Fallback to SQL LIKE search
        return $this->attemptKeywordSearch($query, $tenant->id, $limit);
    }

    /**
     * Get context for a specific asset by ID.
     */
    protected function getAssetContextById(string $assetId, string $tenantId): ?string
    {
        $asset = KnowledgeBaseAsset::where('tenant_id', $tenantId)->find($assetId);
        
        if (!$asset) {
            return null;
        }

        if ($asset->type === 'folder') {
            return $this->getFolderContentRecursive($asset);
        }

        return "--- SOURCE: {$asset->title} ---\n{$asset->content}";
    }

    /**
     * Attempt semantic vector search.
     */
    protected function attemptVectorSearch(string $query, string $tenantId, int $limit, float $minRelevance): ?string
    {
        try {
            $results = $this->vectorService->search($query, $limit * 3); // Fetch more for filtering
            $contextParts = [];

            foreach ($results as $item) {
                $payload = $item['payload'] ?? [];
                $score = $item['score'] ?? 0;

                // Tenant isolation check
                if (isset($payload['tenant_id']) && $payload['tenant_id'] !== $tenantId) {
                    continue;
                }

                if ($score > $minRelevance) {
                    $title = $payload['title'] ?? 'Internal Source';
                    $content = $payload['content'] ?? '';
                    $relevance = number_format($score * 100, 1);
                    $contextParts[] = "--- SEMANTIC SOURCE (Relevance: {$relevance}%): {$title} ---\n{$content}";
                }

                if (count($contextParts) >= $limit) {
                    break;
                }
            }

            return !empty($contextParts) ? implode("\n\n", $contextParts) : null;

        } catch (\Exception $e) {
            Log::warning("Vector search skipped: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Fallback to keyword-based SQL search.
     */
    protected function attemptKeywordSearch(string $query, string $tenantId, int $limit): ?string
    {
        $assets = KnowledgeBaseAsset::where('tenant_id', $tenantId)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get();

        if ($assets->isEmpty()) {
            return null;
        }

        return $assets
            ->map(fn($a) => "--- KEYWORD SOURCE: {$a->title} ---\n{$a->content}")
            ->implode("\n\n");
    }

    /**
     * Recursively get content from folder assets.
     */
    protected function getFolderContentRecursive(KnowledgeBaseAsset $folder, int $depth = 0): string
    {
        if ($depth > 5) {
            return ''; // Prevent infinite recursion
        }

        $content = "--- FOLDER: {$folder->title} ---\n";

        $children = KnowledgeBaseAsset::where('parent_id', $folder->id)->get();

        foreach ($children as $child) {
            if ($child->type === 'folder') {
                $content .= $this->getFolderContentRecursive($child, $depth + 1);
            } else {
                $content .= "--- SOURCE: {$child->title} ---\n{$child->content}\n\n";
            }
        }

        return $content;
    }

    /**
     * Format knowledge base assets into a context string.
     *
     * @param array $assets Array of asset objects with title and content
     * @return string|null Formatted context string or null if empty
     */
    public function formatContext(array $assets): ?string
    {
        if (empty($assets)) {
            return null;
        }

        $parts = ["[KNOWLEDGE BASE CONTEXT]"];

        foreach ($assets as $asset) {
            $title = $asset->title ?? 'Untitled';
            $content = $asset->content ?? '';
            $parts[] = "--- SOURCE: {$title} ---\n{$content}";
        }

        return implode("\n\n", $parts);
    }
}
