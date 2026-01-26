<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index()
    {
        $colors = Color::where('is_active', true)->get();
        return response()->json($colors);
    }

    public function all()
    {
        // For admin to see all, including inactive
        $colors = Color::all();
        return response()->json($colors);
    }

    public function show($id)
    {
        $color = Color::findOrFail($id);
        return response()->json($color);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hex_code' => 'required|string|max:7', // #RRGGBB
            'is_active' => 'boolean'
        ]);

        $color = Color::create([
            'name' => $validated['name'],
            'hex_code' => $validated['hex_code'],
            'is_active' => $validated['is_active'] ?? true
        ]);

        return response()->json($color, 201);
    }

    public function update(Request $request, $id)
    {
        $color = Color::findOrFail($id);

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'hex_code' => 'nullable|string|max:7',
            'is_active' => 'boolean'
        ]);

        $color->update($validated);

        return response()->json($color);
    }

    public function destroy($id)
    {
        $color = Color::findOrFail($id);
        // Maybe soft delete or check if used? Model doesn't use SoftDeletes currently explicitly in what checked?
        // Checking Model content: class Color extends Model { protected $fillable... } 
        // It does NOT use SoftDeletes in the file view I saw.
        // So this will be hard delete.
        $color->delete();

        return response()->json(['message' => 'Color deleted successfully']);
    }
}
