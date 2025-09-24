<?php

namespace App\Http\Controllers;

use App\Models\Season;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    public function index()
    {
        $seasons = Season::orderBy('status', 'desc')->orderBy('end_date', 'desc')->paginate(15);
        return view('seasons.index', compact('seasons'));
    }

    public function create()
    {
        return view('seasons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,closed',
        ]);

        Season::create($validated);
        return redirect()->route('seasons.index')->with('success', 'Temporada criada com sucesso!');
    }

    public function edit(Season $season)
    {
        return view('seasons.edit', compact('season'));
    }

    public function update(Request $request, Season $season)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,closed',
        ]);

        $season->update($validated);
        return redirect()->route('seasons.index')->with('success', 'Temporada atualizada com sucesso!');
    }

    public function destroy(Season $season)
    {
        $season->delete();
        return redirect()->route('seasons.index')->with('success', 'Temporada removida com sucesso!');
    }
}



