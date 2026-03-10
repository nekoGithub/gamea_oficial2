/**
 * DataTables – Auditorías (Server-Side Processing)
 */
import DataTable from "datatables.net";
import "datatables.net-bs5";
import "datatables.net-responsive";
import "datatables.net-responsive-bs5";

document.addEventListener("DOMContentLoaded", () => {
    let tableAuditorias;

    // ================= TABLA AUDITORÍAS CON SERVER-SIDE =================
    const auditoriasEl = document.getElementById("table-auditorias");
    if (auditoriasEl) {
        tableAuditorias = new DataTable(auditoriasEl, {
            // ✅ Server-Side Processing
            processing: true,
            serverSide: true,
            ajax: {
                url: "/admin/auditorias/datatable",
                type: "GET",
                data: function (d) {
                    // Agregar filtros personalizados
                    d.modulo = document.getElementById('filtro-modulo').value;
                    d.accion = document.getElementById('filtro-accion').value;
                    d.usuario = document.getElementById('filtro-usuario').value;
                }
            },

            // ✅ Configuración de idioma
            language: {
                processing: "Procesando...",
                lengthMenu: "Mostrar _MENU_ registros",
                zeroRecords: "No se encontraron resultados",
                emptyTable: "No hay datos disponibles",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                search: "Buscar:",
                paginate: {
                    first: "«",
                    previous: "‹",
                    next: "›",
                    last: "»",
                },
            },

            // ✅ Configuración de tabla
            responsive: true,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            pageLength: 25,
            order: [[0, "desc"]], // Por ID descendente

            columnDefs: [
                { targets: -1, orderable: false, searchable: false, responsivePriority: 1 }
            ],

            // ✅ Búsqueda por columna individual
            initComplete: function () {
                const api = this.api();

                document
                    .querySelectorAll("#column-search-auditorias th input")
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

    // ================= FILTROS RÁPIDOS =================
    document.getElementById('filtro-modulo')?.addEventListener('change', function () {
        tableAuditorias.ajax.reload();
    });

    document.getElementById('filtro-accion')?.addEventListener('change', function () {
        tableAuditorias.ajax.reload();
    });

    document.getElementById('filtro-usuario')?.addEventListener('change', function () {
        tableAuditorias.ajax.reload();
    });

    document.getElementById('btn-limpiar-filtros')?.addEventListener('click', function () {
        document.getElementById('filtro-modulo').value = '';
        document.getElementById('filtro-accion').value = '';
        document.getElementById('filtro-usuario').value = '';

        // Limpiar búsquedas de columnas
        document.querySelectorAll("#column-search-auditorias th input").forEach(input => {
            input.value = '';
        });

        tableAuditorias.search('').columns().search('').ajax.reload();
    });

    // ================= EXPORTAR =================
    window.auditoriasDataTable = tableAuditorias;
});