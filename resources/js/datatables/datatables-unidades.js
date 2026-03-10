/**
 * DataTables con búsqueda por columna para Unidades
 * Implementación con tabs (Activos y Papelera)
 */
import DataTable from "datatables.net";
import 'datatables.net-bs5';
import 'datatables.net-responsive';
import 'datatables.net-responsive-bs5';

document.addEventListener('DOMContentLoaded', () => {
    let tableActivos = null;
    let tablePapelera = null;

    // ==================== CONFIGURACIÓN COMÚN ====================
    const commonConfig = {
        language: {
            paginate: {
                first: '<i class="ti ti-chevrons-left"></i>',
                previous: '<i class="ti ti-chevron-left"></i>',
                next: '<i class="ti ti-chevron-right"></i>',
                last: '<i class="ti ti-chevrons-right"></i>'
            },
            lengthMenu: "Mostrar _MENU_ registros por página",
            zeroRecords: "No se encontraron resultados",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "No hay registros disponibles",
            infoFiltered: "(filtrado de _MAX_ registros totales)",
            search: "Buscar:",
            loadingRecords: "Cargando...",
            processing: "Procesando..."
        },
        responsive: true,
        pageLength: 8,
        lengthMenu: [[5, 8, 10, 15, 20], [5, 8, 10, 15, 20]],
        order: [[0, 'desc']], // Ordenar por ID descendente
        columnDefs: [
            {
                targets: -1, // Última columna (Acciones)
                orderable: false,
                searchable: false
            }
        ]
    };

    // ==================== INICIALIZAR TABLA ACTIVOS ====================
    const tableActivosElement = document.getElementById('table-activos');
    if (tableActivosElement) {
        tableActivos = new DataTable(tableActivosElement, {
            ...commonConfig,
            rowId: function (row) {
                return 'unidad-' + row[0];
            },
            initComplete: function () {
                const api = this.api();

                // Prevenir ordenamiento al hacer clic en los inputs
                document.querySelectorAll('#column-search-activos th').forEach((th) => {
                    th.addEventListener('click', function (e) {
                        e.stopPropagation();
                    });
                });

                // Configurar búsqueda por columna
                document.querySelectorAll('#column-search-activos th input').forEach((input, index) => {
                    input.addEventListener('click', function (e) {
                        e.stopPropagation();
                    });

                    input.addEventListener('keyup', function () {
                        const value = this.value;
                        if (api.column(index).search() !== value) {
                            api.column(index).search(value).draw();
                        }
                    });
                });
            }
        });
    }

    // ==================== INICIALIZAR TABLA PAPELERA ====================
    const tablePapeleraElement = document.getElementById('table-papelera');
    if (tablePapeleraElement) {
        tablePapelera = new DataTable(tablePapeleraElement, {
            ...commonConfig,
            pageLength: 5,
            rowId: function (row) {
                return 'unidad-' + row[0];
            },

            initComplete: function () {
                const api = this.api();

                // Prevenir ordenamiento al hacer clic en los inputs
                document.querySelectorAll('#column-search-papelera th').forEach((th) => {
                    th.addEventListener('click', function (e) {
                        e.stopPropagation();
                    });
                });

                // Configurar búsqueda por columna
                document.querySelectorAll('#column-search-papelera th input').forEach((input, index) => {
                    input.addEventListener('click', function (e) {
                        e.stopPropagation();
                    });

                    input.addEventListener('keyup', function () {
                        const value = this.value;
                        if (api.column(index).search() !== value) {
                            api.column(index).search(value).draw();
                        }
                    });
                });
            }
        });
    }

    window.unidadesDataTables = {
        activos: tableActivos,
        papelera: tablePapelera
    };

    // ==================== REDIBUJAR AL CAMBIAR DE TAB ====================
    document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (e) {
            const targetId = e.target.getAttribute('href');

            if (targetId === '#tab-activos' && tableActivos) {
                tableActivos.columns.adjust().responsive.recalc();
            } else if (targetId === '#tab-papelera' && tablePapelera) {
                tablePapelera.columns.adjust().responsive.recalc();
            }
        });
    });

    // ==================== EXPORTAR INSTANCIAS PARA USO EXTERNO ====================
    window.unidadesDataTables = {
        activos: tableActivos,
        papelera: tablePapelera,
        refresh: function () {
            if (tableActivos) tableActivos.draw();
            if (tablePapelera) tablePapelera.draw();
        }
    };
});