<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserController extends Controller
{
    protected function checkAdmin()
    {
        /** @var User $admin */
        $admin = auth()->user();
        if (!$admin->is_admin) {
            throw new AccessDeniedHttpException();
        }
    }

    public function store(Request $request): Response
    {
        $this->checkAdmin();
        $data = $request->validate([
            'email' => 'required|string|max:63|unique:users,email',
            'name' => 'required|string|max:255',
            'password' => 'required|string|max:20',
            'role' => [
                'required',
                Rule::in([
                    User::ROLE_INSTRUCTOR,
                    User::ROLE_USER,
                ]),
            ],
            'detail.phone' => 'nullable|string|max:31',
            'detail.id_card_number' => 'nullable|string|max:255',
        ]);

        /** @var User $user */
        $user = User::query()->create([
            'email' => $data['email'],
            'name' => $data['name'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        $user->detail()->updateOrCreate([
            'id_user' => $user->getKey(),
        ], $data['detail'] ?? []);

        $user->load('detail');

        return \response($user->toArray());
    }
}
