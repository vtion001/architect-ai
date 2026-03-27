<?php

declare(strict_types=1);

namespace App\View\Composers;

use Illuminate\View\View;

/**
 * View Composer for content-viewer.blade.php
 *
 * Extracts complex PHP data preparation logic from the Blade view.
 */
class ContentViewerComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $content = $view->getData()['content'] ?? null;

        if (! $content) {
            $view->with('postsData', []);

            return;
        }

        $publishedIndexes = $view->getData()['publishedIndexes'] ?? [];
        $postsData = $this->parseContentSegments($content, $publishedIndexes);

        $view->with('postsData', $postsData);
    }

    /**
     * Parse content result into individual post segments.
     */
    protected function parseContentSegments($content, array $publishedIndexes): array
    {
        $rawResult = trim($content->result ?? '');
        $expectedCount = $content->options['count'] ?? 1;

        // Primary split: separator lines (---, ***, ___)
        $rawSegments = preg_split('/^\s*[-*_]{3,}\s*$/m', $rawResult);

        // Fallback 1: Numbered list (e.g., "1. ", "2. ")
        if (count($rawSegments) < $expectedCount) {
            $numberedSplit = preg_split('/^\s*\d+\.\s+/m', $rawResult);
            $numberedSplit = array_values(array_filter(array_map('trim', $numberedSplit)));

            if (count($numberedSplit) >= $expectedCount) {
                $rawSegments = $numberedSplit;
            }
        }

        // Fallback 2: Double newline
        if (count($rawSegments) < $expectedCount) {
            $newlineSplit = preg_split('/\R{2,}/', $rawResult);

            if (count($newlineSplit) >= $expectedCount) {
                $rawSegments = $newlineSplit;
            }
        }

        // Default if no segments found
        if (empty($rawSegments)) {
            $rawSegments = [$content->result ?? 'No content generated.'];
        }

        // Clean and filter
        $rawSegments = array_values(array_filter(array_map('trim', $rawSegments)));

        // Extract global hashtags if present at end
        $globalHashtags = $this->extractGlobalHashtags($rawSegments);

        // Build posts data array
        return $this->buildPostsData($rawSegments, $globalHashtags, $publishedIndexes);
    }

    /**
     * Extract global hashtags from the last segment if applicable.
     */
    protected function extractGlobalHashtags(array &$rawSegments): string
    {
        if (count($rawSegments) <= 1) {
            return '';
        }

        $lastSegment = end($rawSegments);

        // Check if last segment is just hashtags (short, starts with #, no newlines)
        if (str_starts_with($lastSegment, '#') &&
            strlen($lastSegment) < 300 &&
            ! str_contains($lastSegment, "\n")) {
            array_pop($rawSegments);

            return $lastSegment;
        }

        return '';
    }

    /**
     * Build the posts data array with clean formatting.
     */
    protected function buildPostsData(array $rawSegments, string $globalHashtags, array $publishedIndexes): array
    {
        $postsData = [];

        foreach ($rawSegments as $idx => $post) {
            // Clean remaining number prefixes
            $finalPostContent = preg_replace('/^\d+\.\s*/', '', trim($post));

            // Append global hashtags if not present
            if ($globalHashtags && ! str_contains($finalPostContent, $globalHashtags)) {
                $finalPostContent .= "\n\n".$globalHashtags;
            }

            // Generate clean HTML version
            $cleanText = $this->cleanMarkdownForDisplay($finalPostContent);
            $cleanHtml = nl2br(e($cleanText));

            $postsData[] = [
                'index' => $idx,
                'raw' => $finalPostContent,
                'html' => $cleanHtml,
                'published' => in_array($idx, $publishedIndexes),
            ];
        }

        return $postsData;
    }

    /**
     * Clean markdown formatting for display.
     */
    protected function cleanMarkdownForDisplay(string $text): string
    {
        // Remove markdown headers
        $text = preg_replace('/^#+\s+/m', '', $text);

        // Remove bold/italic markers
        $text = str_replace(['*', '`'], '', $text);

        return $text;
    }
}
