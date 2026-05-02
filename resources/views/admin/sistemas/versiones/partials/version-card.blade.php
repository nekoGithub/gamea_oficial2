<div class="col-lg-3 col-md-4 col-sm-6 mb-3">
    <div class="card version-card h-100" data-estado="{{ $version->estado }}" data-id="{{ $version->id }}">
        <div class="card-body text-center position-relative">

            {{-- Badge ACTUAL si es la versión actual --}}
            @if ($version->es_actual)
                <span class="badge badge-actual bg-success">
                    <i class="ti ti-check-circle me-1"></i>ACTUAL
                </span>
            @endif

            {{-- Avatar/Imagen --}}
            <div class="mb-3">
                @if ($version->imagen)
                    <img src="{{ asset('storage/' . $version->imagen) }}" alt="Versión {{ $version->numero_version }}"
                        class="version-avatar">
                @else
                    <img src="{{ asset('img/logo.png') }}" alt="Versión {{ $version->numero_version }}"
                        class="version-avatar">
                @endif

            </div>

            {{-- Número de Versión y Estado --}}
            <h5 class="card-title mb-1">
                <span class="version-number">v{{ $version->numero_version }}</span>
            </h5>

            {{-- Estado --}}
            <p class="text-muted small mb-2">
                @if ($version->estado === 'estable')
                    <span class="badge bg-success-subtle text-success">Estable</span>
                @elseif($version->estado === 'beta')
                    <span class="badge bg-warning-subtle text-warning">Beta</span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary">Deprecated</span>
                @endif
            </p>

            {{-- Descripción --}}
            @if ($version->descripcion)
                <p class="text-muted small mb-3">
                    {{ Str::limit($version->descripcion, 50) }}
                </p>
            @endif

            {{-- Usuario/Email --}}
            <p class="text-muted small mb-3">
                <i class="ti ti-user-circle me-1"></i>
                {{ $version->publicadoPor->name ?? 'Desconocido' }}
            </p>

            {{-- Botones de Acción --}}
            <div class="action-buttons mb-3">
                @can('admin.versiones.edit')
                    <a href="{{ route('admin.sistemas.versiones.edit', [$sistema, $version]) }}"
                        class="btn btn-sm btn-primary">
                        <i class="ti ti-edit"></i>
                    </a>
                @endcan

                @can('admin.versiones.actual')
                    @if (!$version->es_actual)
                        <button class="btn btn-sm btn-outline-success marcar-actual-btn" data-id="{{ $version->id }}"
                            title="Marcar como actual">
                            <i class="ti ti-check"></i>
                        </button>
                    @endif
                @endcan

                @can('admin.versiones.destroy')
                    <button class="btn btn-sm btn-outline-danger delete-version-btn" data-id="{{ $version->id }}">
                        <i class="ti ti-trash"></i>
                    </button>
                @endcan
            </div>

            {{-- Estadísticas --}}
            <div class="row border-top pt-3">
                <div class="col-4 stats-item">
                    <div class="stats-number">{{ $version->tecnologias->count() }}</div>
                    <div class="stats-label">Tecnologías</div>
                </div>
                <div class="col-4 stats-item">
                    <div class="stats-number">{{ $version->servidores->count() }}</div>
                    <div class="stats-label">Servidores</div>
                </div>
                <div class="col-4 stats-item">
                    <div class="stats-number">{{ $version->basesDatos->count() }}</div>
                    <div class="stats-label">BD</div>
                </div>
            </div>

            {{-- Archivos disponibles --}}
            @can('admin.versiones.show')
                @if ($version->codigo_fuente || $version->manual_tecnico || $version->manual_usuario || $version->archivo_bd)
                    <div class="mt-3 text-start">
                        @if ($version->codigo_fuente)
                            <a href="{{ route('admin.sistemas.versiones.descargar', [$sistema, $version, 'codigo_fuente']) }}"
                                class="btn btn-sm btn-light w-100 mb-1">
                                <i class="ti ti-code me-1"></i> Código Fuente
                            </a>
                        @endif
                        @if ($version->manual_tecnico)
                            <a href="{{ route('admin.sistemas.versiones.descargar', [$sistema, $version, 'manual_tecnico']) }}"
                                class="btn btn-sm btn-light w-100 mb-1">
                                <i class="ti ti-book me-1"></i> Manual Técnico
                            </a>
                        @endif
                        @if ($version->manual_usuario)
                            <a href="{{ route('admin.sistemas.versiones.descargar', [$sistema, $version, 'manual_usuario']) }}"
                                class="btn btn-sm btn-light w-100 mb-1">
                                <i class="ti ti-book-2 me-1"></i> Manual Usuario
                            </a>
                        @endif
                        @if ($version->archivo_bd)
                            <a href="{{ route('admin.sistemas.versiones.descargar', [$sistema, $version, 'archivo_bd']) }}"
                                class="btn btn-sm btn-light w-100">
                                <i class="ti ti-database me-1"></i> Base de Datos
                            </a>
                        @endif
                    </div>
                @endif
            @endcan

            {{-- Última actualización - SEGURO --}}
            <div class="mt-3">
                <small class="text-muted">
                    <i class="ti ti-clock me-1"></i>
                    @php
                        $fecha =
                            $version->fecha_lanzamiento instanceof \Carbon\Carbon
                                ? $version->fecha_lanzamiento
                                : \Carbon\Carbon::parse($version->fecha_lanzamiento);
                    @endphp
                    Lanzada {{ $fecha->diffForHumans() }}
                </small>
            </div>

        </div>
    </div>
</div>
