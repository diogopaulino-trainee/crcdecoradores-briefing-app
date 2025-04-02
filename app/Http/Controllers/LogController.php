<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LogController extends Controller
{
    public function index()
    {
        $logs = Activity::latest()->get();

        return Inertia::render('Logs/Index', [
            'logs' => $logs,
        ]);
    }

    public function show($id)
    {
        $log = Activity::findOrFail($id);

        return Inertia::render('Logs/Show', [
            'log' => $log,
        ]);
    }

    public function destroy($id)
    {
        $log = Activity::findOrFail($id);
        $log->delete();

        return redirect()->route('logs.index')->with('success', 'Log eliminado com sucesso.');
    }

    public function clearAll()
    {
        Activity::truncate();

        return redirect()->route('logs.index')->with('success', 'Todos os logs foram eliminados.');
    }
}
