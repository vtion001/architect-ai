<?php

declare(strict_types=1);

namespace Tests\Unit\View\Composers;

use App\View\Composers\ContentViewerComposer;
use Illuminate\View\View;
use Mockery;
use Tests\TestCase;

/**
 * Unit tests for ContentViewerComposer.
 */
class ContentViewerComposerTest extends TestCase
{
    private ContentViewerComposer $composer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->composer = new ContentViewerComposer;
    }

    /** @test */
    public function it_can_be_instantiated(): void
    {
        $this->assertInstanceOf(ContentViewerComposer::class, $this->composer);
    }

    /** @test */
    public function compose_sets_empty_posts_data_when_no_content(): void
    {
        $view = Mockery::mock(View::class);
        $view->shouldReceive('getData')->andReturn([]);
        $view->shouldReceive('with')->with('postsData', [])->once();

        $this->composer->compose($view);
    }

    /** @test */
    public function compose_processes_content_with_separator_segments(): void
    {
        $content = (object) [
            'result' => "Post 1 content\n---\nPost 2 content\n---\nPost 3 content",
            'options' => ['count' => 3],
        ];

        $view = Mockery::mock(View::class);
        $view->shouldReceive('getData')->andReturn([
            'content' => $content,
            'publishedIndexes' => [],
        ]);
        $view->shouldReceive('with')->withArgs(function ($key, $value) {
            return $key === 'postsData' &&
                   is_array($value) &&
                   count($value) === 3;
        })->once();

        $this->composer->compose($view);
    }

    /** @test */
    public function compose_handles_numbered_list_format(): void
    {
        $content = (object) [
            'result' => "1. First post\n2. Second post\n3. Third post",
            'options' => ['count' => 3],
        ];

        $view = Mockery::mock(View::class);
        $view->shouldReceive('getData')->andReturn([
            'content' => $content,
            'publishedIndexes' => [],
        ]);
        $view->shouldReceive('with')->withArgs(function ($key, $value) {
            return $key === 'postsData' &&
                   is_array($value);
        })->once();

        $this->composer->compose($view);
    }

    /** @test */
    public function compose_handles_global_hashtags(): void
    {
        $content = (object) [
            'result' => "Post 1 content\n---\nPost 2 content\n---\n#hashtag1 #hashtag2",
            'options' => ['count' => 2],
        ];

        $view = Mockery::mock(View::class);
        $view->shouldReceive('getData')->andReturn([
            'content' => $content,
            'publishedIndexes' => [],
        ]);
        $view->shouldReceive('with')->withArgs(function ($key, $value) {
            if ($key === 'postsData' && is_array($value)) {
                // Both posts should contain the hashtags
                foreach ($value as $post) {
                    if (! str_contains($post['raw'], '#hashtag1')) {
                        return false;
                    }
                }

                return true;
            }

            return false;
        })->once();

        $this->composer->compose($view);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
