@extends('layouts.app')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Crear Rol</h3>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-dark alert-dismissible fade show" role="alert">
                                <strong>¡Revise los campos!</strong>

                                <div class="mt-2">
                                    @foreach ($errors->all() as $error)
                                        <span class="badge text-bg-danger me-1">{{ $error }}</span>
                                    @endforeach
                                </div>

                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('roles.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nombre del Rol:</label>
                                        <input type="text"
                                               name="name"
                                               id="name"
                                               class="form-control"
                                               value="{{ old('name') }}">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Permisos para este Rol:</label>

                                        <div class="mt-2">
                                            @foreach($permission as $value)
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           name="permission[]"
                                                           id="perm_{{ $value->id }}"
                                                           value="{{ $value->id }}"
                                                           {{ in_array($value->id, old('permission', [])) ? 'checked' : '' }}>

                                                    <label class="form-check-label" for="perm_{{ $value->id }}">
                                                        {{ $value->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
