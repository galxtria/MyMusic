<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    // 1. Tampilkan List User
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Ambil user selain yang sedang login (biar admin gak hapus diri sendiri)
        $users = User::where('id', '!=', auth()->id())
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    // 2. Hapus User
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }

    // 3. Ubah Role jadi Admin/User (Toggle)
    public function toggleRole(User $user)
    {
        $newRole = $user->role === 'admin' ? 'user' : 'admin';
        $user->update(['role' => $newRole]);
        
        return redirect()->back()->with('success', 'Role user berhasil diubah menjadi ' . $newRole);
    }
}