<?php

namespace App\Http\Controllers;

use App\Models\Flat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FlatController extends Controller
{
    /**
     * Display a listing of all flats
     */
    public function index(Request $request)
    {
        $query = Flat::query();

        // Filter by floor
        if ($request->filled('floor')) {
            $query->where('floor_number', $request->floor);
        }

        // Filter by maintenance status
        if ($request->filled('status')) {
            $query->where('maintenance_status', $request->status);
        }

        // Sort
        $sortBy = $request->get('sort', 'flat_number');
        $sortOrder = $request->get('order', 'asc');
        
        // Use numeric sort for flat_number
        if ($sortBy === 'flat_number') {
            $query->orderByRaw('CAST(flat_number AS INTEGER) ' . $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $flats = $query->get();

        // Get statistics
        $stats = [
            'total' => Flat::count(),
            'pending' => Flat::where('maintenance_status', 'pending')->count(),
            'paid' => Flat::where('maintenance_status', 'paid')->count(),
            'overdue' => Flat::where('maintenance_status', 'overdue')->count(),
        ];

        return view('flats.index', compact('flats', 'stats'));
    }

    /**
     * Display a listing by floor
     */
    public function byFloor($floorNumber)
    {
        $flats = Flat::where('floor_number', $floorNumber)->orderBy('flat_number')->paginate(20);
        $floor = $floorNumber;

        return view('flats.floor', compact('flats', 'floor'));
    }

    /**
     * Show the form for creating a new flat
     */
    public function create()
    {
        return view('flats.create');
    }

    /**
     * Store a newly created flat
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'flat_number' => 'required|unique:flats',
            'floor_number' => 'required|integer|between:1,11',
            'flat_type' => 'required|in:1BHK,2BHK',
            'occupancy_type' => 'required|in:owner,tenant',
            'owner_name' => 'nullable|string',
            'owner_email' => 'nullable|email',
            'owner_phone' => 'nullable|string',
            'maintenance_status' => 'required|in:pending,paid,overdue',
            'builder_paid_exception' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $flat = Flat::create($validated);
        
        // Set maintenance amount based on flat type and occupancy
        $flat->syncMaintenanceAmount();

        return redirect()->route('flats.index')->with('success', 'Flat created successfully');
    }

    /**
     * Show the form for editing a flat
     */
    public function edit(Flat $flat)
    {
        return view('flats.edit', compact('flat'));
    }

    /**
     * Update the specified flat
     */
    public function update(Request $request, Flat $flat)
    {
        $validated = $request->validate([
            'flat_type' => 'required|in:1BHK,2BHK',
            'occupancy_type' => 'required|in:owner,tenant',
            'owner_name' => 'nullable|string',
            'owner_email' => 'nullable|email',
            'owner_phone' => 'nullable|string',
            'maintenance_status' => 'required|in:pending,paid,overdue',
            'last_maintenance_date' => 'nullable|date',
            'builder_paid_exception' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $flat->update($validated);
        
        // Sync maintenance amount based on updated flat type and occupancy
        $flat->syncMaintenanceAmount();

        return redirect()->route('flats.index')->with('success', 'Flat updated successfully');
    }

    /**
     * Delete the specified flat
     */
    public function destroy(Flat $flat)
    {
        $flat->delete();

        return redirect()->route('flats.index')->with('success', 'Flat deleted successfully');
    }

    /**
     * Get statistics summary
     */
    public function statistics()
    {
        $stats = [
            'total' => Flat::count(),
            'pending' => Flat::where('maintenance_status', 'pending')->count(),
            'paid' => Flat::where('maintenance_status', 'paid')->count(),
            'overdue' => Flat::where('maintenance_status', 'overdue')->count(),
            'by_floor' => Flat::selectRaw('floor_number, COUNT(*) as count')
                ->groupBy('floor_number')
                ->orderBy('floor_number')
                ->get(),
        ];

        return view('flats.statistics', compact('stats'));
    }
}
