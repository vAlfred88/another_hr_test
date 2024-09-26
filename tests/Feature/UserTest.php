<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест для просмотра списка пользователей (GET /users).
     *
     * @return void
     */
    public function test_authenticated_user_can_view_users_list()
    {
        // Создание тестового пользователя
        $user = User::factory()->create();

        // Авторизация пользователя для выполнения запроса
        $this->actingAs($user);

        // Выполнение GET-запроса на получение списка пользователей
        $response = $this->getJson('/api/users');

        // Проверка успешного статуса и структуры ответа
        $response->assertStatus(200)
            ->assertJsonStructure([['id', 'name', 'email']]);
    }

    /**
     * Тест для просмотра конкретного пользователя (GET /users/{id}).
     *
     * @return void
     */
    public function test_authenticated_user_can_view_a_single_user()
    {
        // Создание тестового пользователя
        $user = User::factory()->create();

        // Авторизация пользователя
        $this->actingAs($user);

        // Выполнение GET-запроса на просмотр одного пользователя
        $response = $this->getJson("/api/users/{$user->id}");

        // Проверка успешного статуса и данных ответа
        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

    /**
     * Тест для редактирования пользователя (PUT /users/{id}).
     *
     * @return void
     */
    public function test_authenticated_user_can_update_user()
    {
        // Создание тестового пользователя
        $user = User::factory()->create();

        // Авторизация пользователя
        $this->actingAs($user);

        // Новые данные для редактирования
        $updatedData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        // Выполнение PUT-запроса для обновления данных пользователя
        $response = $this->putJson("/api/users/{$user->id}", $updatedData);

        // Проверка успешного статуса и обновленных данных в ответе
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User updated successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => 'Updated Name',
                    'email' => 'updated@example.com',
                ],
            ]);

        // Проверка того, что данные были изменены в базе данных
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    /**
     * Тест для логического удаления пользователя (DELETE /users/{id}).
     *
     * @return void
     */
    public function test_authenticated_user_can_soft_delete_user()
    {
        // Создание тестового пользователя
        $user = User::factory()->create();

        // Авторизация пользователя
        $this->actingAs($user);

        // Выполнение DELETE-запроса для логического удаления пользователя
        $response = $this->deleteJson("/api/users/{$user->id}");

        // Проверка успешного статуса
        $response->assertStatus(200)
            ->assertJson(['message' => 'User moved to trash']);

        // Проверка того, что пользователь был удален с помощью Soft Delete
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    /**
     * Тест для логического удаления пользователя (DELETE /users/{id}).
     *
     * @return void
     */
    public function test_authenticated_user_can_view_deleted_users()
    {
        // Создание тестового пользователя
        $users = User::factory(5)->create();

        // Авторизация пользователя
        $this->actingAs($users->first());

        // Выполнение DELETE-запроса для логического удаления пользователя
        $response = $this->deleteJson("/api/users/{$users->last()->id}");

        // Проверка успешного статуса
        $response->assertStatus(200)
            ->assertJson(['message' => 'User moved to trash']);

        // Проверка того, что пользователь был удален с помощью Soft Delete
        $this->assertSoftDeleted('users', ['id' => $users->last()->id]);

        // Проверка списка удаленных пользователей
        $response = $this->getJson('/api/users-deleted');

        // Проверка успешного статуса
        $response->assertStatus(200)
            ->assertJson([
                [
                    'id' => $users->last()->id,
                    'name' => $users->last()->name,
                    'email' => $users->last()->email,
                ],
            ]);
    }

    /**
     * Тест для восстановления логически удаленного пользователя (POST /users/{id}/restore).
     *
     * @return void
     */
    public function test_authenticated_user_can_restore_soft_deleted_user()
    {
        // Создание и логическое удаление тестового пользователя
        $user = User::factory()->create();
        $user->delete();

        // Авторизация пользователя
        $this->actingAs($user);

        // Выполнение POST-запроса для восстановления пользователя
        $response = $this->postJson("/api/users/{$user->id}/restore");

        // Проверка успешного статуса
        $response->assertStatus(200)
            ->assertJson(['message' => 'User restored successfully']);

        // Проверка того, что пользователь был восстановлен
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    /**
     * Тест для полного удаления пользователя (DELETE /users/{id}/force-delete).
     *
     * @return void
     */
    public function test_authenticated_user_can_force_delete_user()
    {
        // Создание тестового пользователя
        $user = User::factory()->create();

        // Авторизация пользователя
        $this->actingAs($user);

        // Выполнение DELETE-запроса для полного удаления пользователя
        $response = $this->deleteJson("/api/users/{$user->id}/force-delete");

        // Проверка успешного статуса
        $response->assertStatus(200)
            ->assertJson(['message' => 'User permanently deleted']);

        // Проверка того, что пользователь был удален из базы данных
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
