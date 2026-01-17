 {{-- MODAL PRODUCTORES --}}
    <div class="modal fade" id="modal_productores" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color:hsl(213, 99%, 49%);">
                    <h5 class="modal-title">Productores</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle productores" id="productores" width="100%">
                            <thead class="text-white" style="background-color:hsl(213, 99%, 49%)">
                                <tr>
                                    <th class="d-none">ID</th>
                                    <th>Asignar a</th>
                                    <th>Teléfono</th>
                                    <th>E-mail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productores as $productor)
                                    <tr>
                                        <td class="d-none">{{ $productor->id }}</td>
                                        <td onclick="selectedRow(); productorData('{{ $productor->id }}')">{{ $productor->nombre }}</td>
                                        <td onclick="selectedRow(); productorData('{{ $productor->id }}')">{{ $productor->telefono }}</td>
                                        <td onclick="selectedRow(); productorData('{{ $productor->id }}')">{{ $productor->correo }}</td>
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