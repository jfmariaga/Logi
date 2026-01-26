

<div class="header-navbar navbar-expand-sm navbar navbar-horizontal navbar-fixed navbar-dark navbar-without-dd-arrow navbar-shadow menu__jota" role="navigation" data-menu="menu-wrapper">
    <div class="navbar-container main-menu-content" data-menu="menu-container">
        <ul class="nav navbar-nav" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="nav-item">
                <a class="nav-link block-page {{ Route::is('dashboard') ? 'active' : '' }}"
                    href="{{ route('dashboard') }}">
                    <i class="la la-home"></i> Dashboard
                </a>
            </li>
            <li class="dropdown nav-item" data-menu="dropdown">
                @can('ver Sección personal')
                    <a class="dropdown-toggle nav-link
                    {{ Route::is('usuarios') ? 'active' : '' }}    
                    {{ Route::is('roles') ? 'active' : '' }}    
                    {{ Route::is('programacion') ? 'active' : '' }}    
                    {{ Route::is('asistencia') ? 'active' : '' }}    
                    " href="#" data-toggle="dropdown">
                        <i class="la la-users"></i><span>Personal</span>
                    </a>
                @endcan
                <ul class="dropdown-menu">
                    @can('ver programación')
                        <li>
                            <a class="dropdown-item block-page" href="{{ route('programacion') }}">
                                <span>Programación</span>
                            </a>
                        </li>
                    @endcan
                    @can('ver marcaciones')
                        <li>
                            <a class="dropdown-item block-page" href="{{ route('asistencia') }}">
                                <span>Marcaciones</span>
                            </a>
                        </li>
                    @endcan
                    @can('ver capacitaciones')
                        <li>
                            <a class="dropdown-item block-page" href="{{ route('cursos') }}">
                                <span>Capacitaciones</span>
                            </a>
                        </li>
                    @endcan
                    @can('ver mis cursos')
                        <li>
                            <a class="dropdown-item block-page" href="{{ route('mis-cursos') }}">
                                <span>Mis cursos</span>
                            </a>
                        </li>
                    @endcan
                    @can('ver información de interes')
                         <li>
                            <a class="dropdown-item block-page" href="{{ route('informacion_de_interes') }}">
                                <span>Información de interés</span>
                            </a>
                         </li>
                    @endcan
                    @can('ver usuarios')
                        <li>
                            <a class="dropdown-item block-page" href="{{ route('usuarios') }}">
                                <span>Lista de usuarios</span>
                            </a>
                        </li>
                    @endcan     
                    @can('ver roles')
                        <li>
                            <a class="dropdown-item block-page" href="{{ route('roles') }}">
                                <span data-i18n="Horizontal">Roles y permisos</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
            <li class="dropdown nav-item" data-menu="dropdown">
                @can('ver Sección Empresas')
                     <a class="dropdown-toggle nav-link 
                    {{ Route::is('gestion-documental') ? 'active' : '' }}
                    {{ Route::is('sedes') ? 'active' : '' }}
                        " href="#" data-toggle="dropdown">
                    <i class="la la-bar-chart"></i><span>Empresa</span>
                    </a>
                @endcan             
                <ul class="dropdown-menu">
                    @can('ver sedes')
                        <li>
                            <a class="dropdown-item block-page" href="{{ route('sedes') }}">
                                <span>Sedes de Trabajo</span>
                            </a>
                        </li>
                    @endcan
                    @can('ver gestión documental')
                       <li>
                            <a class="dropdown-item block-page" href="{{ route('gestion-documental') }}">
                                <span data-i18n="Horizontal">Gestión documental</span>
                            </a>
                        </li> 
                    @endcan
                </ul>
            </li>
            @can('ver Sección página web')
                <li class="nav-item">
                    <a class="nav-link block-page {{ request()->routeIs('admin.pages.edit') ? 'active' : '' }}"
                        href="{{ route('admin.pages.edit', 'home') }}">
                        <i class="la la-list-alt"></i> Página web
                    </a>
                </li>  
            @endcan
            <li class="nav-item show_responsive">
                <a class="nav-link c_red" href="javascript:" onclick="$('#submitLogout').submit()">
                    <i class="la la-power-off c_red"></i> Cerrar sesión
                </a>
            </li>
        </ul>
    </div>
