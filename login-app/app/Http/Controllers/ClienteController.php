<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ClienteController extends Controller
{
    public function registerClient(Request $request){
        // Se crea el usuario con los datos del registro
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Le asignamos el rol de Cliente
        $user->assignRole('cliente');
    }
}
