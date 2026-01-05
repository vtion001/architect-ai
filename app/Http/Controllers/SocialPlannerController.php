<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Content;

class SocialPlannerController extends Controller
{
    public function __construct(
        protected \App\Services\ResearchService $researchService,
        protected \App\Models\Tenant $tenant
    ) {
        $this->tenant = app(\App\Models\Tenant::class);
    }

    public function index()
    {
        $scheduledPosts = Content::where('type', 'social-post')
            ->whereIn('status', ['scheduled', 'published'])
            ->latest()
            ->limit(20)
            ->get();

        $baseUrl = rtrim(config('app.url'), '/');
        
        // Load connected status from Encrypted Metadata
        $metadata = $this->tenant->metadata ?? [];

        $socialConfig = [
            'facebook' => [
                'clientId' => config('services.facebook.client_id'),
                'redirectUri' => config('services.facebook.redirect') ?: $baseUrl . "/social/callback/facebook",
                'connected' => isset($metadata['facebook_access_token']) && !empty($metadata['facebook_access_token']),
                'count' => Content::where('type', 'social-post')->where('options->platform', 'facebook')->count(),
            ],
            'instagram' => [
                'clientId' => config('services.instagram.client_id'),
                'redirectUri' => config('services.instagram.redirect') ?: $baseUrl . "/social/callback/instagram",
                'connected' => isset($metadata['instagram_access_token']) && !empty($metadata['instagram_access_token']),
                'count' => Content::where('type', 'social-post')->where('options->platform', 'instagram')->count(),
            ],
            'linkedin' => [
                'clientId' => config('services.linkedin.client_id'),
                'redirectUri' => config('services.linkedin.redirect') ?: $baseUrl . "/social/callback/linkedin",
                'connected' => isset($metadata['linkedin_access_token']) && !empty($metadata['linkedin_access_token']),
                'count' => Content::where('type', 'social-post')->where('options->platform', 'linkedin')->count(),
            ],
            'twitter' => [
                'clientId' => config('services.twitter.client_id'),
                'redirectUri' => config('services.twitter.redirect') ?: $baseUrl . "/social/callback/twitter",
                'connected' => isset($metadata['twitter_access_token']) && !empty($metadata['twitter_access_token']),
                'count' => Content::where('type', 'social-post')->where('options->platform', 'twitter')->count(),
            ],
        ];

        // Calculate total for percentages
        $totalSocialPosts = Content::where('type', 'social-post')->count() ?: 1;
        foreach ($socialConfig as $key => $config) {
            $socialConfig[$key]['percentage'] = number_format(($config['count'] / $totalSocialPosts) * 100, 1);
        }

        return view('social-planner.social-planner', compact('scheduledPosts', 'socialConfig'));
    }

    // ... (getSuggestions, store methods remain same)

    public function handleCallback(Request $request, $platform)
    {
        $code = $request->get('code');
        
        if (!$code) {
            return response()->json(['error' => 'No code provided'], 400);
        }
        $clientId = config("services.$platform.client_id");
        $clientSecret = config("services.$platform.client_secret");

        $baseUrl = rtrim(config('app.url'), '/');
        $redirectUri = config("services.$platform.redirect") ?: $baseUrl . "/social/callback/$platform";

        try {
            $response = \Illuminate\Support\Facades\Http::get("https://graph.facebook.com/v18.0/oauth/access_token", [
                'client_id' => $clientId,
                'redirect_uri' => $redirectUri,
                'client_secret' => $clientSecret,
                'code' => $code
            ]);
            
            $data = $response->json();

            if (isset($data['access_token'])) {
                $metadata = $this->tenant->metadata ?? [];
                
                // Store in Encrypted Metadata (automatically handled by Model mutator)
                $metadata["{$platform}_access_token"] = $data['access_token'];
                
                if ($platform === 'facebook') {
                    $metadata['instagram_access_token'] = $data['access_token'];
                }

                $this->tenant->update(['metadata' => $metadata]);
                
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
        $metadata = $this->tenant->metadata ?? [];
        $accessToken = $metadata['facebook_access_token'] ?? null;

        if (!$accessToken) {
             return response()->json(['pages' => []]);
        }

        $allPages = [];
        $url = "https://graph.facebook.com/v18.0/me/accounts";
        $params = [
            'fields' => 'id,name,category,access_token,instagram_business_account{id,name,username,profile_picture_url}',
            'limit' => 100,
            'access_token' => $accessToken
        ];

        try {
            $response = \Illuminate\Support\Facades\Http::get($url, $params);
            $data = $response->json();
            
            if (isset($data['data'])) {
                $allPages = array_merge($allPages, $data['data']);
            }

            while (isset($data['paging']['next'])) {
                $response = \Illuminate\Support\Facades\Http::get($data['paging']['next']);
                $data = $response->json();
                
                if (isset($data['data'])) {
                    $allPages = array_merge($allPages, $data['data']);
                }
            }

            \Illuminate\Support\Facades\Log::info("Fetched " . count($allPages) . " Facebook pages from Encrypted Grid Store");
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to fetch Facebook pages: " . $e->getMessage());
            return response()->json(['pages' => [], 'error' => $e->getMessage()]);
        }

        return response()->json([
            'pages' => $allPages
        ]);
    }
}
