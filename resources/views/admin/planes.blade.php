@extends('layouts.app')
@include('admin.layouts.navbar')
@section('content')
    <div class="container mt-5 col-md-10">
        <div class="row my-4">
            <div class="col-md-4" id="planes">
                <div class="card shadow card-body mt-3">
                    <form method="POST" action="{{ route('admin.planes.store') }}" id="planesForm">
                        @csrf
                        <h4>Nuevo Plan</h4>
                        <div class="form-group mb-3">
                            <label for="nombrePlan">Nombre</label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre"
                                value="{{ old('nombre') }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="limiteDTEs">Límite de DTEs al mes</label>
                            <input type="number" class="form-control @error('limite') is-invalid @enderror" id="limite" name="limite"
                                value="{{ old('limite') }}" required min="1">
                            @error('limite')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="precio">Precio</label>
                            <input type="number" class="form-control @error('precio') is-invalid @enderror" id="precio" name="precio"
                                value="{{ old('precio') }}" required step="0.01" min="0">
                            @error('precio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="precio_adicional">Precio DTE Adicional</label>
                            <input type="number" class="form-control @error('precio_adicional') is-invalid @enderror" id="precio_adicional" name="precio_adicional"
                                value="{{ old('precio_adicional') }}" required step="0.01" min="0">
                            @error('precio_adicional')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3 text-center">
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <button type="submit" class="btn btn-secondary d-none" id="btnCancelar">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow card-body mt-3">
                    <h4 class="mt-4">Planes</h4>
                    {{ $dataTable->table() }}
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush
<script>
    function editarPlan(id){
        axios.get(`/admin/planes/${id}`)
            .then(response => {
                const data = response.data;
                document.getElementById('nombre').value = data.nombre;
                document.getElementById('limite').value = data.limite;
                document.getElementById('precio').value = data.precio;
                // Change form action
                document.getElementById('planesForm').action = `{{ route('admin.planes.update', '') }}/${id}`;
                // Change submit button text
                document.querySelector('button[type="submit"]').textContent = 'Actualizar';
                // Show cancel button
                document.getElementById('btnCancelar').classList.remove('d-none');
            })
            .catch(error => {
                console.error(error);
            });
    }

    function cancelar(){
        document.getElementById('nombre').value = '';
        document.getElementById('limite').value = '';
        document.getElementById('precio').value = '';
        // Change form action
        document.getElementById('planesForm').action = "{{ route('admin.planes.store') }}";
        // Change submit button text
        document.querySelector('button[type="submit"]').textContent = 'Guardar';
        // Hide cancel button
        document.getElementById('btnCancelar').classList.add('d-none');
    }
</script>
@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
        });
    </script>
@endif