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

    public function voiceToIntelligence(Request $request): JsonResponse
    {
        $request->validate([
            'audio' => 'required|file|mimetypes:audio/webm,audio/ogg,audio/mpeg,audio/mp4,video/webm,video/ogg,audio/wav,audio/x-wav|max:25600', // 25MB max
            'type' => 'required|in:note,tasks',
        ]);

        $file = $request->file('audio');
        $apiKey = config('services.openai.key');

        if (!$apiKey) {
            return response()->json(['success' => false, 'message' => 'AI not configured'], 503);
        }

        try {
            // 1. Transcribe Audio (Whisper)
            $transcriptionResponse = Http::withToken($apiKey)
                ->attach('file', file_get_contents($file->getPathname()), $file->getClientOriginalName())
                ->post('https://api.openai.com/v1/audio/transcriptions', [
                    'model' => 'whisper-1',
                    'language' => 'en',
                ]);

            if ($transcriptionResponse->failed()) {
                Log::error('Whisper API Error: ' . $transcriptionResponse->body());
                return response()->json(['success' => false, 'message' => 'Transcription failed'], 500);
            }

            $transcript = $transcriptionResponse->json('text');

            if (empty($transcript)) {
                return response()->json(['success' => false, 'message' => 'No speech detected'], 422);
            }

            // 2. Process based on Type
            if ($request->type === 'note') {
                $note = Task::create([
                    'tenant_id' => auth()->user()->tenant_id,
                    'user_id' => auth()->id(),
                    'title' => 'Voice Memo - ' . now()->format('M d, H:i'),
                    'description' => $transcript,
                    'type' => 'note',
                    'status' => 'pending'
                ]);

                return response()->json(['success' => true, 'note' => $note]);
            } 
            
            if ($request->type === 'tasks') {
                // 3. Extract Tasks via GPT-4o
                $aiResponse = Http::withToken($apiKey)->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => "You are an executive assistant. Analyze the meeting transcript. 
                            1. Identify the main topic/goal for the Project Title.
                            2. Extract clear, actionable steps as Tasks.
                            Return JSON ONLY: { \"title\": \"Project Title\", \"tasks\": [\"Action Item 1\", \"Action Item 2\"] }"
                        ],
                        ['role' => 'user', 'content' => $transcript]
                    ],
                    'response_format' => ['type' => 'json_object']
                ]);

                if ($aiResponse->successful()) {
                    $content = $aiResponse->json('choices.0.message.content');
                    $data = json_decode($content, true);

                    $parentTask = Task::create([
                        'tenant_id' => auth()->user()->tenant_id,
                        'user_id' => auth()->id(),
                        'title' => $data['title'] ?? 'Voice Meeting Results',
                        'description' => "Transcript Summary:\n" . substr($transcript, 0, 500) . (strlen($transcript)>500 ? '...' : ''),
                        'type' => 'task',
                        'status' => 'pending'
                    ]);

                    if (!empty($data['tasks']) && is_array($data['tasks'])) {
                        foreach ($data['tasks'] as $step) {
                            Task::create([
                                'tenant_id' => auth()->user()->tenant_id,
                                'user_id' => auth()->id(),
                                'parent_id' => $parentTask->id,
                                'title' => $step,
                                'type' => 'task',
                                'status' => 'pending'
                            ]);
                        }
                    }

                    return response()->json(['success' => true, 'task' => $parentTask->load('subtasks')]);
                }
            }

            return response()->json(['success' => false, 'message' => 'Processing failed'], 500);

        } catch (\Exception $e) {
            Log::error('Voice processing error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function saveAudioOnly(Request $request): JsonResponse
    {
        $request->validate([
            'audio' => 'required|file|mimes:webm,mp3,mp4,wav,m4a|max:25600',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $file = $request->file('audio');
            $filename = 'media/voice-' . \Illuminate\Support\Str::uuid() . '.' . $file->getClientOriginalExtension();
            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, file_get_contents($file->getPathname()));
            $url = \Illuminate\Support\Facades\Storage::disk('public')->url($filename);

            $asset = \App\Models\MediaAsset::create([
                'tenant_id' => auth()->user()->tenant_id,
                'user_id' => auth()->id(),
                'name' => $request->title,
                'url' => $url,
                'type' => 'audio',
                'source' => 'Voice Recorder',
                'prompt' => $request->description,
                'metadata' => [
                    'filename' => $filename,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]
            ]);

            return response()->json(['success' => true, 'asset' => $asset]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Audio save error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function storeGhostDemo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'events' => 'required|array',
        ]);

        try {
            $filename = 'demos/ghost-' . \Illuminate\Support\Str::uuid() . '.json';
            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, json_encode($validated['events']));

            $document = \App\Models\Document::create([
                'tenant_id' => auth()->user()->tenant_id,
                'user_id' => auth()->id(),
                'name' => $validated['title'],
                'type' => 'GHOST_DEMO',
                'category' => 'Demos',
                'path' => $filename,
                'size' => strlen(json_encode($validated['events'])),
                'status' => 'completed',
                'content' => '', // Content is in file
            ]);

            return response()->json([
                'success' => true,
                'demo' => [
                    'id' => $document->id,
                    'title' => $document->name,
                    'created_at' => $document->created_at->toIso8601String(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Ghost save error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function showGhostDemo(\App\Models\Document $document)
    {
        if ($document->type !== 'GHOST_DEMO') abort(404);
        
        $events = json_decode(\Illuminate\Support\Facades\Storage::disk('public')->get($document->path), true);
        
        return view('tasks.ghost-player', compact('document', 'events'));
    }
}