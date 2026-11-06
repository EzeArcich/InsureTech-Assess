import DataTable from 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.css';
import { tableOptions, connectTableSearch } from './table-options';

const table = document.getElementById('all-claims');

if (table) {
    const dataTable = new DataTable(table, {
        ...tableOptions,
        processing: true,
        serverSide: true,
        ajax: table.dataset.source,
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        order: [[1, 'desc']],
        searchDelay: 350,
        columns: [
            {
                data: null,
                name: '',
                orderable: false,
                searchable: false,
                width: '20px',
                render: (data, type, row) => type === 'display'
                    ? `<input type="checkbox" class="claims-row-check" aria-label="Seleccionar siniestro ${Number(row.id)}">`
                    : '',
            },
            { data: 'id', name: 'claims.id', visible: false },
            { data: 'claim_number', name: 'claims.claim_number', defaultContent: '-' },
            { data: 'company_name', name: 'companies.name', defaultContent: '-' },
            { data: 'client_name', name: 'clients.name', defaultContent: '-', render: (name, type, row) => {
                const id = Number(row.client_id);
                return type === 'display' && Number.isSafeInteger(id) && id > 0
                    ? `<a class="person-link" href="${table.dataset.clientUrl.replace('__ID__', id)}">${name || 'Ver asegurado'}</a>` : name;
            } },
            { data: 'license_plate', name: 'claims.license_plate', defaultContent: '-' },
            {
                data: 'status', name: 'claims.status', defaultContent: '-',
                render: (status, type) => {
                    if (type !== 'display') {
                        return status;
                    }
                    const statuses = {
                        Pendiente: ['pending', 'Pendiente'],
                        Ausente: ['absent', 'Ausente'],
                        Coordinado: ['coordinated', 'Coordinado'],
                        Peritando: ['inspecting', 'Peritando'],
                        IPgestion: ['progress', 'En gestión'],
                    };
                    const [style, label] = statuses[status] ?? ['absent', status ?? '-'];
                    return `<span class="status-badge status-badge--${style}">${label}</span>`;
                },
            },
            { data: 'inspector_name', name: 'inspectors.name', defaultContent: '-', render: (name, type, row) => {
                const id = Number(row.inspector_id);
                return type === 'display' && Number.isSafeInteger(id) && id > 0
                    ? `<a class="person-link" href="${table.dataset.inspectorUrl.replace('__ID__', id)}">${name || 'Ver inspector'}</a>` : name;
            } },
            { data: 'created_at', name: 'claims.created_at', render: (value) => value ? value.slice(0, 10) : '-' },
        ],

    });

    const globalSearch = document.getElementById('claims-global-search');
    const selectAll = document.getElementById('claims-select-all');
    connectTableSearch(dataTable, globalSearch);

    dataTable.on('draw', () => {
        selectAll.checked = false;
        selectAll.indeterminate = false;
    });

    selectAll.addEventListener('change', () => {
        table.querySelectorAll('.claims-row-check').forEach((checkbox) => {
            checkbox.checked = selectAll.checked;
            checkbox.closest('tr').classList.toggle('is-selected', checkbox.checked);
        });
    });

    table.addEventListener('change', (event) => {
        if (!event.target.matches('.claims-row-check')) {
            return;
        }
        event.target.closest('tr').classList.toggle('is-selected', event.target.checked);
        const checkboxes = [...table.querySelectorAll('.claims-row-check')];
        const checkedCount = checkboxes.filter((checkbox) => checkbox.checked).length;
        selectAll.checked = checkedCount === checkboxes.length;
        selectAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
    });
}

