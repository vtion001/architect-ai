<?php

// Architect API OpenAPI JSON route
use Illuminate\Support\Facades\Route;

Route::get('/api-docs.json', function () {
    // You can move this to a controller if preferred
    $openApi = json_decode(file_get_contents(base_path('architect-ai-docs/openapi.json')), true);

    return response()->json($openApi);
})->name('api-docs.json');
