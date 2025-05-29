@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 rounded-2xl shadow-md">
    <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">Iniciar sesión</h2>

    @if ($errors->has('login'))
    <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
        {{ $errors->first('login') }}
    </div>
    @endif

    @if (session('success'))
    <div class="bg-green-100 text-green-800 border border-green-300 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('login') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700">Usuario</label>
            <input type="text" name="username" required
                class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Contraseña</label>
            <input type="password" name="password" required
                class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
            <button type="submit"
                class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200">
                Ingresar
            </button>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('register') }}"
                class="text-blue-600 hover:underline text-sm font-medium">
                ¿No tienes cuenta? Crear un nuevo usuario MySQL
            </a>
        </div>

    </form>
</div>
@endsection