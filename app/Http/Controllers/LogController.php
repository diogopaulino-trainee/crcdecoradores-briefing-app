<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::query()->with('causer');

        if ($request->filled('termo')) {
            $termo = $request->termo;
            $query->where(function ($q) use ($termo) {
                $q->where('description', 'like', "%{$termo}%")
                    ->orWhere('log_name', 'like', "%{$termo}%")
                    ->orWhere('properties->ip', 'like', "%{$termo}%")
                    ->orWhereHas('causer', function ($q2) use ($termo) {
                        $q2->where('name', 'like', "%{$termo}%");
                    });
            });
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $logs = $query->paginate(10)->withQueryString();

        activity()
            ->useLog('Logs')
            ->causedBy(auth()->user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Acedeu à listagem dos logs.');

        return Inertia::render('Logs/Index', [
            'logs' => $logs,
            'filtros' => $request->only('termo', 'sort', 'direction'),
        ]);
    }

    public function destroy($id)
    {
        $log = Activity::findOrFail($id);
        $descricao = $log->description;
        $logName = $log->log_name;
        $timestamp = $log->created_at->toDateTimeString();
        $log->delete();

        activity()
        ->useLog('Logs')
        ->causedBy(auth()->user())
        ->withProperties([
            'id_log_eliminado' => $id,
            'descricao_original' => $descricao,
            'log_name' => $logName,
            'data_original' => $timestamp,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ])
        ->log("Eliminou o log ID {$id} com descrição '{$descricao}'.");

        return redirect()->route('logs.index')->with('success', 'Log eliminado com sucesso.');
    }

    public function clearAll()
    {
        $total = Activity::count();

        Activity::truncate();

        activity()
            ->useLog('Logs')
            ->causedBy(auth()->user())
            ->withProperties([
                'quantidade_eliminada' => $total,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log("Eliminou todos os logs existentes ({$total} registos).");

        return redirect()->route('logs.index')->with('success', 'Todos os logs foram eliminados.');
    }
}
