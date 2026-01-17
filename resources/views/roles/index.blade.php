@extends('layouts.app')

@section('content')
<section class="section">
    <div class="section-header">
        <h3 class="page__heading">Roles</h3>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">

                        @hasrole('CEO')
                            <a class="btn btn-primary" href="{{ route('roles.create') }}">Nuevo</a>
                        @endhasrole

                        <table class="table table-striped mt-2">
                            <thead style="background: linear-gradient(to right, hsla(234, 94%, 39%, 0.977), #023cfb, hsl(213, 99%, 49%));">
                                <tr>
                                    <th style="color:#000;">Rol</th>
                                    <th style="color:#000;">Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($roles as $role)
                                    <tr>
                                        <td>{{ $role->name }}</td>
                                        <td>
                                            @hasrole('CEO')
                                                <a class="btn btn-primary" href="{{ route('roles.edit', $role->id) }}">
                                                    Editar
                                                </a>
                                            @endhasrole

                                            @hasrole('CEO')
                                                <form action="{{ route('roles.destroy', $role->id) }}"
                                                      method="POST"
                                                      style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        Borrar
                                                    </button>
                                                </form>
                                            @endhasrole
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-end">
                            {{ $roles->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
