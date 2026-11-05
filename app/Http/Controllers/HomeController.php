<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Home', [
            'flash' => [
                'success' => session('success'),
                'info' => session('info'),
                'error' => session('error'),
            ],
        ]);
    }

    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        // TODO: Send email or save to database
        return redirect()->route('home')->with('success', 'Thank you for your message! We will get back to you soon.');
    }
}
