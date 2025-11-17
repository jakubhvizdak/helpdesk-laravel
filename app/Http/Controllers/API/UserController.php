<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('projects:id,name')
            ->select('id', 'name', 'surname', 'email', 'role')
            ->whereNull('deleted_at')
            ->get();

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:user,admin,customer',
        ], [
            'name.required' => 'Meno je povinné.',
            'surname.required' => 'Priezvisko je povinné.',
            'name.string' => 'Meno musí byť text.',
            'email.required' => 'Email je povinný.',
            'email.email' => 'Zadajte platnú e-mailovú adresu.',
            'email.unique' => 'Tento e-mail sa už používa.',
            'password.required' => 'Heslo je povinné.',
            'password.min' => 'Heslo musí mať aspoň :min znakov.',
            'role.required' => 'Rola je povinná.',
            'role.in' => 'Rola musí byť "user", "admin" alebo "customer".',
        ]);

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return response()->json($user, 201);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:user,admin,customer',
        ], [
            'name.required' => 'Meno je povinné.',
            'surname.required' => 'Priezvisko je povinné.',
            'email.required' => 'Email je povinný.',
            'email.email' => 'Zadajte platnú e-mailovú adresu.',
            'email.unique' => 'Tento e-mail sa už používa.',
            'password.min' => 'Heslo musí mať aspoň :min znakov.',
            'role.required' => 'Rola je povinná.',
            'role.in' => 'Rola musí byť "user", "admin" alebo "customer".',
        ]);

        $user->name = $data['name'];
        $user->surname = $data['surname'];
        $user->email = $data['email'];
        $user->role = $data['role'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return response()->json($user);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'Používateľ bol odstránený.']);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'surname' => $user->surname,
            'email' => $user->email,
            'role' => $user->role,
        ]);
    }

    public function assignProjectsToUser(Request $request, User $user)
    {
        $request->validate([
            'project_ids' => 'array',
            'project_ids.*' => 'exists:projects,id'
        ]);

        $user->projects()->sync($request->project_ids);
        return response()->json(['message' => 'Projekty boli aktualizované.']);
    }

}
