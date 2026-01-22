
<nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow navbar-static-top navbar-light navbar-brand-center" data-nav="brand-center">
    <div class="navbar-wrapper" >
        <div class="navbar-header" >
            <ul class="nav navbar-nav flex-row">
                {{-- open menú responsive --}}
                <li class="nav-item mobile-menu d-md-none mr-auto">
                    <a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#">
                        <i class="ft-menu font-large-1"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="navbar-brand" href="{{ route('dashboard') }}">
                        <img class="brand-logo" alt="logo" src="{{ asset('img-logisticarga/logo.png') }}">
                        {{-- <h3 class="brand-text"><b>Logisticarga JM S.A.S</b></h3> --}}
                    </a>
                </li>
                <li class="nav-item d-none">
                    <a class="nav-link open-navbar-container collapsed" data-toggle="collapse" data-target="#navbar-mobile" aria-expanded="false">
                        <i class="la la-ellipsis-v"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="navbar-container content" >
            <div class="navbar-collapse collapse" id="navbar-mobile">
            <ul class="nav navbar-nav mr-auto float-left"></ul>
            <ul class="nav navbar-nav float-right">
                <li class="dropdown dropdown-user nav-item">
                    <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                        <span class="mr-1 user-name text-bold-700">
                            <span>Bienvenido, </span>
                            {{ auth()->user()->name ?? '' }}
                        </span>
                        <span class="avatar avatar-online">
                            <img src="{{ asset('storage/avatars/'.( auth()->user()->picture ?? 'default.png' )) }}" onerror="this.onerror=null;this.src='storage/avatars/default.png';" alt="avatar">
                            <i></i>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" >
                        {{-- <a class="dropdown-item" href="user-profile.html">
                            <i class="ft-user"></i> Edit Profile
                        </a>
                        <a class="dropdown-item" href="app-kanban.html">
                            <i class="ft-clipboard"></i> Todo
                        </a>
                        <a class="dropdown-item" href="user-cards.html">
                            <i class="ft-check-square"></i> Task
                        </a> --}}
                        <div class="dropdown-divider" ></div>
                        <a  href="javascript:" onclick="$('#submitLogout').submit()" class="dropdown-item c_red">   
                            <i class="ft-power c_red"></i> Cerrar Sesión
                        </a>
                        {{-- <a href="javascript:" onclick="$('#submitLogout').submit()"><i class="la la-power-off c_red"></i>
                            <span class="menu-title c_red" data-i18n="nav.support_documentation.main">Cerrar sesión</span>
                        </a> --}}
                        <form id="submitLogout" action="{{ url('logout') }}" method="POST">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
            </div>
        </div>
    </div>
</nav>
