<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatHistory;

class ChatHistoryController extends Controller
{
    public function index()
    {
        $histories = ChatHistory::with('user')->latest()->paginate(20);
        return view('admin.chat_histories', compact('histories'));
    }
} 