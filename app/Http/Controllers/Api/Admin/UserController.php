<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $users = User::query()
            ->latest('id')
            ->paginate(max(1, min($request->integer('per_page', 15), 50)));

        return UserResource::collection($users);
    }

    public function show(User $user): UserResource
    {
        return UserResource::make($user);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        return UserResource::make($user)->response()->setStatusCode(201);
    }

    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $data = $request->validated();

        if (array_key_exists('password', $data) && blank($data['password'])) {
            unset($data['password']);
        }

        if ($request->user()->is($user) && array_key_exists('role', $data)) {
            abort(403, 'Anda tidak dapat mengubah role akun sendiri.');
        }

        $user->update($data);

        // Role/password baru hanya berlaku lewat token baru.
        if (array_key_exists('role', $data) || array_key_exists('password', $data)) {
            $user->tokens()->delete();
        }

        return UserResource::make($user->fresh());
    }

    public function destroy(Request $request, User $user): Response
    {
        if ($request->user()->is($user)) {
            abort(403, 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->noContent();
    }
}
