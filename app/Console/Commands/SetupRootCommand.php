<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;
use function Laravel\Prompts\info;

class SetupRootCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup-root';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Securely setup or reset the Root account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        info('Root Account Setup');

        if (User::role('Root')->exists()) {
            \Laravel\Prompts\error('A Root account already exists. Only one Root account is permitted.');
            return self::FAILURE;
        }

        $name = text(
            label: 'What is the name of the Root user?',
            placeholder: 'Super Admin',
            default: 'Super Admin',
            required: true
        );

        $email = text(
            label: 'What is the email address for the Root user?',
            placeholder: 'root@example.com',
            required: true,
            validate: fn (string $value) => match (true) {
                ! filter_var($value, FILTER_VALIDATE_EMAIL) => 'Please enter a valid email address.',
                default => null
            }
        );

        $pass = password(
            label: 'Enter a strong password for the Root user',
            placeholder: 'minimum 8 characters',
            required: true,
            validate: fn (string $value) => match (true) {
                strlen($value) < 8 => 'The password must be at least 8 characters.',
                default => null
            }
        );

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($pass),
            ]
        );

        $user->assignRole('Root');

        info('Root account has been successfully setup and assigned the Root role.');
        return self::SUCCESS;
    }
}
