<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\JsonResponse as JsonResponseAlias;

class HistoryController extends Controller
{
    public function index(): JsonResponseAlias
    {
        $histories = History::all();

        return response()->json($histories);
    }

    public function show($id): JsonResponseAlias
    {
        $history = History::findOrFail($id);

        return response()->json($history);
    }

    public function softDelete($id): JsonResponseAlias
    {
        $history = History::findOrFail($id);
        $history->delete();  // Soft delete

        return response()->json(['message' => 'History record moved to trash']);
    }

    public function forceDelete($id): JsonResponseAlias
    {
        $history = History::withTrashed()->findOrFail($id);
        $history->forceDelete();  // Полное удаление

        return response()->json(['message' => 'History record permanently deleted']);
    }

    public function restore($id): JsonResponseAlias
    {
        $history = History::withTrashed()->findOrFail($id);
        $history->restore();  // Восстановление

        return response()->json(['message' => 'History record restored']);
    }

    public function restoreModel($id): JsonResponseAlias
    {
        // Найдем запись в таблице histories
        $history = History::findOrFail($id);

        // Используем model_id и model_name, чтобы найти модель, к которой привязана история
        $modelClass = 'App\\Models\\'.$history->model_name;
        $model = $modelClass::withTrashed()->findOrFail($history->model_id);

        // Восстанавливаем данные модели до состояния, зафиксированного в истории (before/after)
        $model->update($history->before);

        return response()->json(['message' => 'Model restored to previous state']);
    }
}
