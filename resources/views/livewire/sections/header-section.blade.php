<div>
    @if ($isPreview)
        {{-- =========================
                HEADER REAL (PRODUCCIÓN)
            ========================== --}}
        <header>
            <div class="header-area">
                <div class="tpheader__top theme-background pt-5 pb-5 pl-160 pr-160">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="tpheader__info">
                                    <ul>
                                        <li>
                                            <a href="tel:{{ $data['phone'] ?? '' }}">
                                                <i class="fa-sharp fa-solid fa-phone"></i>
                                                {{ $data['phone'] ?? '' }}
                                            </a>
                                        </li>
                                        <li>
                                            <a href="mailto:{{ $data['email'] ?? '' }}">
                                                <i class="fa-sharp fa-solid fa-envelope-open"></i>
                                                {{ $data['email'] ?? '' }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="tpheader d-flex justify-content-end">
                                    <div class="tpheader__top-menu mr-60">
                                        <ul>
                                            <li>
                                                <a href="#">
                                                    {{ $data['address'] ?? '' }}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="tpheader">
                    <div id="header-sticky" class="tpheader__bottom">
                        <div class="tpheader__bottom-wrap p-relative pr-160">
                            <div class="custom-container">
                                <div class="tpheader__bottom-wrapp d-flex justify-content-between align-items-center"
                                    style="height:75px;">
                                    <div class="tpheader__main-logo">
                                        <div class="tpheader__logo p-relative z-index-2">
                                            <div class="">
                                                @if (!empty($data['logo']))
                                                    <img src="{{ $data['logo'] }}" style="max-width:350px;"
                                                        alt="logo">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="tpheader__bottom-right d-flex justify-content-between align-items-center">
                                        <div class="tpheader__main-menu mr-130">
                                            <div class="tpheader__main-menu main-menu">
                                                <nav class="tp-main-menu-content">
                                                    <ul>
                                                        <li><a href="/">INICIO</a></li>
                                                        <li><a href="#servicios">SERVICIOS</a></li>
                                                        <li><a href="#clientes">CLIENTES</a></li>
                                                        <li><a href="#cotacto">CONTACTO</a></li>
                                                        <li><a href="{{ route('marcacion') }}">Marcación</a></li>
                                                        <li><a href="{{ route('login') }}">ADMIN</a></li>
                                                    </ul>
                                                </nav>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
    @endif
</div>
