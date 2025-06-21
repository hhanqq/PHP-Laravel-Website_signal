<?php
/*
// app/Http/Controllers/UpdatePocketIdController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdatePocketIdController extends Controller
{
    public function update(Request $request)
    {
        // Проверяем, есть ли пользователь
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Валидация
        $request->validate([
            'pocket_id' => 'required|integer|min:1',
        ]);

        // Обновляем pocket_id
        $user->update([
            'pocket_id' => $request->input('pocket_id'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'pocket_id успешно установлен.',
            'pocket_id' => $user->pocket_id,
        ]);
    }
}
*/


// UpdatePocketIdController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdatePocketIdController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'pocket_id' => 'required|integer|min:1',
        ]);

        $user->update([
            'pocket_id' => $request->input('pocket_id'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'pocket_id установлен.',
            'redirect' => '/signal',
        ]);
    }
}
