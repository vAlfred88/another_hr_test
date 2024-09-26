<?php

namespace App\Http\Controllers;

use App\Filters\EmailFilter;
use App\Filters\NameFilter;
use App\Models\User;
use Illuminate\Http\JsonResponse as JsonResponseAlias;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    public function index(Request $request): JsonResponseAlias
    {
        // Создаем ключ для кеша на основе параметров запроса
        $cacheKey = 'users_list';

        $users = Cache::remember($cacheKey, 600, function () {
            return app(Pipeline::class)
                ->send(User::query())
                ->through([
                    NameFilter::class,
                    EmailFilter::class,
                ])
                ->thenReturn()
                ->get(); // Paginate or use `get()` if you don't want pagination.
        });

        return response()->json($users);
    }

    public function show($id): JsonResponseAlias
    {
        // Кешируем профиль пользователя на 10 минут
        $user = Cache::remember("user_{$id}", 600, function () use ($id) {
            return User::findOrFail($id);
        });

        return response()->json($user);
    }

    public function update(Request $request, $id): JsonResponseAlias
    {
        // Очистка кеша для этого пользователя
        Cache::forget("user_{$id}");

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,'.$id,
        ]);

        $user->update($request->all());

        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }

    public function softDelete($id): JsonResponseAlias
    {
        // Очистка кеша для этого пользователя
        Cache::forget("user_{$id}");

        $user = User::findOrFail($id);
        $user->delete();  // Soft Delete

        return response()->json(['message' => 'User moved to trash']);
    }

    public function deleted(): JsonResponseAlias
    {
        $users = User::onlyTrashed()->get();

        return response()->json($users);
    }

    public function restore($id): JsonResponseAlias
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return response()->json(['message' => 'User restored successfully']);
    }

    public function forceDelete($id): JsonResponseAlias
    {
        // Очистка кеша для этого пользователя
        Cache::forget("user_{$id}");

        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();  // Полное удаление

        return response()->json(['message' => 'User permanently deleted']);
    }
}
