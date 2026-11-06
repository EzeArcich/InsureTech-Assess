import DataTable from 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.css';
import { tableOptions, connectTableSearch } from './table-options';

for (const table of document.querySelectorAll('[data-standard-table]')) {
    const columns = [...table.tHead.rows[0].cells].map((cell) => ({
        visible: !cell.classList.contains('d-none') && cell.style.display !== 'none',
        orderable: cell.textContent.trim() !== 'Acciones' && cell.textContent.trim() !== 'Asignación',
    }));
    const dataTable = new DataTable(table, {
        ...tableOptions,
        columns,
        rowCallback: function (row, data) {
            if (table.dataset.duplicateColumn === undefined) {
                return;
            }
            const index = Number(table.dataset.duplicateColumn);
            const values = this.api().column(index).data().toArray();
            row.classList.toggle('claim-duplicate', values.indexOf(data[index]) !== values.lastIndexOf(data[index]));
        },
    });
    connectTableSearch(dataTable, document.getElementById(table.dataset.searchInput || 'claims-global-search'));
}
