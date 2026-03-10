import DataTable from "datatables.net";
import "datatables.net-bs5";
import "datatables.net-responsive";
import "datatables.net-responsive-bs5";

document.addEventListener("DOMContentLoaded", () => {
    const tableEl = document.getElementById("table-notificaciones");
    if (!tableEl) return;

    const table = new DataTable(tableEl, {
        language: {
            paginate: {
                first: "«",
                previous: "‹",
                next: "›",
                last: "»",
            },
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No se encontraron notificaciones",
            info: "Mostrando _START_ a _END_ de _TOTAL_",
            infoEmpty: "No hay registros",
            infoFiltered: "(filtrado de _MAX_)",
            search: "Buscar:",
        },
        responsive: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        pageLength: 10,
        order: [[0, "desc"]],
        columnDefs: [
            { targets: -1, orderable: false, searchable: false }
        ],

        initComplete() {
            const api = this.api();

            document.querySelectorAll("#column-search th input").forEach((input, index) => {
                input.addEventListener("keyup", function () {
                    if (api.column(index).search() !== this.value) {
                        api.column(index).search(this.value).draw();
                    }
                });
            });
        },
    });

    window.notificacionesDataTable = table;
});