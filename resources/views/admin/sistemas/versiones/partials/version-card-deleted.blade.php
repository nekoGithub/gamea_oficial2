<div class="col-lg-3 col-md-4 col-sm-6 mb-3">
    <div class="card version-card h-100 border-secondary bg-light bg-opacity-10">
        <div class="card-body text-center position-relative opacity-75">

            {{-- Avatar/Imagen con filtro gris --}}
            <div class="mb-3">
                <img src="{{ $version->imagen_url }}" alt="Versión {{ $version->numero_version }}" class="version-avatar"
                    style="filter: grayscale(100%);">
            </div>

            {{-- Número de Versión --}}
            <h5 class="card-title mb-1 text-muted">
                <span class="version-number">v{{ $version->numero_version }}</span>
            </h5>

            {{-- Estado --}}
            <p class="text-muted small mb-2">
                <span class="badge bg-secondary-subtle text-secondary">Eliminada</span>
            </p>

            {{-- Descripción --}}
            @if ($version->descripcion)
                <p class="text-muted small mb-3">
                    {{ Str::limit($version->descripcion, 50) }}
                </p>
            @endif

            {{-- Usuario --}}
            <p class="text-muted small mb-3">
                <i class="ti ti-user-circle me-1"></i>
                {{ $version->publicadoPor->name ?? 'Desconocido' }}
            </p>

            {{-- Botón de Restaurar --}}
            <div class="action-buttons mb-3">
                @can('admin.versiones.restore')
                    <button class="btn btn-sm btn-outline-primary restore-version-btn" data-id="{{ $version->id }}">
                        <i class="ti ti-rotate me-1"></i> Restaurar
                    </button>
                @endcan
            </div>

            {{-- Fecha eliminación --}}
            <div class="mt-3">
                <small class="text-muted">
                    <i class="ti ti-trash me-1"></i>
                    Eliminada {{ $version->deleted_at->diffForHumans() }}
                </small>
            </div>

        </div>
    </div>
</div>
