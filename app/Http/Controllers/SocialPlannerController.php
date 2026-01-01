<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Content;

class SocialPlannerController extends Controller
{
    public function __construct(
        protected \App\Services\ResearchService $researchService
    ) {}

    public function index()
    {
        $scheduledPosts = Content::where('type', 'social-post')
            ->whereIn('status', ['scheduled', 'published'])
            ->latest()
            ->limit(20)
            ->get();

        $baseUrl = rtrim(config('app.url'), '/');
        
        // Load connected status from our "database" (the JSON file)
        $path = storage_path('app/social_tokens.json');
        $tokens = [];
        if (file_exists($path)) {
            $tokens = json_decode(file_get_contents($path), true);
        }

        $socialConfig = [
            'facebook' => [
                'clientId' => config('services.facebook.client_id'),
                'redirectUri' => config('services.facebook.redirect') ?: $baseUrl . "/social/callback/facebook",
                'connected' => isset($tokens['facebook']) && !empty($tokens['facebook']),
                'count' => Content::where('type', 'social-post')->where('options->platform', 'facebook')->count(),
            ],
            'instagram' => [
                'clientId' => config('services.instagram.client_id'),
                'redirectUri' => config('services.instagram.redirect') ?: $baseUrl . "/social/callback/instagram",
                'connected' => isset($tokens['instagram']) && !empty($tokens['instagram']),
                'count' => Content::where('type', 'social-post')->where('options->platform', 'instagram')->count(),
            ],
            'linkedin' => [
                'clientId' => config('services.linkedin.client_id'),
                'redirectUri' => config('services.linkedin.redirect') ?: $baseUrl . "/social/callback/linkedin",
                'connected' => isset($tokens['linkedin']) && !empty($tokens['linkedin']),
                'count' => Content::where('type', 'social-post')->where('options->platform', 'linkedin')->count(),
            ],
            'twitter' => [
                'clientId' => config('services.twitter.client_id'),
                'redirectUri' => config('services.twitter.redirect') ?: $baseUrl . "/social/callback/twitter",
                'connected' => isset($tokens['twitter']) && !empty($tokens['twitter']),
                'count' => Content::where('type', 'social-post')->where('options->platform', 'twitter')->count(),
            ],
        ];

        // Calculate total for percentages
        $totalSocialPosts = Content::where('type', 'social-post')->count() ?: 1;
        foreach ($socialConfig as $key => $config) {
            $socialConfig[$key]['percentage'] = number_format(($config['count'] / $totalSocialPosts) * 100, 1);
        }

        return view('social-planner.index', compact('scheduledPosts', 'socialConfig'));
    }

    public function getSuggestions(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|min:3',
        ]);

        $suggestions = $this->researchService->suggestSocialMediaTopics($request->topic);

        return response()->json([
            'suggestions' => $suggestions
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'scheduled_at' => 'required|date',
            'platform' => 'nullable|string'
        ]);

        $content = new Content();
        $content->title = 'Scheduled Post - ' . now()->format('M d');
        $content->topic = 'Social Post'; // Generic topic
        $content->type = 'social-post';
        $content->status = 'scheduled';
        $content->result = $request->content;
        
        $options = [
            'scheduled_at' => $request->scheduled_at,
            'platform' => $request->platform ?? 'generic'
        ];
        
        $content->options = $options;
        $content->save();

        return response()->json([
            'success' => true,
            'message' => 'Post scheduled successfully'
        ]);
    }
    public function handleCallback(Request $request, $platform)
    {
        $code = $request->get('code');
        
        if (!$code) {
            return response()->json(['error' => 'No code provided'], 400);
        }
        $clientId = config("services.$platform.client_id");
        $clientSecret = config("services.$platform.client_secret");

        // Use APP_URL to ensure consistency (especially with ngrok/https)
        $baseUrl = rtrim(config('app.url'), '/');
        $redirectUri = config("services.$platform.redirect") ?: $baseUrl . "/social/callback/$platform";

        try {
            if ($platform === 'facebook') {
                $response = \Illuminate\Support\Facades\Http::get("https://graph.facebook.com/v18.0/oauth/access_token", [
                    'client_id' => $clientId,
                    'redirect_uri' => $redirectUri,
                    'client_secret' => $clientSecret,
                    'code' => $code
                ]);
            } else {
                // Post for others if needed
                $response = \Illuminate\Support\Facades\Http::asForm()->post("https://graph.facebook.com/v18.0/oauth/access_token", [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => $redirectUri,
                    'code' => $code
                ]);
            }
            
            $data = $response->json();

            if (isset($data['access_token'])) {
                // Store token in a JSON file for this demo
                $path = storage_path('app/social_tokens.json');
                $tokens = [];
                if (file_exists($path)) {
                    $tokens = json_decode(file_get_contents($path), true);
                }
                $tokens[$platform] = $data['access_token'];
                file_put_contents($path, json_encode($tokens, JSON_PRETTY_PRINT));
                
                return response()->view('social-planner.callback', ['platform' => $platform]);
            } else {
                \Illuminate\Support\Facades\Log::error("Social Auth Error: " . json_encode($data));
                return response()->json(['error' => 'Failed to get token', 'details' => $data], 400);
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error($e->getMessage());
            return response()->json(['error' => 'Exception during token exchange'], 500);
        }
    }

    public function getFacebookPages()
    {
        $path = storage_path('app/social_tokens.json');
        if (!file_exists($path)) {
             return response()->json(['pages' => []]);
        }
        
        $tokens = json_decode(file_get_contents($path), true);
        $accessToken = $tokens['facebook'] ?? null;

        if (!$accessToken) {
             return response()->json(['pages' => []]);
        }

        $response = \Illuminate\Support\Facades\Http::get("https://graph.facebook.com/v18.0/me/accounts?access_token=$accessToken");
        $data = $response->json();
        
        return response()->json([
            'pages' => $data['data'] ?? []
        ]);
    }
}
