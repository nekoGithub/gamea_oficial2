@extends('layouts.base', ['title' => 'Iniciar Sesión'])

@section('css')
    <style>
        /* ── Ken Burns ── */
        .card-side-img {
            position: relative !important;
            overflow: hidden !important;
        }

        #kenburns-bg {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            background-image: url('/img/gamea.jpg') !important;
            background-size: cover !important;
            background-position: center !important;
            animation: kenburns 25s ease-in-out infinite alternate;
        }

        @keyframes kenburns {
            0% {
                transform: scale(1.2) translate(0, 0);
            }

            25% {
                transform: scale(1.3) translate(-5%, 5%);
            }

            50% {
                transform: scale(1.1) translate(5%, -5%);
            }

            75% {
                transform: scale(1.25) translate(-3%, 3%);
            }

            100% {
                transform: scale(1.15) translate(3%, -3%);
            }
        }

        /* ── Overlay ── */
        .gamea-overlay {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            z-index: 1 !important;
        }

        /* ── Typewriter ── */
        .welcome-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #007B8A !important;
            /* WCAG AA: ratio ~4.98:1 ✓ */
            line-height: 1.6;
            text-align: center;
            margin: 0 auto 1.5rem;
            min-height: 90px;
        }

        .typewriter-line {
            display: block;
            min-height: 1.6em;
            white-space: nowrap;
        }

        .cursor {
            display: inline;
            border-right: 3px solid #007B8A;
            animation: blink 0.7s infinite;
            margin-left: 2px;
        }

        @keyframes blink {

            0%,
            50% {
                border-color: #007B8A;
            }

            51%,
            100% {
                border-color: transparent;
            }
        }

        /* ── Subtítulo ── */
        .auth-subtitle {
            font-size: 15px;
            line-height: 1.6;
            color: #595959 !important;
            /* WCAG AA: ratio ~7:1 ✓ */
        }

        /* ── Labels ── */
        .form-label {
            font-size: 15px !important;
            font-weight: 600 !important;
            color: #495057 !important;
            margin-bottom: 10px !important;
        }

        .required-mark {
            color: #C0392B !important;
            /* ratio 5.1:1 ✓ */
            font-weight: 700;
        }

        /* ── Inputs ── */
        .form-control-lg {
            font-size: 16px !important;
            padding: 14px 16px !important;
            height: auto !important;
            line-height: 1.5 !important;
        }

        .form-control::placeholder {
            color: #767676 !important;
            /* WCAG AA: ratio ~4.54:1 ✓ */
            opacity: 1 !important;
            font-size: 15px !important;
        }

        .form-control:focus {
            border-color: #007B8A !important;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 138, 0.25) !important;
            transition: all 0.3s ease;
        }

        /* ── Checkbox ── */
        .form-check-label {
            font-size: 15px !important;
            margin-left: 4px;
            color: #595959 !important;
            /* WCAG AA: ratio ~7:1 ✓ */
        }

        .form-check-input {
            width: 18px !important;
            height: 18px !important;
            margin-top: 2px !important;
        }

        .form-check-input:checked {
            background-color: #007B8A !important;
            border-color: #007B8A !important;
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 138, 0.25) !important;
        }

        /* ── Enlaces ── */
        a {
            color: #007B8A !important;
            /* WCAG AA ✓ */
            transition: color 0.3s ease;
            font-size: 15px !important;
        }

        a:hover {
            color: #005F6B !important;
        }

        /* ── Botón ── */
        .btn-primary {
            background-color: #D32F2F !important;
            border-color: #D32F2F !important;
            padding: 16px !important;
            font-weight: 600 !important;
            font-size: 16px !important;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
        }

        .btn-primary:hover {
            background-color: #B71C1C !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(211, 47, 47, 0.4);
        }

        /* ── Footer ── */
        .auth-footer {
            font-size: 14px;
        }

        .auth-footer small,
        .auth-footer small span {
            font-size: 13px;
            color: #595959 !important;
            /* WCAG AA: ratio ~7:1 ✓ */
        }

        /* ── Espaciado ── */
        .mb-custom {
            margin-bottom: 1.5rem !important;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .card-side-img {
                min-height: 300px;
            }

            .welcome-title {
                font-size: 1.2rem;
            }

            .cursor {
                height: 1.2rem;
            }

            .form-label {
                font-size: 14px !important;
            }

            .form-control-lg {
                font-size: 15px !important;
                padding: 12px 14px !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="auth-box p-0 w-100">
        <div class="row w-100 g-0">
            <!-- IZQUIERDA: Formulario -->
            <div class="col-md-auto">
                <div class="card auth-box-form border-0 mb-0">
                    <div class="card-body min-vh-100 d-flex flex-column justify-content-center px-4 py-5">

                        <!-- Logo -->
                        <div class="auth-brand mb-4 text-center">
                            <img alt="Logo GAMEA" src="/img/logo.png" style="height: 60px;" />
                        </div>

                        <div class="mt-auto">
                            <!-- Título con typewriter en 2 líneas -->
                            <div class="welcome-title">
                                <span class="typewriter-line" id="line1"></span>
                                <span class="typewriter-line" id="line2"></span>
                            </div>

                            <p class="text-center mb-4 auth-subtitle">
                                Sistema de Gestión de Activos Tecnológicos<br>
                                Ingrese sus credenciales para continuar
                            </p>

                            <x-validation-errors class="mb-4" />

                            @session('status')
                                <div class="alert alert-success mb-4">{{ $value }}</div>
                            @endsession

                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="mb-custom">
                                    <label class="form-label" for="email">
                                        Correo Electrónico <span class="required-mark">*</span>
                                    </label>
                                    <input class="form-control form-control-lg" id="email" name="email" type="email"
                                        placeholder="usuario@ejemplo.com" value="{{ old('email') }}" required autofocus />
                                </div>

                                <div class="mb-custom">
                                    <label class="form-label" for="password">
                                        Contraseña <span class="required-mark">*</span>
                                    </label>
                                    <input class="form-control form-control-lg" id="password" name="password"
                                        type="password" placeholder="••••••••" required />
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    

                                </div>

                                <div class="d-grid mb-3">
                                    <button class="btn btn-primary btn-lg" type="submit">
                                        Iniciar Sesión
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="mt-auto pt-5 auth-footer">
                            <p class="text-center text-muted mb-0">
                                <small>
                                    © {{ date('Y') }} GAMEA - Gobierno Autónomo Municipal de El Alto<br>
                                    <span class="fw-semibold">Gestión de Sistemas</span>
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DERECHA: Edificio GAMEA con Ken Burns Effect -->
            <div class="col">
                <div class="h-100 position-relative card-side-img rounded-0 overflow-hidden">
                    <!-- Imagen con Ken Burns -->
                    <div id="kenburns-bg"></div>
                    <!-- Overlay transparente -->
                    <div class="gamea-overlay"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Efecto Typewriter en 2 líneas con cursor inline
        const line1Element = document.getElementById('line1');
        const line2Element = document.getElementById('line2');

        const line1Text = 'UNIDAD DE ADMINISTRACIÓN';
        const line2Text = 'DE SISTEMAS DE INFORMACIÓN';

        let line1Index = 0;
        let line2Index = 0;
        let isDeleting = false;
        let deletingLine = 0; // 0 = ninguna, 1 = línea 1, 2 = línea 2

        function updateDisplay() {
            // Actualizar línea 1
            if (line1Index > 0 || (!isDeleting && deletingLine === 0)) {
                const showCursor1 = !isDeleting && deletingLine === 0 && line1Index < line1Text.length;
                line1Element.innerHTML = line1Text.substring(0, line1Index) + (showCursor1 ?
                    '<span class="cursor"></span>' : '');
            } else {
                line1Element.innerHTML = '';
            }

            // Actualizar línea 2
            if (line2Index > 0 || (line1Index === line1Text.length && !isDeleting && deletingLine === 0)) {
                const showCursor2 = !isDeleting && deletingLine === 0 && line1Index === line1Text.length && line2Index <
                    line2Text.length;
                const showCursor2Delete = isDeleting && deletingLine === 2;
                line2Element.innerHTML = line2Text.substring(0, line2Index) + ((showCursor2 || showCursor2Delete) ?
                    '<span class="cursor"></span>' : '');
            } else {
                line2Element.innerHTML = '';
            }

            // Mostrar cursor al borrar línea 1
            if (isDeleting && deletingLine === 1 && line1Index > 0) {
                line1Element.innerHTML = line1Text.substring(0, line1Index) + '<span class="cursor"></span>';
            }
        }

        function typeWriter() {
            // FASE 1: Escribir línea 1
            if (!isDeleting && deletingLine === 0 && line1Index < line1Text.length) {
                line1Index++;
                updateDisplay();
                setTimeout(typeWriter, 100);
            }
            // FASE 2: Escribir línea 2
            else if (!isDeleting && deletingLine === 0 && line1Index === line1Text.length && line2Index < line2Text
                .length) {
                line2Index++;
                updateDisplay();
                setTimeout(typeWriter, 100);
            }
            // FASE 3: Pausa antes de borrar
            else if (!isDeleting && line2Index === line2Text.length) {
                isDeleting = true;
                updateDisplay();
                setTimeout(typeWriter, 2000);
            }
            // FASE 4: Borrar línea 2
            else if (isDeleting && deletingLine === 0) {
                deletingLine = 2;
                updateDisplay();
                setTimeout(typeWriter, 50);
            } else if (isDeleting && deletingLine === 2 && line2Index > 0) {
                line2Index--;
                updateDisplay();
                setTimeout(typeWriter, 50);
            }
            // FASE 5: Borrar línea 1
            else if (isDeleting && deletingLine === 2 && line2Index === 0) {
                deletingLine = 1;
                updateDisplay();
                setTimeout(typeWriter, 100);
            } else if (isDeleting && deletingLine === 1 && line1Index > 0) {
                line1Index--;
                updateDisplay();
                setTimeout(typeWriter, 50);
            }
            // FASE 6: Reiniciar
            else if (isDeleting && line1Index === 0) {
                isDeleting = false;
                deletingLine = 0;
                updateDisplay();
                setTimeout(typeWriter, 500);
            }
        }

        // Iniciar el efecto al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(typeWriter, 500);
        });
    </script>
@endsection
