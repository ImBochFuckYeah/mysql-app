<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function viewLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        try {
            config([
                'database.connections.temp' => [
                    'driver' => 'mysql',
                    'host' => env('DB_HOST', '127.0.0.1'),
                    'port' => env('DB_PORT', '3306'),
                    'database' => null,
                    'username' => $credentials['username'],
                    'password' => $credentials['password'],
                ]
            ]);

            DB::connection('temp')->getPdo();
            Session::put('mysql_user', $credentials['username']);
            Session::put('mysql_pass', $credentials['password']);
            return redirect('/');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['login' => 'Credenciales inválidas']);
        }
    }

    public function viewRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {        
        try {
            $request->validate([
                'username' => 'required|string|min:3|max:50',
                'password' => 'required|string|min:5|confirmed',
            ]);
            
            $username = $request->username;
            $password = $request->password;

            // Usar la conexión por defecto del .env (DB_CONNECTION)
            DB::statement("CREATE USER '$username'@'localhost' IDENTIFIED BY '$password';");

            // Otorgar permisos mínimos (opcional)
            DB::statement("GRANT ALL PRIVILEGES ON *.* TO '$username'@'localhost';");

            return redirect('/login')->with('success', 'Usuario MySQL creado correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function logout()
    {
        Session::forget(['mysql_user', 'mysql_pass']);
        return redirect('/login');
    }
}
