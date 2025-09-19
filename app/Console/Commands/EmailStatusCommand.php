<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class EmailStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:status {email? : The email address to check} {--all : Show all users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check email verification status for user(s)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $showAll = $this->option('all');

        if ($showAll) {
            return $this->showAllUsers();
        }

        if (!$email) {
            $this->error('Please provide an email address or use --all flag');
            return self::FAILURE;
        }

        return $this->showSingleUser($email);
    }

    private function showSingleUser(string $email): int
    {
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error("Invalid email format: {$email}");
            return self::FAILURE;
        }

        // Find the user
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User not found with email: {$email}");
            return self::FAILURE;
        }

        $isVerified = $user->hasVerifiedEmail();
        $status = $isVerified ? '✅ Verified' : '❌ Not Verified';

        $this->info("Email Status for: {$email}");
        $this->table(
            ['Field', 'Value'],
            [
                ['User ID', $user->id],
                ['Name', $user->name],
                ['Email', $user->email],
                ['Status', $status],
                ['Verified At', $user->email_verified_at?->format('Y-m-d H:i:s') ?? 'Never'],
                ['Created At', $user->created_at->format('Y-m-d H:i:s')],
                ['Updated At', $user->updated_at->format('Y-m-d H:i:s')],
            ]
        );

        return self::SUCCESS;
    }

    private function showAllUsers(): int
    {
        $users = User::orderBy('created_at', 'desc')->get();

        if ($users->isEmpty()) {
            $this->warn('No users found in the system.');
            return self::SUCCESS;
        }

        $this->info('All Users Email Status:');

        $tableData = $users->map(function (User $user) {
            $isVerified = $user->hasVerifiedEmail();
            $status = $isVerified ? '✅ Verified' : '❌ Not Verified';
            
            return [
                $user->id,
                $user->name,
                $user->email,
                $status,
                $user->email_verified_at?->format('Y-m-d H:i:s') ?? 'Never',
                $user->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $this->table(
            ['ID', 'Name', 'Email', 'Status', 'Verified At', 'Created At'],
            $tableData->toArray()
        );

        $verifiedCount = $users->filter(fn(User $user) => $user->hasVerifiedEmail())->count();
        $totalCount = $users->count();

        $this->info("Summary: {$verifiedCount}/{$totalCount} users have verified emails");

        return self::SUCCESS;
    }
}
