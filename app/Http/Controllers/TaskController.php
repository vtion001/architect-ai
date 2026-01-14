<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Task::where('tenant_id', auth()->user()->tenant_id)
            ->where('user_id', auth()->id())
            ->whereNull('parent_id') // Top level only
            ->with(['subtasks', 'category'])
            ->orderBy('status', 'asc') // pending first
            ->latest();

        if ($request->has('trashed')) {
            $tasks = $query->onlyTrashed()->get();
        } else {
            $tasks = $query->get();
        }

        $categories = TaskCategory::where('tenant_id', auth()->user()->tenant_id)
            ->where('user_id', auth()->id())
            ->get();

        return response()->json(['tasks' => $tasks, 'categories' => $categories]);
    }

    public function restore(string $id): JsonResponse
    {
        $task = Task::where('tenant_id', auth()->user()->tenant_id)
            ->where('user_id', auth()->id())
            ->onlyTrashed()
            ->findOrFail($id);

        $task->restore();

        return response()->json(['success' => true]);
    }

    public function forceDelete(string $id): JsonResponse
    {
        $task = Task::where('tenant_id', auth()->user()->tenant_id)
            ->where('user_id', auth()->id())
            ->onlyTrashed()
            ->findOrFail($id);

        $task->forceDelete();

        return response()->json(['success' => true]);
    }

    public function store(Request $request): JsonResponse
    {
        Log::info('Task Store Request', $request->all());

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in_progress,completed',
            'priority' => 'nullable|in:low,medium,high',
            'due_date' => 'nullable|date',
            'type' => 'nullable|in:task,note',
            'parent_id' => 'nullable|exists:tasks,id',
            'category_id' => 'nullable|exists:task_categories,id',
            'alarm_enabled' => 'nullable|boolean',
            'alarm_sound' => 'nullable|string',
        ]);

        $task = Task::create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id' => auth()->id(),
            ...$validated,
        ]);

        return response()->json(['success' => true, 'task' => $task->load(['subtasks', 'category'])]);
    }

    public function update(Request $request, Task $task): JsonResponse
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:pending,in_progress,completed',
            'priority' => 'sometimes|in:low,medium,high',
            'due_date' => 'nullable|date',
            'type' => 'nullable|in:task,note',
            'category_id' => 'nullable|exists:task_categories,id',
            'alarm_enabled' => 'nullable|boolean',
            'alarm_sound' => 'nullable|string',
        ]);

        $task->update($validated);

        return response()->json(['success' => true, 'task' => $task->load(['subtasks', 'category'])]);
    }

    public function destroy(Task $task): JsonResponse
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json(['success' => true]);
    }

    // Category Management
    public function storeCategory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'color' => 'required|string|max:7',
        ]);

        $category = TaskCategory::create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id' => auth()->id(),
            ...$validated,
        ]);

        return response()->json(['success' => true, 'category' => $category]);
    }

    public function destroyCategory(TaskCategory $category): JsonResponse
    {
        if ($category->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $category->delete();
        return response()->json(['success' => true]);
    }

    public function breakdown(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        try {
            $apiKey = config('services.openai.key');
            if (!$apiKey) {
                return response()->json(['message' => 'AI service not configured'], 503);
            }

            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model', 'gpt-4o-mini'),
                    'messages' => [
                        [
                            'role' => 'system', 
                            'content' => 'You are a productivity expert. Analyze the user request and generate a concise but descriptive title for the project/task, and then break it down into concrete, actionable steps. Return ONLY a valid JSON object with two keys: "title" (string) and "steps" (array of strings). Do not include markdown formatting.'
                        ],
                        [
                            'role' => 'user', 
                            'content' => $validated['content']
                        ],
                    ],
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                // Clean up any markdown blocks if the model ignores instruction
                $content = str_replace(['```json', '```'], '', $content);
                $data = json_decode(trim($content), true);
                
                // Backward compatibility / Fallback
                if (!isset($data['title']) || !isset($data['steps'])) {
                    if (is_array($data) && array_is_list($data)) {
                        // It returned just the list of steps
                        $data = [
                            'title' => substr($validated['content'], 0, 50) . '...',
                            'steps' => $data
                        ];
                    } else {
                        // Failed to parse, probably plain text
                        $data = [
                            'title' => 'New Project',
                            'steps' => []
                        ];
                    }
                }

                return response()->json([
                    'success' => true, 
                    'title' => $data['title'],
                    'steps' => $data['steps']
                ]);
            }

            return response()->json(['success' => false, 'message' => 'AI generation failed'], 500);

        } catch (\Exception $e) {
            Log::error('Task breakdown error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }
}