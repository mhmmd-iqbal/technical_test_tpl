<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index()
    {
        if (Gate::denies('manage-user')) {
            abort(403);
        }
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }
}
