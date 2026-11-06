export const tableOptions = {
    pageLength: 25,
    lengthMenu: [10, 25, 50, 100],
    order: [],
    language: {
        search: 'Buscar:',
        lengthMenu: 'Mostrar _MENU_ registros',
        info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
        infoEmpty: 'No hay registros para mostrar',
        infoFiltered: '(filtrados de _MAX_ registros)',
        zeroRecords: 'No se encontraron registros',
        emptyTable: 'Todavía no hay registros cargados',
        processing: 'Cargando...',
        loadingRecords: 'Cargando...',
        paginate: { first: '«', last: '»', next: 'Siguiente ›', previous: '‹ Anterior' },
    },
};

export function connectTableSearch(dataTable, input) {
    if (!input) {
        return;
    }
    let timer;
    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => dataTable.search(input.value).draw(), 350);
    });
    dataTable.on('search', () => {
        if (input.value !== dataTable.search()) {
            input.value = dataTable.search();
        }
    });
}
