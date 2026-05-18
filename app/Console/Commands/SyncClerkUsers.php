<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Clerk\Backend\ClerkBackend;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SyncClerkUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clerk:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all registered users from Clerk to MongoDB';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $clerkSecret = env('CLERK_SECRET_KEY');

        if (!$clerkSecret) {
            $this->error('CLERK_SECRET_KEY is not set in .env file.');
            return 1;
        }

        $this->info('Connecting to Clerk API...');

        try {
            $clerk = ClerkBackend::builder()
                ->setSecurity($clerkSecret)
                ->build();

            // Fetch users from Clerk
            $response = $clerk->users->list();
            $clerkUsers = $response->userList ?? [];

            if (empty($clerkUsers)) {
                $this->info('No users found in Clerk.');
                return 0;
            }

            $count = 0;
            foreach ($clerkUsers as $clerkUser) {
                $userId = $clerkUser->id;
                
                // Get primary email
                $email = '';
                foreach ($clerkUser->emailAddresses as $emailObj) {
                    if ($emailObj->id === $clerkUser->primaryEmailAddressId) {
                        $email = $emailObj->emailAddress;
                        break;
                    }
                }

                if (!$email && count($clerkUser->emailAddresses) > 0) {
                    $email = $clerkUser->emailAddresses[0]->emailAddress;
                }

                $name = trim(($clerkUser->firstName ?? '') . ' ' . ($clerkUser->lastName ?? ''));
                if (!$name) {
                    $name = 'Clerk User';
                }

                // Sync user to MongoDB
                $user = User::where('clerk_id', $userId)->first();
                
                if (!$user) {
                    $user = User::where('email', $email)->first();
                    
                    if ($user) {
                        $user->clerk_id = $userId;
                        $user->save();
                        $this->line("Updated existing user: <info>{$email}</info>");
                    } else {
                        User::create([
                            'clerk_id' => $userId,
                            'name' => $name,
                            'email' => $email,
                            'password' => Hash::make(Str::random(24)),
                            'role' => 'user',
                        ]);
                        $this->line("Imported new user: <info>{$email}</info>");
                        $count++;
                    }
                } else {
                    $this->line("User already exists: <comment>{$email}</comment>");
                }
            }

            $this->info("Successfully processed " . count($clerkUsers) . " users.");
            $this->info("New users imported: {$count}");

        } catch (\Exception $e) {
            $this->error('Error syncing users: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
