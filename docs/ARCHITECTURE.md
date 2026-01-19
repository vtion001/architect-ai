# Application Architecture

## Overview

This document describes the modular architecture of the ArchitGrid application,
focusing on reusable services, maintainability patterns, and best practices.

---

## Directory Structure

```
app/
├── Contracts/                    # Interfaces (ContentGeneratorInterface, etc.)
├── DTOs/                         # Data Transfer Objects
│   └── ChatProcessingResult.php
├── Http/
│   ├── Controllers/
│   │   ├── Traits/
│   │   │   └── ConsumesTokens.php  # Reusable token consumption
│   │   └── ...
│   └── Middleware/
├── Models/
│   ├── Traits/
│   │   └── BelongsToTenant.php     # Multi-tenant scoping
│   └── ...
├── Providers/
│   ├── AppServiceProvider.php
│   └── AIServiceProvider.php       # AI service registration
├── Services/
│   ├── AI/                         # ⭐ Modular AI services
│   │   ├── OpenAIClient.php        # Centralized API client
│   │   └── PromptBuilder.php       # Prompt construction utilities
│   ├── ContentGenerators/          # Strategy pattern for content
│   │   ├── BaseContentGenerator.php
│   │   ├── SocialPostGenerator.php
│   │   └── BlogPostGenerator.php
│   ├── AiChatProcessingService.php
│   ├── TokenService.php
│   └── ...
│
config/
├── tokens.php                      # Token cost configuration
└── services.php                    # External service credentials

resources/views/
└── components/
    └── token-balance.blade.php     # Reusable token display
```

---

## Core Modules

### 1. AI Services (`app/Services/AI/`)

Centralized AI functionality that can be used across the application.

#### OpenAIClient

Handles all OpenAI API communication.

```php
use App\Services\AI\OpenAIClient;

$client = app(OpenAIClient::class);

$response = $client->chat([
    ['role' => 'system', 'content' => 'You are helpful.'],
    ['role' => 'user', 'content' => 'Hello!'],
], [
    'model' => 'gpt-4o-mini',
    'temperature' => 0.7,
    'max_tokens' => 2000,
]);

if ($response['success']) {
    echo $response['message'];
}
```

#### PromptBuilder

Constructs prompts with consistent formatting.

```php
use App\Services\AI\PromptBuilder;

$builder = app(PromptBuilder::class);

// Build complete prompt from components
$systemPrompt = $builder->build([
    'You are an expert content writer.',
    $builder->brandContext($brand),
    $builder->humanizeInstructions('Professional'),
    $builder->formattingInstructions('plain'),
]);

// Sanitize AI output
$cleanText = $builder->sanitize($aiResponse);
```

---

### 2. Token System (`app/Services/TokenService.php`)

Centralized token management with configurable costs.

#### Configuration (`config/tokens.php`)

```php
return [
    'costs' => [
        'content_generation' => env('TOKEN_COST_CONTENT', 10),
        'ai_chat_message' => env('TOKEN_COST_AI_CHAT', 5),
        'research' => env('TOKEN_COST_RESEARCH', 50),
        // Add more as needed
    ],
    'initial_grant' => env('TOKEN_INITIAL_GRANT', 1000),
];
```

#### Usage in Controllers

```php
use App\Http\Controllers\Traits\ConsumesTokens;

class MyController extends Controller
{
    use ConsumesTokens;

    public function generate()
    {
        // Use operation name from config
        if ($error = $this->consumeTokens('content_generation')) {
            return $error; // Returns 402 JSON response
        }

        // Proceed with generation...
    }
}
```

#### Usage in Services

```php
use App\Services\TokenService;

class MyService
{
    public function __construct(protected TokenService $tokenService) {}

    public function doSomething(User $user)
    {
        $cost = $this->tokenService->getCost('ai_chat_message');
        
        if (!$this->tokenService->consume($user, $cost, 'my_operation')) {
            throw new InsufficientTokensException($cost);
        }
    }
}
```

---

### 3. Multi-Tenant Isolation

#### BelongsToTenant Trait

Automatically scopes all queries to the current tenant.

```php
use App\Models\Traits\BelongsToTenant;

class MyModel extends Model
{
    use BelongsToTenant;
    
    // All queries automatically filtered by tenant_id
}
```

#### Explicit Tenant Validation

For sensitive operations, always validate explicitly:

```php
// Good: Explicit validation
KnowledgeBaseAsset::query()
    ->where('tenant_id', $agent->tenant_id)
    ->whereIn('id', $knowledgeIds)
    ->get();
```

---

### 4. Content Generation (Strategy Pattern)

All content generators extend `BaseContentGenerator` and implement:
- `getType(): string`
- `getSystemPrompt(array $options): string`
- `getUserPrompt(string $topic, ?string $context, array $options): string`

```php
class CustomGenerator extends BaseContentGenerator
{
    public function getType(): string
    {
        return 'custom';
    }

    public function getSystemPrompt(array $options = []): string
    {
        $tone = $options['tone'] ?? 'Professional';
        return "You are an expert writer. {$this->getHumanizeInstruction($tone)}";
    }

    public function getUserPrompt(string $topic, ?string $context = null, array $options = []): string
    {
        return "Write about: {$topic}\nContext: {$context}";
    }
}
```

---

### 5. Blade Components

#### Token Balance

```blade
{{-- Default badge style --}}
<x-token-balance />

{{-- Compact (icon + number) --}}
<x-token-balance variant="compact" />

{{-- Detailed card with low balance warning --}}
<x-token-balance variant="detailed" />

{{-- Custom balance value --}}
<x-token-balance :balance="500" />
```

---

## Adding New Features

### Adding a New Token Operation

1. Add cost to `config/tokens.php`:
```php
'costs' => [
    'my_new_operation' => env('TOKEN_COST_MY_OPERATION', 15),
],
```

2. Use in controller/service:
```php
$this->consumeTokens('my_new_operation');
// or
$tokenService->getCost('my_new_operation');
```

### Adding a New AI-Powered Feature

1. Inject the modular services:
```php
public function __construct(
    protected OpenAIClient $aiClient,
    protected PromptBuilder $promptBuilder,
    protected TokenService $tokenService
) {}
```

2. Build prompts using PromptBuilder
3. Call AI using OpenAIClient
4. Consume tokens using TokenService

---

## Testing

### Mocking AI Services

```php
$mock = $this->mock(OpenAIClient::class);
$mock->shouldReceive('chat')
    ->once()
    ->andReturn([
        'success' => true,
        'message' => 'Test response',
    ]);
```

### Mocking Token Service

```php
$mock = $this->mock(TokenService::class);
$mock->shouldReceive('consume')->andReturn(true);
$mock->shouldReceive('getCost')->andReturn(10);
```

---

## Best Practices

1. **Use Dependency Injection** - Never `new` up services directly
2. **Config Over Constants** - Use config files for customizable values
3. **Single Responsibility** - Each service does one thing well
4. **Trait Reuse** - Use traits for cross-cutting concerns (tokens, tenant scoping)
5. **Component Reuse** - Create Blade components for repeated UI patterns
