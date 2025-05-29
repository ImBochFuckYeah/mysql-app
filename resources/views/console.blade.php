@extends('layouts.app')

@section('title', 'SQL Console')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-lg">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Consola de Comandos SQL</h2>

    <form id="formulario" class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Escribe tu sentencia SQL:</label>
        <textarea id="query" name="query" rows="6"
            class="w-full p-4 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
            placeholder="Ej. SELECT * FROM usuarios;"></textarea>

        <button type="submit"
            class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200">
            Ejecutar Comando
        </button>
    </form>

    <div id="resultado"></div>
</div>
<div class="max-w-4xl mx-auto">
    <form action="{{ route('logout') }}" method="GET">
        <button type="submit"
            class="mt-3 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200">
            Cerrar sesión
        </button>
    </form>
</div>
<script>
    document.getElementById('formulario').addEventListener('submit', function(e) {
        e.preventDefault();
        const query = document.getElementById('query').value;
        const resultadoDiv = document.getElementById('resultado');
        resultadoDiv.innerHTML = "<p class='text-gray-500 mt-4'>Ejecutando...</p>";

        fetch('/ejecutar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    query
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    resultadoDiv.innerHTML = `<div class="bg-red-100 text-red-700 p-3 rounded mt-4">${data.error}</div>`;
                } else if (data.data) {
                    // Si es un SELECT, mostrar tabla
                    if (data.data.length === 0) {
                        resultadoDiv.innerHTML = `<div class="bg-green-100 text-green-700 p-3 rounded mt-4">Consulta ejecutada correctamente. No se encontraron resultados.</div>`;
                        return;
                    }

                    const headers = Object.keys(data.data[0]);
                    let html = '<div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200 shadow-sm mt-4">';
                    html += '<thead class="bg-blue-600 text-white"><tr>';
                    headers.forEach(header => {
                        html += `<th class="px-4 py-2 text-left text-sm font-semibold">${header}</th>`;
                    });
                    html += '</tr></thead><tbody class="bg-white divide-y divide-gray-100">';
                    data.data.forEach(row => {
                        html += '<tr>';
                        headers.forEach(header => {
                            html += `<td class="px-4 py-2 text-sm text-gray-800">${row[header]}</td>`;
                        });
                        html += '</tr>';
                    });
                    html += '</tbody></table></div>';
                    resultadoDiv.innerHTML = html;
                } else {
                    resultadoDiv.innerHTML = `<div class="bg-green-100 text-green-700 p-3 rounded mt-4">${data.message || 'Sentencia ejecutada.'}</div>`;
                }
            })
            .catch(err => {
                resultadoDiv.innerHTML = `<div class="bg-red-100 text-red-700 p-3 rounded mt-4">Error ejecutando la consulta.</div  >`;
                console.error(err);
            });
    });
</script>
@endsection