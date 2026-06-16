<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Consumo de Água</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold text-slate-800 mb-6 text-center">Consumo de Água 💧</h1>
        
        <form action="{{ route('water.calculate') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tipo de Consumidor</label>
                <select name="type" class="w-full border border-slate-300 rounded-lg p-2.5 bg-white text-slate-800">
                    <option value="residencial" {{ (isset($type) && $type == 'residencial') ? 'selected' : '' }}>Residencial</option>
                    <option value="comercial" {{ (isset($type) && $type == 'comercial') ? 'selected' : '' }}>Comercial</option>
                    <option value="industrial" {{ (isset($type) && $type == 'industrial') ? 'selected' : '' }}>Industrial</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Consumo (m³)</label>
                <input type="number" step="0.01" name="consumption" value="{{ $consumption ?? '' }}" required class="w-full border border-slate-300 rounded-lg p-2.5 text-slate-800" placeholder="Ex: 15.5">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition-colors">
                Calcular Fatura
            </button>
        </form>

        @if(isset($totalValue))
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-900 mb-2">Resultado do Cálculo</h3>
                <div class="space-y-1 text-sm text-blue-800">
                    <p><strong>Tipo:</strong> {{ ucfirst($type) }}</p>
                    <p><strong>Consumo registrado:</strong> {{ $consumption }} m³</p>
                    <p><strong>Taxa Básica:</strong> R$ {{ number_format($baseTariff, 2, ',', '.') }}</p>
                    <hr class="my-2 border-blue-200">
                    <p class="text-base text-blue-950"><strong>Valor Total:</strong> R$ {{ number_format($totalValue, 2, ',', '.') }}</p>
                </div>
            </div>
        @endif
    </div>
</body>
</html>
