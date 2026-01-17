@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h3 class="page__heading">Editar Usuario</h3>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                     
                        @if ($errors->any())                                                
                            <div class="alert alert-dark alert-dismissible fade show" role="alert">
                            <strong>¡Revise los campos!</strong>                        
                                @foreach ($errors->all() as $error)                                    
                                    <span class="badge badge-danger">{{ $error }}</span>
                                @endforeach                        
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            </div>
                        @endif

                        <form action="{{ route('usuarios.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            {{-- Aquí van tus inputs --}}
                            {{-- Ejemplo: --}}
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre:</label>
                                <input type="text" name="name" id="name"
                                    value="{{ old('name', $user->name) }}"
                                    class="form-control">
                            </div>

                            <button type="submit" class="btn btn-primary">
                                Actualizar
                            </button>
                        </form>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre</label>
                                    <input type="text"
                                        name="name"
                                        id="name"
                                        class="form-control"
                                        value="{{ old('name', $user->name ?? '') }}">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input type="email"
                                        name="email"
                                        id="email"
                                        class="form-control"
                                        value="{{ old('email', $user->email ?? '') }}">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password"
                                        name="password"
                                        id="password"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="confirm-password" class="form-label">Confirmar Password</label>
                                    <input type="password"
                                        name="confirm-password"
                                        id="confirm-password"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="roles" class="form-label">Roles</label>
                                    <select name="roles[]" id="roles" class="form-select" multiple>
                                        @foreach($roles as $id => $name)
                                            <option value="{{ $id }}"
                                                {{ in_array($id, old('roles', $userRole ?? [])) ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary text-dark">
                                    Guardar
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
