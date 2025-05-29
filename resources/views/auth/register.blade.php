@extends('layouts.app')

@section('title', 'Registrar Usuario MySQL')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 rounded-2xl shadow-md">
    <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">Registro de Usuario MySQL</h2>

    @if ($errors->has('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            {{ $errors->first('error') }}
        </div>
    @endif

    <form action="/register" method="POST" class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700">Nombre de Usuario</label>
            <input type="text" name="username" required
                   class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Contraseña</label>
            <input type="password" name="password" required
                   class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
            <input type="password" name="password_confirmation" required
                   class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
            <button type="submit"
                    class="w-full bg-green-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-green-700 transition duration-200">
                Crear Usuario
            </button>
        </div>
    </form>
</div>
@endsection
