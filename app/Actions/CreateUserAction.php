<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateUserAction
{
    private $createDefaultWidgetsAction;
    
    public function __construct(CreateDefaultWidgetsAction $createDefaultWidgetsAction)
    {
        $this->createDefaultWidgetsAction = $createDefaultWidgetsAction;
    }

    public function execute(string $name, string $email, string $password): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'verification_token' => Str::random(100)
        ]);

        $this->createDefaultWidgetsAction->execute($user->id);

        return $user;
    }
}
