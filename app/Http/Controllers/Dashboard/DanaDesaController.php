<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DanaDesaController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/DanaDesa/Index');
    }

    public function create()
    {
        return Inertia::render('Dashboard/DanaDesa/Create');
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required',
            'file' => 'required',
            'link' => 'required',
            'date' => 'required'
        ];

        $request->validate($rules);
    }

    public function edit($id)
    {
        return Inertia::render('Dashboard/DanaDesa/Edit');
    }

    public function update(Request $request, $id) {}
}
