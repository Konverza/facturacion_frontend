@extends('layouts.auth-template')
@section('title', 'Nueva Notificación')

@push('head')
    <script src="https://cdn.tiny.cloud/1/no-api-key/hugerte/6/hugerte.min.js" referrerpolicy="origin"></script>
@endpush

@section('content')
    <section class="my-4 px-4 pb-4">
        <div class="mb-4 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Nueva Notificación
            </h1>
            <x-button type="a" href="{{ route('admin.notifications.index') }}" typeButton="secondary" icon="arrow-left"
                text="Volver" size="normal" />
        </div>

        <form id="notification-form" class="space-y-6">
            @csrf

            <!-- Información Básica -->
            <div class="rounded-lg border border-gray-300 bg-white p-6 dark:border-gray-800 dark:bg-gray-950">
                <h2 class="mb-4 text-xl font-bold text-gray-600 dark:text-white">
                    Información del Mensaje
                </h2>

                <div class="space-y-4">
                    <x-input type="text" name="subject" id="subject" label="Asunto del correo" placeholder="Ej: Actualización importante del sistema"
                        required="true" />

                    <div>
                        <label for="content" class="mb-2 block text-sm font-medium text-gray-500 dark:text-gray-300">
                            Contenido del mensaje: <span class="text-red-500">*</span>
                        </label>
                        <textarea id="content" name="content" rows="15"
                            class="w-full rounded-lg border border-gray-300 bg-gray-50 p-4 text-sm text-gray-900 focus:border-gray-600 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:border-gray-800 dark:bg-gray-950 dark:text-white dark:placeholder-gray-400 dark:focus:border-gray-500 dark:focus:ring-gray-900"></textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Puede usar HTML para dar formato al contenido
                        </p>
                    </div>
                </div>
            </div>

            <!-- Selección de Destinatarios -->
            <div class="rounded-lg border border-gray-300 bg-white p-6 dark:border-gray-800 dark:bg-gray-950">
                <h2 class="mb-4 text-xl font-bold text-gray-600 dark:text-white">
                    Destinatarios
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-500 dark:text-gray-300">
                            Tipo de destinatarios: <span class="text-red-500">*</span>
                        </label>
                        <div class="flex flex-wrap gap-4">
                            <label class="flex cursor-pointer items-center gap-2">
                                <input type="radio" name="recipient_type" value="businesses" class="size-4 text-primary-500"
                                    checked>
                                <span class="text-sm text-gray-700 dark:text-gray-300">Clientes (Negocios)</span>
                            </label>
                            <label class="flex cursor-pointer items-center gap-2">
                                <input type="radio" name="recipient_type" value="users" class="size-4 text-primary-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Usuarios del Sistema</span>
                            </label>
                            <label class="flex cursor-pointer items-center gap-2">
                                <input type="radio" name="recipient_type" value="custom" class="size-4 text-primary-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Correos Personalizados</span>
                            </label>
                        </div>
                    </div>

                    <!-- Lista de Negocios -->
                    <div id="businesses-section" class="recipient-section">
                        <label class="mb-2 block text-sm font-medium text-gray-500 dark:text-gray-300">
                            Seleccionar clientes: <span class="text-red-500">*</span>
                        </label>
                        
                        <div class="mb-3">
                            <x-input type="text" id="search-businesses" placeholder="Buscar cliente por nombre o correo..." 
                                icon="search" />
                        </div>

                        <div class="mb-2 flex gap-2">
                            <x-button type="button" id="select-all-businesses" typeButton="secondary" text="Seleccionar todos"
                                size="small" icon="check" />
                            <x-button type="button" id="deselect-all-businesses" typeButton="secondary" text="Deseleccionar todos"
                                size="small" icon="x" />
                        </div>
                        <div id="businesses-list" class="max-h-64 space-y-2 overflow-y-auto rounded-lg border border-gray-300 p-4 dark:border-gray-800">
                            @foreach ($businesses as $business)
                                <label class="business-item flex cursor-pointer items-center gap-2 rounded p-2 hover:bg-gray-100 dark:hover:bg-gray-800"
                                    data-name="{{ strtolower($business->nombre) }}"
                                    data-email="{{ strtolower($business->correo_responsable) }}">
                                    <input type="checkbox" name="businesses[]" value="{{ $business->id }}"
                                        data-email="{{ $business->correo_responsable }}" class="size-4 text-primary-500 business-checkbox">
                                    <div class="flex-1">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $business->nombre }}</span>
                                        <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $business->correo_responsable }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Lista de Usuarios -->
                    <div id="users-section" class="recipient-section hidden">
                        <label class="mb-2 block text-sm font-medium text-gray-500 dark:text-gray-300">
                            Seleccionar usuarios: <span class="text-red-500">*</span>
                        </label>

                        <div class="mb-3">
                            <x-input type="text" id="search-users" placeholder="Buscar usuario por nombre o correo..." 
                                icon="search" />
                        </div>

                        <div class="mb-2 flex gap-2">
                            <x-button type="button" id="select-all-users" typeButton="secondary" text="Seleccionar todos"
                                size="small" icon="check" />
                            <x-button type="button" id="deselect-all-users" typeButton="secondary" text="Deseleccionar todos"
                                size="small" icon="x" />
                        </div>
                        <div id="users-list" class="max-h-64 space-y-2 overflow-y-auto rounded-lg border border-gray-300 p-4 dark:border-gray-800">
                            @foreach ($users as $user)
                                <label class="user-item flex cursor-pointer items-center gap-2 rounded p-2 hover:bg-gray-100 dark:hover:bg-gray-800"
                                    data-name="{{ strtolower($user->name) }}"
                                    data-email="{{ strtolower($user->email) }}">
                                    <input type="checkbox" name="users[]" value="{{ $user->id }}"
                                        data-email="{{ $user->email }}" class="size-4 text-primary-500 user-checkbox">
                                    <div class="flex-1">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $user->name }}</span>
                                        <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Correos Personalizados -->
                    <div id="custom-section" class="recipient-section hidden">
                        <label class="mb-2 block text-sm font-medium text-gray-500 dark:text-gray-300">
                            Correos electrónicos: <span class="text-red-500">*</span>
                        </label>
                        <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                            Escriba un correo y presione Tab o Enter para agregarlo
                        </p>
                        <div id="email-tags-container" 
                            class="min-h-[120px] w-full rounded-lg border border-gray-300 bg-gray-50 p-2 focus-within:border-primary-500 focus-within:ring-2 focus-within:ring-primary-200 dark:border-gray-800 dark:bg-gray-950 dark:focus-within:border-primary-500 dark:focus-within:ring-primary-900">
                            <div id="email-tags" class="flex flex-wrap gap-2 mb-2"></div>
                            <input type="text" id="email-input" 
                                placeholder="ejemplo@correo.com"
                                class="w-full border-0 bg-transparent p-1 text-sm text-gray-900 outline-none focus:ring-0 dark:text-white dark:placeholder-gray-400" />
                        </div>
                        <input type="hidden" id="custom-emails" name="custom_emails" />
                    </div>

                    <!-- Resumen -->
                    <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-950/30">
                        <div class="flex items-center gap-2">
                            <x-icon icon="info-circle" class="size-5 text-blue-600 dark:text-blue-400" />
                            <p class="text-sm font-medium text-blue-800 dark:text-blue-300">
                                Total de destinatarios: <span id="recipient-count" class="font-bold">0</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex justify-end gap-4">
                <x-button type="a" href="{{ route('admin.notifications.index') }}" typeButton="secondary" text="Cancelar"
                    size="normal" />
                <x-button type="submit" typeButton="primary" icon="paper-plane" text="Enviar Notificaciones"
                    size="normal" id="submit-btn" />
            </div>
        </form>
    </section>

    <!-- Modal de Progreso -->
    <div id="sending-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="w-full max-w-md rounded-lg bg-white p-6 dark:bg-gray-900">
            <div class="mb-4 text-center">
                <x-icon icon="spinner" class="mx-auto size-12 animate-spin text-primary-500" />
                <h3 class="mt-4 text-lg font-bold text-gray-900 dark:text-white">Enviando Notificaciones</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Las notificaciones están siendo procesadas en segundo plano.
                </p>
            </div>
            <div class="mb-4">
                <div class="h-4 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                    <div class="h-full w-1/3 animate-pulse bg-primary-500"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Inicializar TinyMCE
                hugerte.init({
                    selector: '#content',
                    height: 400,
                    menubar: false,
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'media', 'table', 'help', 'wordcount'
                    ],
                    toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | removeformat | code',
                    content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
                    skin: document.documentElement.classList.contains('dark') ? 'oxide-dark' : 'oxide',
                    content_css: document.documentElement.classList.contains('dark') ? 'dark' : 'default',
                    language_url: '/js/hugerte/lang/es.min.js',
                    language: 'es'
                });

                // Cambio de tipo de destinatario
                const recipientTypes = document.querySelectorAll('input[name="recipient_type"]');
                const sections = {
                    businesses: document.getElementById('businesses-section'),
                    users: document.getElementById('users-section'),
                    custom: document.getElementById('custom-section')
                };

                recipientTypes.forEach(radio => {
                    radio.addEventListener('change', function() {
                        Object.values(sections).forEach(section => section.classList.add('hidden'));
                        sections[this.value].classList.remove('hidden');
                        updateRecipientCount();
                    });
                });

                // Seleccionar/Deseleccionar todos
                document.getElementById('select-all-businesses').addEventListener('click', () => {
                    document.querySelectorAll('.business-checkbox').forEach(cb => cb.checked = true);
                    updateRecipientCount();
                });

                document.getElementById('deselect-all-businesses').addEventListener('click', () => {
                    document.querySelectorAll('.business-checkbox').forEach(cb => cb.checked = false);
                    updateRecipientCount();
                });

                document.getElementById('select-all-users').addEventListener('click', () => {
                    document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = true);
                    updateRecipientCount();
                });

                document.getElementById('deselect-all-users').addEventListener('click', () => {
                    document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = false);
                    updateRecipientCount();
                });

                // Actualizar contador en cambios
                document.querySelectorAll('.business-checkbox, .user-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', updateRecipientCount);
                });

                // Búsqueda de clientes (negocios)
                const searchBusinesses = document.getElementById('search-businesses');
                const businessItems = document.querySelectorAll('.business-item');

                searchBusinesses.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    
                    businessItems.forEach(item => {
                        const name = item.dataset.name;
                        const email = item.dataset.email;
                        const matches = name.includes(searchTerm) || email.includes(searchTerm);
                        item.style.display = matches ? 'flex' : 'none';
                    });
                });

                // Búsqueda de usuarios
                const searchUsers = document.getElementById('search-users');
                const userItems = document.querySelectorAll('.user-item');

                searchUsers.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    
                    userItems.forEach(item => {
                        const name = item.dataset.name;
                        const email = item.dataset.email;
                        const matches = name.includes(searchTerm) || email.includes(searchTerm);
                        item.style.display = matches ? 'flex' : 'none';
                    });
                });

                // Sistema de tags para correos personalizados
                const emailInput = document.getElementById('email-input');
                const emailTagsContainer = document.getElementById('email-tags');
                const customEmailsHidden = document.getElementById('custom-emails');
                let emailTags = [];

                function isValidEmail(email) {
                    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
                }

                function addEmailTag(email) {
                    email = email.trim().toLowerCase();
                    
                    if (!email) return;
                    
                    if (!isValidEmail(email)) {
                        alert('Por favor ingrese un correo electrónico válido');
                        return;
                    }
                    
                    if (emailTags.includes(email)) {
                        alert('Este correo ya ha sido agregado');
                        return;
                    }
                    
                    emailTags.push(email);
                    renderEmailTags();
                    updateCustomEmailsInput();
                    emailInput.value = '';
                    updateRecipientCount();
                }

                function removeEmailTag(email) {
                    emailTags = emailTags.filter(e => e !== email);
                    renderEmailTags();
                    updateCustomEmailsInput();
                    updateRecipientCount();
                }

                function renderEmailTags() {
                    emailTagsContainer.innerHTML = emailTags.map(email => `
                        <span class="inline-flex items-center gap-1 rounded-full bg-primary-100 px-3 py-1 text-sm text-primary-800 dark:bg-primary-900/30 dark:text-primary-300">
                            ${email}
                            <button type="button" onclick="window.removeEmailTag('${email}')" 
                                class="ml-1 rounded-full hover:bg-primary-200 dark:hover:bg-primary-800">
                                <svg class="size-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </span>
                    `).join('');
                }

                function updateCustomEmailsInput() {
                    customEmailsHidden.value = emailTags.join(',');
                }

                // Exponer función globalmente para el onclick
                window.removeEmailTag = removeEmailTag;

                emailInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === 'Tab') {
                        e.preventDefault();
                        addEmailTag(this.value);
                    } else if (e.key === 'Backspace' && !this.value && emailTags.length > 0) {
                        removeEmailTag(emailTags[emailTags.length - 1]);
                    }
                });

                function updateRecipientCount() {
                    const recipientType = document.querySelector('input[name="recipient_type"]:checked').value;
                    let count = 0;

                    if (recipientType === 'businesses') {
                        count = document.querySelectorAll('.business-checkbox:checked').length;
                    } else if (recipientType === 'users') {
                        count = document.querySelectorAll('.user-checkbox:checked').length;
                    } else if (recipientType === 'custom') {
                        count = emailTags.length;
                    }

                    document.getElementById('recipient-count').textContent = count;
                }

                // Enviar formulario
                document.getElementById('notification-form').addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const recipientType = document.querySelector('input[name="recipient_type"]:checked').value;
                    const subject = document.getElementById('subject').value.trim();
                    const content = hugerte.get('content').getContent();

                    if (!subject) {
                        alert('Por favor ingrese el asunto del correo');
                        return;
                    }

                    if (!content) {
                        alert('Por favor ingrese el contenido del mensaje');
                        return;
                    }

                    let recipients = [];

                    if (recipientType === 'businesses') {
                        recipients = Array.from(document.querySelectorAll('.business-checkbox:checked'))
                            .map(cb => cb.dataset.email);
                    } else if (recipientType === 'users') {
                        recipients = Array.from(document.querySelectorAll('.user-checkbox:checked'))
                            .map(cb => cb.dataset.email);
                    } else if (recipientType === 'custom') {
                        recipients = emailTags;
                    }

                    if (recipients.length === 0) {
                        alert('Por favor seleccione al menos un destinatario');
                        return;
                    }

                    // Mostrar modal de envío
                    document.getElementById('sending-modal').classList.remove('hidden');
                    document.getElementById('sending-modal').classList.add('flex');

                    try {
                        const response = await fetch('{{ route('admin.notifications.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                subject,
                                content,
                                recipient_type: recipientType,
                                recipients
                            })
                        });

                        const result = await response.json();

                        if (result.success) {
                            setTimeout(() => {
                                window.location.href = '{{ route('admin.notifications.index') }}';
                            }, 1500);
                        } else {
                            alert('Error: ' + result.message);
                            document.getElementById('sending-modal').classList.add('hidden');
                            document.getElementById('sending-modal').classList.remove('flex');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error al enviar las notificaciones');
                        document.getElementById('sending-modal').classList.add('hidden');
                        document.getElementById('sending-modal').classList.remove('flex');
                    }
                });

                // Inicializar contador
                updateRecipientCount();
            });
        </script>
    @endpush
@endsection
