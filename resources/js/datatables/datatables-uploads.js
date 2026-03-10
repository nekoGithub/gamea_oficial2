/**
 * DataTables – Uploads
 */
import DataTable from "datatables.net";
import "datatables.net-bs5";
import "datatables.net-responsive";
import "datatables.net-responsive-bs5";

document.addEventListener("DOMContentLoaded", () => {
    let tableTodos;
    let tablePendientes;
    let tableCompletados;
    let tableErrores;

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
        pageLength: 10,
        columnDefs: [
            { targets: -1, orderable: false, searchable: false, responsivePriority: 1 } // acciones
        ],
    };

    // ================= TABLA TODOS =================
    const todosEl = document.getElementById("table-todos");
    if (todosEl) {
        tableTodos = new DataTable(todosEl, {
            ...baseConfig,
            order: [[0, "desc"]],
            rowId: row => `upload-${row[0]}`,

            initComplete() {
                const api = this.api();

                document
                    .querySelectorAll("#column-search-todos th input")
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

    // ================= TABLA PENDIENTES =================
    const pendientesEl = document.getElementById("table-pendientes");
    if (pendientesEl) {
        tablePendientes = new DataTable(pendientesEl, {
            ...baseConfig,
            order: [[0, "desc"]],
            pageLength: 8,
        });
    }

    // ================= TABLA COMPLETADOS =================
    const completadosEl = document.getElementById("table-completados");
    if (completadosEl) {
        tableCompletados = new DataTable(completadosEl, {
            ...baseConfig,
            order: [[0, "desc"]],
            pageLength: 8,
        });
    }

    // ================= TABLA ERRORES =================
    const erroresEl = document.getElementById("table-errores");
    if (erroresEl) {
        tableErrores = new DataTable(erroresEl, {
            ...baseConfig,
            order: [[0, "desc"]],
            pageLength: 8,
        });
    }

    // ================= EXPORTAR =================
    window.uploadsDataTables = {
        todos: tableTodos,
        pendientes: tablePendientes,
        completados: tableCompletados,
        errores: tableErrores,
    };

    // ================= AJUSTE AL CAMBIAR TAB =================
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener("shown.bs.tab", e => {
            const target = e.target.getAttribute("href");
            
            if (target === "#tab-todos") {
                tableTodos?.columns.adjust().responsive.recalc();
            } else if (target === "#tab-pendientes") {
                tablePendientes?.columns.adjust().responsive.recalc();
            } else if (target === "#tab-completados") {
                tableCompletados?.columns.adjust().responsive.recalc();
            } else if (target === "#tab-errores") {
                tableErrores?.columns.adjust().responsive.recalc();
            }
        });
    });
});