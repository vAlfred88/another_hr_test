<?php

namespace App\Console\Commands;

use App\Models\History;
use Illuminate\Console\Command;

class DeleteHistories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'history:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Полное удаление всех записей истории';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Удаляем все записи в таблице histories
        History::withTrashed()->forceDelete();
        $this->info('All history records have been permanently deleted.');
    }
}
