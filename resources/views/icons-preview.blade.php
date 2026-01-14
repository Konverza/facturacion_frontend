<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería de Íconos del Sistema</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .icon-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .icon-card {
            transition: all 0.2s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Galería de Íconos</h1>
            <p class="text-gray-600">Sistema de facturación electrónica - {{ count($icons) }} íconos disponibles</p>
        </div>

        <div class="mb-6">
            <input 
                type="text" 
                id="searchIcons" 
                placeholder="Buscar ícono..." 
                class="w-full max-w-md mx-auto block px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach($icons as $name => $svg)
                <div class="icon-card bg-white rounded-lg p-4 border border-gray-200 hover:border-blue-500 cursor-pointer" 
                     data-icon-name="{{ $name }}"
                     onclick="copyIconName('{{ $name }}')">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-12 h-12 flex items-center justify-center mb-3 text-gray-700">
                            {!! $svg !!}
                        </div>
                        <p class="text-xs text-gray-600 text-center font-medium break-all">{{ $name }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="toast" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-20 opacity-0 transition-all duration-300">
            <p class="font-medium">✓ Copiado: <span id="copiedIcon"></span></p>
        </div>
    </div>

    <script>
        // Búsqueda de íconos
        document.getElementById('searchIcons').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const iconCards = document.querySelectorAll('.icon-card');
            
            iconCards.forEach(card => {
                const iconName = card.getAttribute('data-icon-name').toLowerCase();
                if (iconName.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Copiar nombre del ícono
        function copyIconName(iconName) {
            const textToCopy = `icon="${iconName}"`;
            
            navigator.clipboard.writeText(textToCopy).then(() => {
                showToast(iconName);
            }).catch(err => {
                console.error('Error al copiar:', err);
            });
        }

        // Mostrar notificación
        function showToast(iconName) {
            const toast = document.getElementById('toast');
            const copiedIcon = document.getElementById('copiedIcon');
            
            copiedIcon.textContent = iconName;
            toast.classList.remove('translate-y-20', 'opacity-0');
            
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 2000);
        }
    </script>
</body>
</html>
