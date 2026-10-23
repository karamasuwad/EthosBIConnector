<?php

namespace App\Http\Controllers;

use App\Models\BiLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
/**
 * BiLinkController handles the management of BI Links.
 */

class BiLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $biLinks = BiLink::where('is_active', true)
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('bi-links.index', compact('biLinks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('bi-links.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'link' => 'required|url|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        BiLink::create([
            'name' => $request->name,
            'link' => $request->link,
            'is_active' => true,
        ]);

        return redirect()->route('bi-links.index')
                        ->with('success', 'BI Link created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(BiLink $biLink)
    {
        return view('bi-links.show', compact('biLink'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BiLink $biLink)
    {
        return view('bi-links.edit', compact('biLink'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BiLink $biLink)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'link' => 'required|url|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        $biLink->update([
            'name' => $request->name,
            'link' => $request->link,
        ]);

        return redirect()->route('bi-links.index')
                        ->with('success', 'BI Link updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BiLink $biLink)
    {
        $biLink->update(['is_active' => false]);

        return redirect()->route('bi-links.index')
                        ->with('success', 'BI Link deleted successfully!');
    }
}
