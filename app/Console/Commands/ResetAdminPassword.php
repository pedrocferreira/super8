<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-admin-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resets the password for the admin user.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $admin = User::where('email', 'admin@admin.com')->first();

        if ($admin) {
            $admin->password = Hash::make('admin1234');
            $admin->save();
            $this->info('Admin password has been reset successfully. The new password is "admin1234".');
        } else {
            $this->error('Admin user not found.');
        }
    }
}
