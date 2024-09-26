<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheTest extends TestCase
{
    /**
     * Тест кеширования списка пользователей с использованием Pipeline.
     */
    public function test_users_list_is_cached_with_pipeline()
    {
        // Создаем несколько тестовых пользователей
        $users = User::factory()->count(10)->create();

        // Авторизация пользователя
        $this->actingAs($users->first());

        // Генерируем ключ кеша
        $cacheKey = 'users_list';

        // Выполняем первый запрос на получение списка пользователей
        $response1 = $this->getJson('/api/users');
        $response1->assertStatus(200);

        // Проверяем, что данные закешировались
        $this->assertTrue(Cache::has($cacheKey));

        // Выполним запрос еще раз и убедимся, что он был получен из кеша
        $cachedUsers = Cache::get($cacheKey);

        // Повторный запрос к API должен вернуть тот же набор данных из кеша
        $response2 = $this->getJson('/api/users');
        $response2->assertStatus(200);
        $response2->assertJson($cachedUsers->toArray());

        // Проверка корректности фильтров
        // Допустим, что фильтры "NameFilter" и "EmailFilter" работают правильно, создадим запрос с фильтрацией
        $responseWithFilters = $this->getJson('/api/users?name=John&email=john@example.com');
        $responseWithFilters->assertStatus(200);
    }

    public function test_cache_is_cleared_when_user_is_updated()
    {
        // Создание тестового пользователя
        $user = User::factory()->create();

        // Авторизация пользователя
        $this->actingAs($user);

        // Выполнение первого запроса (данные кешируются)
        $response1 = $this->getJson("/api/users/{$user->id}");
        $response1->assertStatus(200);

        // Проверка, что данные в кеше существуют
        $this->assertTrue(Cache::has("user_{$user->id}"));

        // Выполнение PUT-запроса для обновления пользователя
        $this->putJson("/api/users/{$user->id}", ['name' => 'Updated Name']);

        // Проверка, что кеш был очищен
        $this->assertFalse(Cache::has("user_{$user->id}"));
    }
}
