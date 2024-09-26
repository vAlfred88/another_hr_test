<?php

namespace App\Observers;

use App\Models\History;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Логирование создания пользователя
        History::create([
            'model_id' => $user->id,
            'model_name' => 'User',
            'before' => null,
            'after' => $user->toArray(),
            'action' => 'created',
        ]);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Сохраняем состояние до изменений
        $before = $user->toArray();

        // Логирование изменений
        History::create([
            'model_id' => $user->id,
            'model_name' => 'User',
            'before' => $before,
            'after' => $user->toArray(),
            'action' => 'updated',
        ]);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Сохраняем состояние до изменений
        $before = $user->toArray();

        // Логирование удаления
        History::create([
            'model_id' => $user->id,
            'model_name' => 'User',
            'before' => $before,
            'after' => null,
            'action' => 'deleted',
        ]);
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        // Сохраняем состояние до изменений
        $before = $user->toArray();

        // Логирование восстановления
        History::create([
            'model_id' => $user->id,
            'model_name' => 'User',
            'before' => null,
            'after' => $before,
            'action' => 'restored',
        ]);
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
