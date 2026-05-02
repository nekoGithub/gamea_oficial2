/**
 * DataTables – Roles
 */
import DataTable from "datatables.net";
import "datatables.net-bs5";
import "datatables.net-responsive";
import "datatables.net-responsive-bs5";

document.addEventListener("DOMContentLoaded", () => {
    let tableRoles;

    // ================= CONFIG BASE =================
    const baseConfig = {
        language: {
            paginate: {
                first: "«",
                previous: "‹",
                next: "›",
                last: "»",
            },
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No se encontraron resultados",
            info: "Mostrando _START_ a _END_ de _TOTAL_",
            infoEmpty: "No hay registros",
            infoFiltered: "(filtrado de _MAX_)",
            search: "Buscar:",
        },
        responsive: true,
        lengthMenu: [[5, 8, 10, 15, 20], [5, 8, 10, 15, 20]],
        pageLength: 8,
        columnDefs: [
            { targets: -1, orderable: false, searchable: false },
            { targets: -1, orderable: false, searchable: false, responsivePriority: 1 , className: 'text-end'} // acciones
        ],
    };

    // ================= TABLA ROLES =================
    const rolesEl = document.getElementById("table-roles");
    if (rolesEl) {
        tableRoles = new DataTable(rolesEl, {
            ...baseConfig,
            order: [[0, "desc"]],
            rowId: row => `role-${row[0]}`,

            initComplete() {
                const api = this.api();

                document
                    .querySelectorAll("#column-search-roles th input")
                    .forEach((input, index) => {
                        input.addEventListener("keyup", function () {
                            if (api.column(index).search() !== this.value) {
                                api.column(index).search(this.value).draw();
                            }
                        });
                    });
            },
        });
    }

    // ================= EXPORTAR =================
    window.rolesDataTable = tableRoles;
});