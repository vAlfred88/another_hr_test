<?php

namespace App\Console\Commands;

use App\Models\History;
use Illuminate\Console\Command;

class RestoreModelFromHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'history:restore-model {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Восстановление модели до состояния из истории';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Получаем ID записи истории из аргумента команды
        $historyId = $this->argument('id');

        // Находим запись в истории
        $history = History::findOrFail($historyId);

        // В команде RestoreModelFromHistory
        $history = History::findOrFail($historyId);
        if (! $history) {
            $this->error("No history found with ID: {$historyId}");
        } else {
            $this->info("Found history with ID: {$history->id}");
        }

        // Находим модель по данным из истории
        $modelClass = 'App\\Models\\'.$history->model_name;
        $model = $modelClass::withTrashed()->findOrFail($history->model_id);

        // Восстанавливаем модель до состояния, указанного в истории
        $model->update($history->before);

        $this->info("Model {$history->model_name} with ID {$history->model_id} has been restored.");
    }
}
