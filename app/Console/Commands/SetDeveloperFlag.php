<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SetDeveloperFlag extends Command
{
    protected $signature = 'app:set-developer-flag {email : The user email}';

    protected $description = 'Set is_developer flag for a user';

    public function handle(): int
    {
        $email = $this->argument('email');

        $user = User::withoutGlobalScopes()->where('email', $email)->first();

        if (! $user) {
            $this->error("User not found: $email");

            return 1;
        }

        $user->is_developer = 1;
        $user->save();

        $this->info("Set is_developer=true for {$user->id} ({$user->email})");

        return 0;
    }
}
