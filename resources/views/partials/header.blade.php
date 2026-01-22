@php
    $data = $data ?? [];
@endphp

<div class="cc-loadingpage"></div>
<div class="cc-loadingpage_transparent dnone"></div>

<div class="tpoffcanvas-area">
    <div class="tpoffcanvas">
        <div class="tpoffcanvas__close-btn">
            <button class="close-btn"><i class="fal fa-times"></i></button>
        </div>

        <div class="tpoffcanvas__logo">
            @if (!empty($data['logo']))
                <img src="{{ asset($data['logo']) }}" style="max-width:350px;" alt="logo">
            @endif
        </div>

        <div class="tp-main-menu-mobile"></div>

        <div class="offcanvas__btn mb-20">
            <a href="https://api.whatsapp.com/send?phone={{ preg_replace('/\D/', '', $data['phone'] ?? '') }}"
                target="_blank" class="tp-btn w-100">
                Contáctenos
            </a>
        </div>

        <div class="offcanvas__contact mb-40">
            <p class="offcanvas__contact-call">
                <a href="tel:{{ $data['phone'] ?? '' }}">{{ $data['phone'] ?? '' }}</a>
            </p>
            <p class="offcanvas__contact-mail">
                <a href="mailto:{{ $data['email'] ?? '' }}">{{ $data['email'] ?? '' }}</a>
            </p>
        </div>
    </div>
</div>

<div class="body-overlay"></div>

<header>
    {{-- DESKTOP --}}
    <div class="header-area d-none d-xl-block">
        <div class="header-area d-none d-xl-block">
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
                                                <img src="{{ $data['logo'] }}" style="max-width:350px;" alt="logo">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="tpheader__bottom-right d-flex justify-content-between align-items-center">
                                    <div class="tpheader__main-menu mr-130">
                                        <div class="tpheader__main-menu main-menu">
                                            <nav class="tp-main-menu-content">
                                                <ul>
                                                    <li><a href="/">INICIO</a></li>
                                                    <li><a href="#servicios">SERVICIOS</a></li>
                                                    <li><a href="#clientes">CLIENTES</a></li>
                                                    <li><a href="#cotacto">CONTACTO</a></li>
                                                    <li><a href="{{ route('marcacion') }}">MARCACIÓN</a></li>
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
    </div>

    {{-- MOBILE --}}
    <div class="mobile-header d-xl-none pt-20 pb-20">
        <div class="container d-flex justify-content-between align-items-center">
            @if (!empty($data['logo']))
                <img src="{{ asset($data['logo']) }}" style="max-width:350px;">
            @endif

            <a class="tp-menu-bar" href="javascript:void(0)">
                <i class="fa-solid fa-bars"></i>
            </a>
        </div>
    </div>
</header>
