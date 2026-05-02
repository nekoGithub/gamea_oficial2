@extends('layouts.vertical', ['title' => 'Perfil'])

@section('css')
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Usuarios', 'title' => 'Perfil'])

    <div class="row">
        <div class="col-12">
            <article class="card overflow-hidden mb-0">
                <div class="position-relative card-side-img overflow-hidden"
                    style="min-height: 300px; background-image: url(/img/gamea.jpg);">
                    <div
                        class="p-4 card-img-overlay rounded-start-0 auth-overlay d-flex align-items-center flex-column justify-content-center">
                        <h3 class="text-white mb-1 fst-italic">"Fomentando la innovación a través del diseño limpio"</h3>
                        <p class="text-white mb-4">– {{ Auth::user()->name }}</p>
                    </div>
                </div>
            </article>
        </div>
    </div>

    <div class="px-3 mt-n4">
        <div class="row">
            <div class="col-xl-4">
                <div class="card card-top-sticky">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div class="me-3 position-relative">
                                <img alt="avatar" class="rounded-circle" height="72"
                                    src="{{ Auth::user()->profile_photo_path
                                        ? asset('storage/avatars/' . Auth::user()->profile_photo_path)
                                        : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=random' }}"
                                    width="72" />
                            </div>
                            <div>
                                <h5 class="mb-0 d-flex align-items-center">
                                    <a class="link-reset" href="#!">{{ Auth::user()->name }}</a>
                                </h5>
                                <p class="text-muted mb-2">{{ Auth::user()->email }}</p>
                                <span class="badge text-bg-light badge-label">Mienbro</span>
                            </div>
                            {{-- <div class="ms-auto">
                                <div class="dropdown">
                                    <a class="btn btn-icon btn-ghost-light text-muted" data-bs-toggle="dropdown"
                                        href="#">
                                        <i class="ti ti-dots-vertical fs-xl"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#settings" data-bs-toggle="tab">Edit Profile</a>
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger">Logout</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div> --}}
                        </div>

                        <div class="">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div
                                    class="avatar-sm text-bg-light bg-opacity-75 d-flex align-items-center justify-content-center rounded-circle">
                                    <i class="ti ti-mail fs-xl"></i>
                                </div>
                                <p class="mb-0 fs-sm">Correo Electronico <a class="text-primary fw-semibold"
                                        href="mailto:{{ Auth::user()->email }}">{{ Auth::user()->email }}</a>
                                </p>
                            </div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div
                                    class="avatar-sm text-bg-light bg-opacity-75 d-flex align-items-center justify-content-center rounded-circle">
                                    <i class="ti ti-calendar fs-xl"></i>
                                </div>
                                <p class="mb-0 fs-sm">Mienbro desde <span
                                        class="text-dark fw-semibold">{{ Auth::user()->created_at->format('M Y') }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header card-tabs d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="card-title">Mi cuenta</h4>
                        </div>
                        <ul class="nav nav-tabs card-header-tabs nav-bordered">
                            <li class="nav-item">
                                <a aria-expanded="true" class="nav-link active" data-bs-toggle="tab" href="#profile-info">
                                    <i class="ti ti-user-circle d-md-none d-block"></i>
                                    <span class="d-none d-md-block fw-bold">Informacion de perfil</span>
                                </a>
                            </li>
                            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                                <li class="nav-item">
                                    <a aria-expanded="false" class="nav-link" data-bs-toggle="tab" href="#password">
                                        <i class="ti ti-lock d-md-none d-block"></i>
                                        <span class="d-none d-md-block fw-bold">Contraseña</span>
                                    </a>
                                </li>
                            @endif

                            <li class="nav-item">
                                <a aria-expanded="false" class="nav-link" data-bs-toggle="tab" href="#sessions">
                                    <i class="ti ti-devices d-md-none d-block"></i>
                                    <span class="d-none d-md-block fw-bold">Sesiones</span>
                                </a>
                            </li>

                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            {{-- Profile Information --}}
                            <div class="tab-pane show active" id="profile-info">
                                @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                                    @livewire('profile.update-profile-information-form')
                                @endif
                            </div>

                            {{-- Update Password --}}
                            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                                <div class="tab-pane" id="password">
                                    @livewire('profile.update-password-form')
                                </div>
                            @endif



                            {{-- Browser Sessions --}}
                            <div class="tab-pane" id="sessions">
                                @livewire('profile.logout-other-browser-sessions-form')
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
