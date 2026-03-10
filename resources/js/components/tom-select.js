import TomSelect from "tom-select";

export function initTomSelect(selector, options = {}) {
    document.querySelectorAll(selector).forEach(el => {
        if (!el || el.tagName !== 'SELECT') return;
        if (el.tomselect) return;

        // placeholder seguro
        const placeholder =
            el.getAttribute('placeholder') ||
            el.dataset.placeholder ||
            'Seleccione opciones';

        new TomSelect(el, {
            plugins: ['remove_button'],
            maxItems: null,
            create: false,
            placeholder: placeholder,

            onInitialize() {
                // si ya tiene valores (editar), ocultar placeholder
                if (this.items.length > 0) {
                    this.control_input.placeholder = '';
                }
            },

            onItemAdd() {
                this.control_input.placeholder = '';
            },

            onItemRemove() {
                if (this.items.length === 0) {
                    this.control_input.placeholder = placeholder;
                }
            },

            ...options
        });
    });
}
