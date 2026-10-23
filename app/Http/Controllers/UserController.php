<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BiLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $users = User::with('biLink')->orderBy('created_at', 'desc')->get();
        $users = User::with('biLink')->paginate(5);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $biLinks = BiLink::where('is_active', true)->get();
        return view('users.create', compact('biLinks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telephone' => 'nullable|string|max:20',
            'bi_link_id' => 'nullable|exists:bi_links,id',
            'bi_link_name' => 'nullable|string|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'bi_link_id' => $request->bi_link_id,
            'bi_link_name' => $request->bi_link_name,
            'slpCode' => $request->slpCode,
            'password' => Hash::make($request->password),
            'is_active' => $request->has('is_active'),
            'is_user' => $request->has('is_user'),
        ]);

        return redirect()->route('users.index')
                        ->with('success', 'User created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $biLinks = BiLink::where('is_active', true)->get();
        return view('users.edit', compact('user', 'biLinks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
            'bi_link_id' => 'nullable|exists:bi_links,id',
            'bi_link_name' => 'nullable|string|max:255',
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'bi_link_id' => $request->bi_link_id,
            'slpCode' => $request->slpCode,
            'bi_link_name' => $request->bi_link_name,
            'is_active' => $request->has('is_active'),
            'is_user' => $request->has('is_user'),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('users.index')
                        ->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent users from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                            ->with('error', 'You cannot delete your own account!');
        }

        $user->update(['is_active' => false]);

        return redirect()->route('users.index')
                        ->with('success', 'User deactivated successfully!');
    }

    /**
     * Activate the specified resource.
     */
    public function activate(User $user)
    {
        $user->update(['is_active' => true]);

        return redirect()->route('users.index')
                        ->with('success', 'User activated successfully!');
    }
}
