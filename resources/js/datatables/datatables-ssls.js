/**
 * DataTables – SSLs (Activos y Papelera)
 */
import DataTable from "datatables.net";
import "datatables.net-bs5";
import "datatables.net-responsive";
import "datatables.net-responsive-bs5";

document.addEventListener("DOMContentLoaded", () => {
    let tableActivos;
    let tablePapelera;

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
            { targets: -1, orderable: false, searchable: false, responsivePriority: 1 } // acciones
        ],
    };

    // ================= TABLA ACTIVOS =================
    const activosEl = document.getElementById("table-activos");
    if (activosEl) {
        tableActivos = new DataTable(activosEl, {
            ...baseConfig,
            order: [[0, "desc"]],
            rowId: row => `ssl-${row[0]}`,

            initComplete() {
                const api = this.api();

                document
                    .querySelectorAll("#column-search-activos th input")
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

    // ================= TABLA PAPELERA =================
    const papeleraEl = document.getElementById("table-papelera");
    if (papeleraEl) {
        tablePapelera = new DataTable(papeleraEl, {
            ...baseConfig,
            pageLength: 5,
            rowId: row => `ssl-${row[0]}`,

            initComplete() {
                const api = this.api();

                document
                    .querySelectorAll("#column-search-papelera th input")
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
    window.sslsDataTables = {
        activos: tableActivos,
        papelera: tablePapelera,
    };

    // ================= AJUSTE AL CAMBIAR TAB =================
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener("shown.bs.tab", e => {
            if (e.target.getAttribute("href") === "#tab-activos") {
                tableActivos?.columns.adjust().responsive.recalc();
            } else {
                tablePapelera?.columns.adjust().responsive.recalc();
            }
        });
    });
});