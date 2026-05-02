<!-- App js -->
@vite(['resources/js/app.js'])

@yield('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {

        function actualizarCheckEsquema() {
            // Ocultar todos
            document.querySelectorAll('.color-scheme-check').forEach(el => {
                el.style.display = 'none';
            });
            // Mostrar solo el seleccionado
            const checked = document.querySelector('input[name="data-bs-theme"]:checked');
            if (checked) {
                const label = document.querySelector(`label[for="${checked.id}"]`);
                if (label) {
                    const icon = label.querySelector('.color-scheme-check');
                    if (icon) icon.style.display = 'block';
                }
            }
        }

        // Al cambiar
        document.querySelectorAll('input[name="data-bs-theme"]').forEach(radio => {
            radio.addEventListener('change', actualizarCheckEsquema);
        });

        // Al cargar la página
        actualizarCheckEsquema();
    });
</script>