</div>

@php
    /*
@endphp

<div id="sticky-wrapper" class="sticky-wrapper">
    <div class="header-navbar navbar-expand-sm navbar navbar-horizontal navbar-fixed navbar-dark navbar-without-dd-arrow navbar-shadow navbar-brand-center" role="navigation" data-menu="menu-wrapper" data-nav="brand-center">
        <div class="navbar-container main-menu-content" data-menu="menu-container" >
            <ul class="nav navbar-nav" id="main-menu-navigation" data-menu="menu-navigation">
                <li class="nav-item">
                    <a class="nav-link block-page {{ Route::is('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <i class="la la-home"></i> Dashboard
                    </a>
                </li>
                <li class="dropdown nav-item" data-menu="dropdown">
                    <a class="dropdown-toggle nav-link
                    {{ Route::is('usuarios') ? 'active' : '' }}    
                    {{ Route::is('roles') ? 'active' : '' }}    
                    {{ Route::is('programacion') ? 'active' : '' }}    
                    {{ Route::is('asistencia') ? 'active' : '' }}    
                    " href="#" data-toggle="dropdown">
                        <i class="la la-users"></i><span>Personal</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item block-page" href="{{ route('programacion') }}">
                                <span>Programación</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item block-page" href="{{ route('asistencia') }}">
                                <span>Marcaciones</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item block-page" href="{{ route('cursos') }}">
                                <span>Capacitaciones</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item block-page" href="{{ route('mis-cursos') }}">
                                <span>Mis cursos</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item block-page" href="#">
                                <span>Información de interés</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item block-page" href="{{ route('usuarios') }}">
                                <span>Lista de usuarios</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item block-page" href="{{ route('roles') }}">
                                <span data-i18n="Horizontal">Roles y permisos</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown nav-item" data-menu="dropdown">
                    <a class="dropdown-toggle nav-link 
                        {{ Route::is('gestion-documental') ? 'active' : '' }}
                        {{ Route::is('sedes') ? 'active' : '' }}
                            " href="#" data-toggle="dropdown">
                        <i class="la la-bar-chart"></i><span>Empresa</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item block-page" href="{{ route('sedes') }}">
                                <span>Sedes de Trabajo</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item block-page" href="{{ route('gestion-documental') }}">
                                <span data-i18n="Horizontal">Gestión documental</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item block-page" href="#">
                                <span data-i18n="Horizontal">Novedades</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link block-page {{ request()->routeIs('admin.pages.edit') ? 'active' : '' }}"
                        href="{{ route('admin.pages.edit', 'home') }}">
                        <i class="la la-list-alt"></i> Página web
                    </a>
                </li>
                <li class="nav-item show_responsive">
                    <a class="nav-link c_red" href="javascript:" onclick="$('#submitLogout').submit()">
                        <i class="la la-power-off c_red"></i> Cerrar sesión
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
@php
    */
@endphp

{{-- ejemplo menu anidado --}}
{{-- <li class="dropdown nav-item" data-menu="dropdown">
                    <a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown">
                        <i class="la la-television"></i><span data-i18n="Templates">Templates</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#" data-toggle="dropdown">
                                <i class="la la-arrows-h"></i><span data-i18n="Horizontal">Horizontal</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li data-menu="">
                                    <a class="dropdown-item" href="../horizontal-menu-template" data-toggle="">
                                        <span data-i18n="Classic">Classic</span>
                                    </a>
                                </li>
                                <li data-menu="">
                                    <a class="dropdown-item" href="../horizontal-menu-template-nav" data-toggle="">
                                        <span data-i18n="Full Width">Full Width</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li> --}}
