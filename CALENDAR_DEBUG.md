# Calendar Generation Debugging Guide

## Issue: 1-Click Calendar Not Working

### System Requirements

The calendar framework generator requires:
1. **Queue Worker Running** (if using database/redis queue)
2. **Sufficient Tokens** (50 tokens per generation)
3. **OpenAI API Key** configured
4. **8 Cards Output** (3 Educational, 2 Showcase, 2 Conversational, 1 Promotional)

---

## Debugging Steps

### Step 1: Check Browser Console

Open browser DevTools (F12) and look for logs:

```javascript
[Content Creator] Response: {...}
[Content Creator] Generator: framework
[Content Creator] Content Status: generating
[Content Creator] Content ID: <uuid>
[Content Creator] Starting polling for framework ID: <uuid>
[Content Creator] Poll attempt 1/60 for ID: <uuid>
```

**If you don't see these logs:**
- JavaScript error preventing execution
- Alpine.js not initialized
- Check for JS errors in console

---

### Step 2: Check Laravel Logs

```bash
tail -f storage/logs/laravel.log
```

Look for:

```
[ContentCreator] Framework generation requested
[ContentCreator] Content record created
[ContentCreator] Job dispatched to queue
GenerateCalendarFramework Job CREATED for Content ID: <id>
GenerateCalendarFramework Job STARTED for Content ID: <id>
```

**If "Job STARTED" doesn't appear:**
- Queue worker not running
- Queue connection misconfigured

---

### Step 3: Check Queue Configuration

```bash
php artisan config:show queue
```

or check `.env`:

```env
QUEUE_CONNECTION=database  # or 'sync', 'redis'
```

**Solutions:**

1. **If using `sync` (immediate execution):**
   - Jobs run immediately in same process
   - Should work without queue worker
   - Check Laravel logs for errors

2. **If using `database` or `redis`:**
   - Must run queue worker: `php artisan queue:work`
   - Check jobs table: `SELECT * FROM jobs ORDER BY created_at DESC;`
   - Check failed_jobs table: `SELECT * FROM failed_jobs ORDER BY failed_at DESC;`

---

### Step 4: Manually Start Queue Worker

```bash
# Terminal 1: Start queue worker
php artisan queue:work --tries=3 --timeout=600

# Terminal 2: Trigger generation
# Then watch Terminal 1 for job processing
```

**Expected output:**
```
[2026-02-05 12:34:56][abc123] Processing: App\Jobs\GenerateCalendarFramework
[2026-02-05 12:35:12][abc123] Processed:  App\Jobs\GenerateCalendarFramework
```

---

### Step 5: Check Token Balance

```bash
php artisan tinker
```

```php
$user = \App\Models\User::find(1); // Your user ID
$user->tenant->token_balance;
// Should be >= 50
```

**If insufficient tokens:**
```php
$user->tenant->update(['token_balance' => 1000]);
```

---

### Step 6: Test Job Manually

```bash
php artisan tinker
```

```php
$user = \App\Models\User::first();
$content = \App\Models\Content::create([
    'title' => 'Test Calendar',
    'topic' => 'Digital Marketing Strategy',
    'type' => 'framework_calendar',
    'context' => 'Test generation',
    'status' => 'generating',
    'options' => []
]);

\App\Jobs\GenerateCalendarFramework::dispatchSync($content, $user, 50);

// Check result
$content->fresh()->status; // Should be 'draft'
$content->fresh()->result; // Should contain JSON with 8 posts
```

---

### Step 7: Check OpenAI API

```bash
php artisan tinker
```

```php
$service = app(\App\Services\ContentService::class);
$result = $service->generateText('Digital Marketing', 'framework_calendar', 'Test context', ['generator' => 'framework']);

echo $result;
// Should output JSON with 8 posts
```

---

## Common Issues & Solutions

### Issue: "Generation failed" alert

**Cause:** Job threw exception

**Solution:**
1. Check `failed_jobs` table
2. Check Laravel logs for stack trace
3. Verify OpenAI API key: `php artisan config:show services.openai`

---

### Issue: Polling timeout (2 minutes)

**Cause:** Job taking too long or not starting

**Solution:**
1. Check queue worker is running
2. Increase timeout: `queue:work --timeout=900`
3. Check if job is stuck in queue: `SELECT * FROM jobs;`

---

### Issue: "Insufficient valid content generated"

**Cause:** OpenAI returned fewer than 8 posts

**Solution:**
- Job automatically retries (max 5 attempts)
- Check logs for retry attempts
- May need to adjust prompt in `FrameworkCalendarGenerator`

---

### Issue: Calendar shows but has 0 cards

**Cause:** JSON parsing error or incorrect data structure

**Solution:**
```bash
# Check content result
php artisan tinker
```

```php
$content = \App\Models\Content::latest()->first();
$result = json_decode($content->result, true);
print_r($result);

// Should have structure:
// [
//   'educational' => [3 posts],
//   'showcase' => [2 posts],
//   'conversational' => [2 posts],
//   'promotional' => [1 post]
// ]
```

---

## Force Sync Execution (Development)

If queue worker issues persist, force synchronous execution:

**In `ContentCreatorController.php`:**

```php
// Change from:
\App\Jobs\GenerateCalendarFramework::dispatch($content, auth()->user(), $tokenCost);

// To:
\App\Jobs\GenerateCalendarFramework::dispatchSync($content, auth()->user(), $tokenCost);
```

**Note:** This blocks the HTTP request until job completes (~30-60 seconds). Only for debugging.

---

## Success Indicators

✅ Browser console shows polling progress
✅ Laravel log shows "Job STARTED" and "Job COMPLETED"
✅ Content status changes from 'generating' → 'draft'
✅ 8 child Content records created with type 'social-post'
✅ Calendar sidebar displays 8 cards (3+2+2+1)

---

## Need Help?

1. Copy Laravel logs from last 5 minutes
2. Copy browser console output
3. Share queue configuration (`QUEUE_CONNECTION`)
4. Share error message from UI
