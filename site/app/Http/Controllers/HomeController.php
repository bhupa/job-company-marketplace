<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function accountDelete()
    {
        return view('account-deletion-guide');
    }

    public function appStore(Request $request)
    {
        $data = $request->all();
        // Example: Log the incoming data
        Log::info('Received App Store notification:', $data);

        // Perform necessary actions based on the notification type
        // For example, update user subscriptions, process purchases, etc.
        return response()->json([$request->all()]);
    }
}
