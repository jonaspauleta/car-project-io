<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifyEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:verify {email : The email address to verify} {--force : Force verification even if already verified}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually verify a user email address';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $force = $this->option('force');

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

        // Check if already verified
        if ($user->hasVerifiedEmail() && !$force) {
            $this->warn("Email {$email} is already verified.");
            $this->info("Use --force flag to re-verify.");
            return self::SUCCESS;
        }

        // Verify the email
        try {
            DB::beginTransaction();
            
            $user->markEmailAsVerified();
            
            DB::commit();
            
            $this->info("✅ Email verified successfully!");
            $this->table(
                ['Field', 'Value'],
                [
                    ['User ID', $user->id],
                    ['Name', $user->name],
                    ['Email', $user->email],
                    ['Verified At', $user->email_verified_at?->format('Y-m-d H:i:s') ?? 'Not verified'],
                    ['Created At', $user->created_at->format('Y-m-d H:i:s')],
                ]
            );

            return self::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Failed to verify email: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
