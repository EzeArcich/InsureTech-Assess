{{-- MODAL TALLERES --}}
    <div class="modal fade" id="modal_talleres" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color:hsl(213, 99%, 49%);">
                    <h5 class="modal-title">Talleres homologados</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle talleres" id="talleres" width="100%">
                            <thead class="text-white" style="background-color:hsl(213, 99%, 49%)">
                                <tr>
                                    <th class="d-none">ID</th>
                                    <th>Taller</th>
                                    <th>Teléfono</th>
                                    <th>E-mail</th>
                                    <th>Domicilio</th>
                                    <th>Localidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($talleres as $taller)
                                    <tr>
                                        <td class="d-none">{{ $taller->id }}</td>
                                        <td onclick="selectedRow3(); tallerData('{{ $taller->id }}')">{{ $taller->taller }}</td>
                                        <td onclick="selectedRow3(); tallerData('{{ $taller->id }}')">{{ $taller->telefonos }}</td>
                                        <td onclick="selectedRow3(); tallerData('{{ $taller->id }}')">{{ $taller->email }}</td>
                                        <td onclick="selectedRow3(); tallerData('{{ $taller->id }}')">{{ $taller->direccion }}</td>
                                        <td onclick="selectedRow3(); tallerData('{{ $taller->id }}')">{{ $taller->localidad }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Confirmar</button>
                </div>
            </div>
        </div>
    </div>