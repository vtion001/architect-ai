<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;

class GenerateBlogBatchTest extends TestCase
{
    protected function parseAnglesStatic(string $json, int $count): array
    {
        $count = (int) $count;

        $json = trim($json);

        if (preg_match('/```json\s*(.*?)\s*```/s', $json, $m)) {
            $json = $m[1];
        }

        $data = json_decode($json, true);

        if (! is_array($data)) {
            return $this->fallbackAnglesStatic($count);
        }

        if (isset($data['angles']) && is_array($data['angles'])) {
            $data = $data['angles'];
        }

        $angles = [];
        foreach (array_slice(array_values($data), 0, $count) as $item) {
            if (! is_array($item)) {
                continue;
            }
            $angles[] = [
                'angle' => $item['angle'] ?? $item['title'] ?? $item['name'] ?? 'General Guide',
                'keyword' => $item['keyword'] ?? $item['focus_keyword'] ?? '',
                'description' => $item['description'] ?? '',
            ];
        }

        if (count($angles) < $count) {
            $angles = array_merge($angles, $this->fallbackAnglesStatic($count - count($angles)));
        }

        return array_slice($angles, 0, $count);
    }

    protected function fallbackAnglesStatic(int $count): array
    {
        $defaults = [
            ['angle' => 'Getting Started Guide', 'keyword' => 'beginner', 'description' => 'Entry-level overview for newcomers'],
            ['angle' => 'Advanced Strategies', 'keyword' => 'advanced', 'description' => 'In-depth techniques for experienced readers'],
            ['angle' => 'Common Mistakes to Avoid', 'keyword' => 'mistakes', 'description' => 'Pitfalls and how to sidestep them'],
            ['angle' => 'Best Practices', 'keyword' => 'best practices', 'description' => 'Proven methods and standards'],
            ['angle' => 'Case Studies & Examples', 'keyword' => 'case study', 'description' => 'Real-world examples and results'],
        ];

        return array_slice($defaults, 0, $count);
    }

    public function test_parse_angles_extracts_json_array()
    {
        $angles = $this->parseAnglesStatic('[
            {"angle": "Angle One", "keyword": "kw1", "description": "d1"},
            {"angle": "Angle Two", "keyword": "kw2", "description": "d2"},
            {"angle": "Angle Three", "keyword": "kw3", "description": "d3"}
        ]', 3);

        $this->assertCount(3, $angles);
        $this->assertEquals('Angle One', $angles[0]['angle']);
        $this->assertEquals('kw1', $angles[0]['keyword']);
        $this->assertEquals('Angle Two', $angles[1]['angle']);
        $this->assertEquals('kw2', $angles[1]['keyword']);
        $this->assertEquals('Angle Three', $angles[2]['angle']);
    }

    public function test_parse_angles_extracts_from_wrapped_angles_key()
    {
        $angles = $this->parseAnglesStatic(json_encode([
            'angles' => [
                ['angle' => 'Wrapped 1', 'keyword' => 'w1'],
                ['angle' => 'Wrapped 2', 'keyword' => 'w2'],
            ],
        ]), 2);

        $this->assertCount(2, $angles);
        $this->assertEquals('Wrapped 1', $angles[0]['angle']);
        $this->assertEquals('Wrapped 2', $angles[1]['angle']);
    }

    public function test_parse_angles_limits_to_count()
    {
        $angles = $this->parseAnglesStatic(json_encode([
            ['angle' => 'A1', 'keyword' => 'kw1'],
            ['angle' => 'A2', 'keyword' => 'kw2'],
            ['angle' => 'A3', 'keyword' => 'kw3'],
            ['angle' => 'A4', 'keyword' => 'kw4'],
            ['angle' => 'A5', 'keyword' => 'kw5'],
        ]), 2);

        $this->assertCount(2, $angles);
    }

    public function test_parse_angles_falls_back_on_invalid_json()
    {
        $angles = $this->parseAnglesStatic('not valid json {{{', 3);

        $this->assertCount(3, $angles);
        $this->assertArrayHasKey('angle', $angles[0]);
        $this->assertArrayHasKey('keyword', $angles[0]);
    }

    public function test_parse_angles_falls_back_on_empty_array()
    {
        $angles = $this->parseAnglesStatic('[]', 3);

        $this->assertCount(3, $angles);
    }

    public function test_parse_angles_handles_markdown_code_block()
    {
        $json = json_encode([
            ['angle' => 'MD Angle', 'keyword' => 'md_kw'],
        ]);
        $angles = $this->parseAnglesStatic("```json\n$json\n```", 1);

        $this->assertCount(1, $angles);
        $this->assertEquals('MD Angle', $angles[0]['angle']);
    }

    public function test_parse_angles_fills_short_array_with_defaults()
    {
        $angles = $this->parseAnglesStatic('[{"angle": "Only One"}]', 3);

        $this->assertCount(3, $angles);
        $this->assertEquals('Only One', $angles[0]['angle']);
        $this->assertNotEmpty($angles[1]['angle']);
        $this->assertNotEmpty($angles[2]['angle']);
    }

    public function test_fallback_angles_returns_correct_count()
    {
        $angles = $this->fallbackAnglesStatic(5);

        $this->assertCount(5, $angles);
    }

    public function test_fallback_angles_limits_to_available_defaults()
    {
        $angles = $this->fallbackAnglesStatic(10);

        $this->assertCount(5, $angles);
    }

    public function test_fallback_angles_contains_required_keys()
    {
        $angles = $this->fallbackAnglesStatic(3);

        foreach ($angles as $angle) {
            $this->assertArrayHasKey('angle', $angle);
            $this->assertArrayHasKey('keyword', $angle);
            $this->assertArrayHasKey('description', $angle);
            $this->assertNotEmpty($angle['angle']);
        }
    }

    public function test_parse_angles_handles_title_key_as_fallback()
    {
        $angles = $this->parseAnglesStatic(json_encode([
            ['title' => 'Via Title Key'],
            ['name' => 'Via Name Key'],
        ]), 2);

        $this->assertEquals('Via Title Key', $angles[0]['angle']);
        $this->assertEquals('Via Name Key', $angles[1]['angle']);
    }

    public function test_parse_angles_handles_missing_optional_keys()
    {
        $angles = $this->parseAnglesStatic('[{"angle": "Minimal"}]', 1);

        $this->assertEquals('Minimal', $angles[0]['angle']);
        $this->assertEquals('', $angles[0]['keyword']);
        $this->assertEquals('', $angles[0]['description']);
    }
}
