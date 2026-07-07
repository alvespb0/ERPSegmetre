<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function update(array $dados, int $id): bool
    {
        $user = User::withTrashed()->findOrFail($id);

        $payload = [
            'name' => $dados['name'],
            'email' => $dados['email'],
            'tipo' => $dados['tipo'],
        ];

        if (! empty($dados['password'])) {
            $payload['password'] = Hash::make($dados['password']);
        }

        return $user->update($payload);
    }

    public function destroy(int $id): bool
    {
        $user = User::findOrFail($id);

        return (bool) $user->delete();
    }

    public function restore(int $id): bool
    {
        return (bool) User::withTrashed()->findOrFail($id)->restore();
    }
}
