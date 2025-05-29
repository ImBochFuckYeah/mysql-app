<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class QueryController extends Controller
{

    private function getUserConnection()
    {
        $user = Session::get('mysql_user');
        $pass = Session::get('mysql_pass');

        config([
            'database.connections.temp' => [
                'driver' => 'mysql',
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => null, // Se puede usar "USE dbname;" como sentencia explícita
                'username' => $user,
                'password' => $pass,
            ]
        ]);

        return DB::connection('temp');
    }

    public function ejecutar(Request $request)
    {
        try {
            $query = trim($request['query']);
            $userConn = $this->getUserConnection();

            // Detectar inicio de transacción
            if (preg_match('/^\s*(begin|start transaction)\s*;?$/i', $query)) {
                Session::put('transaction_mode', true);
                Session::put('transaction_queries', []);
                return response()->json(['message' => 'Transacción iniciada.']);
            }

            // Detectar COMMIT
            if (preg_match('/^\s*commit\s*;?$/i', $query)) {
                $queries = Session::get('transaction_queries', []);
                $userConn->beginTransaction();
                foreach ($queries as $q) {
                    $userConn->statement($q);
                }
                $userConn->commit();
                Session::forget(['transaction_mode', 'transaction_queries']);
                return response()->json(['message' => 'Transacción ejecutada con COMMIT.']);
            }

            // Detectar ROLLBACK
            if (preg_match('/^\s*rollback\s*;?$/i', $query)) {
                Session::forget(['transaction_mode', 'transaction_queries']);
                return response()->json(['message' => 'Transacción cancelada con ROLLBACK.']);
            }

            // Si estamos en modo transacción, guardar sin ejecutar
            if (Session::get('transaction_mode')) {
                $queries = Session::get('transaction_queries', []);
                $queries[] = $query;
                Session::put('transaction_queries', $queries);
                return response()->json(['message' => 'Sentencia almacenada en transacción.']);
            }

            // Si no es parte de transacción, ejecutar directamente
            $queryType = strtolower(strtok($query, " "));
            switch ($queryType) {
                case 'select':
                    return response()->json(['data' => $userConn->select($query)]);
                case 'insert':
                    return response()->json(['message' => 'Insert ejecutado.', 'filas_afectadas' => $userConn->insert($query)]);
                case 'update':
                    return response()->json(['message' => 'Update ejecutado.', 'filas_afectadas' => $userConn->update($query)]);
                case 'delete':
                    return response()->json(['message' => 'Delete ejecutado.', 'filas_afectadas' => $userConn->delete($query)]);
                default:
                    $resultado = $userConn->statement($query);
                    return response()->json(['message' => 'Sentencia ejecutada.', 'resultado' => $resultado]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
