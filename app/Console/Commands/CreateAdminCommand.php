<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        while (true) {
            $email = $this->ask("Admin email: ");
            $email = trim($email);
            $exist = User::query()->where('email', $email)->exists();
            if (!$exist) {
                break;
            }
            $this->error('Email already exists!');
        }

        $name = $this->ask("Admin name: ");
        $password = $this->secret("What is the password: ");
        User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => User::ROLE_ADMIN,
        ]);
        $this->output->success("Create user successfully!");
        return Command::SUCCESS;
    }
}
