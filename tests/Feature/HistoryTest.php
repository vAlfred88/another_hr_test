<?php

namespace Tests\Feature;

use App\Models\History;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class HistoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест для проверки записи истории при создании пользователя.
     *
     * @return void
     */
    public function test_history_record_is_created_when_user_is_created()
    {
        // Данные для создания пользователя
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        // Выполнение POST-запроса на регистрацию
        $response = $this->postJson('/register', $userData);

        // Проверяем успешное создание пользователя
        $response->assertStatus(204);

        // Проверяем, что запись в истории была создана
        $this->assertDatabaseHas('histories', [
            'model_name' => 'User',
            'action' => 'created',
        ]);
    }

    /**
     * Тест для проверки записи истории при обновлении пользователя.
     *
     * @return void
     */
    public function test_history_record_is_created_when_user_is_updated()
    {
        $user = User::factory()->create();

        // Авторизация
        $this->actingAs($user);

        // Данные для обновления
        $updateData = ['name' => 'Updated Name'];

        // Выполнение PUT-запроса для обновления пользователя
        $response = $this->putJson("/api/users/{$user->id}", $updateData);

        // Проверяем успешное обновление пользователя
        $response->assertStatus(200);

        // Проверяем, что запись в истории была создана
        $this->assertDatabaseHas('histories', [
            'model_name' => 'User',
            'action' => 'updated',
        ]);
    }

    /**
     * Тест команды history:delete для полного удаления всех записей истории.
     */
    public function test_history_delete_command_deletes_all_history_records()
    {
        // Очищаем историю (по-хорошему нужно изолировать тест, но мне лень)
        History::query()->forceDelete();

        // Создание тестовых записей в таблице histories
        History::factory()->count(5)->create();

        // Убедимся, что записи существуют в базе
        $this->assertDatabaseCount('histories', 5);

        // Выполнение команды history:delete через artisan
        $this->artisan('history:delete')
            ->expectsOutput('All history records have been permanently deleted.')
            ->assertExitCode(0);

        // Проверка, что все записи были удалены
        $this->assertDatabaseCount('histories', 0);
    }

    /**
     * Тест команды history:restore-model для восстановления модели до состояния из истории.
     */
    public function test_history_restore_model_command_restores_model_to_previous_state()
    {
        // Создание тестового пользователя
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertDatabaseHas('histories', [
            'model_name' => 'User',
            'model_id' => $user->id,
        ]);

        $this->actingAs($user);

        // Убедимся, что начальные данные пользователя "John Doe"
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);

        // Выполнение DELETE-запроса для логического удаления пользователя
        $response = $this->deleteJson("/api/users/{$user->id}");

        // Проверка успешного статуса
        $response->assertStatus(200)
            ->assertJson(['message' => 'User moved to trash']);

        // Создание записи в истории, содержащей состояние до изменений
        $history = History::factory()->create([
            'id' => Str::uuid(),
            'model_id' => $user->id,
            'model_name' => 'User',
            'before' => [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
            ],
            'action' => 'updated',
        ]);

        // Выполнение команды history:restore-model через artisan
        $this->artisan("history:restore-model {$history->id}")
            ->expectsOutput("Model User with ID {$user->id} has been restored.")
            ->assertExitCode(0);

        // Обновим данные пользователя
        $user->refresh();

        // Проверим, что данные пользователя восстановлены до состояния из истории
        $this->assertEquals('Jane Doe', $user->name);
        $this->assertEquals('jane@example.com', $user->email);
    }
}
