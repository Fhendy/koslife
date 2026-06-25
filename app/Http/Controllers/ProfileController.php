<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Note;
use App\Models\Habit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        
        // Stats for account tab
        $stats = [
            'total_tasks' => Task::where('user_id', $user->id)->count(),
            'total_notes' => Note::where('user_id', $user->id)->count(),
            'total_habits' => Habit::where('user_id', $user->id)->count(),
        ];
        
        return view('profile.edit', compact('user', 'stats'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|max:2048',
            'theme' => 'nullable|string|in:light,dark,system',
            'daily_meal_budget' => 'nullable|numeric|min:0',
            'savings_goal' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|in:IDR,USD,SGD',
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        // Update avatar
        if ($request->hasFile('avatar')) {
            if ($user->avatar && $user->avatar != 'default') {
                // Delete old avatar
                $oldPath = str_replace('/storage/', '', $user->avatar);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = '/storage/' . $path;
        }
        
        // Update password
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini salah']);
            }
            $user->password = Hash::make($request->password);
        }
        
        // Update other fields
        $user->name = $validated['name'] ?? $user->name;
        $user->email = $validated['email'] ?? $user->email;
        $user->theme = $validated['theme'] ?? $user->theme;
        $user->daily_meal_budget = $validated['daily_meal_budget'] ?? $user->daily_meal_budget;
        $user->savings_goal = $validated['savings_goal'] ?? $user->savings_goal;
        $user->currency = $validated['currency'] ?? $user->currency;
        
        $user->save();
        
        return redirect()->route('profile.edit')
                         ->with('success', 'Profil berhasil diperbarui! ✅');
    }

    public function destroy()
    {
        $user = Auth::user();
        
        // Delete user data
        Task::where('user_id', $user->id)->delete();
        Note::where('user_id', $user->id)->delete();
        Habit::where('user_id', $user->id)->delete();
        // Add other models as needed
        
        // Delete avatar
        if ($user->avatar && $user->avatar != 'default') {
            $path = str_replace('/storage/', '', $user->avatar);
            Storage::disk('public')->delete($path);
        }
        
        $user->delete();
        
        Auth::logout();
        
        return redirect('/')->with('success', 'Akun berhasil dihapus');
    }

    public function clearData()
    {
        $user = Auth::user();
        
        // Delete all user data except profile
        Task::where('user_id', $user->id)->delete();
        Note::where('user_id', $user->id)->delete();
        Habit::where('user_id', $user->id)->delete();
        // Add other models as needed
        
        return redirect()->route('profile.edit')
                         ->with('success', 'Semua data berhasil dihapus! 🗑️');
    }
}