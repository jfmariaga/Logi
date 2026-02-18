<!DOCTYPE html>
<html>

<head>
    <title>Portal Proveedores - Logisticarga JM SAS</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('pagina-web/assets/css/bootstrap.css') }}">

    <!-- Tu CSS -->
    <link rel="stylesheet" href="{{ asset('css/style-p.css') }}?v=2">

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    @livewireStyles
</head>

<body style="background:#f2f4f7">

    <div class="container-fluid">
        {{ $slot }}
    </div>

    @livewireScripts

    <script src="{{ asset('pagina-web/assets/js/bootstrap.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>

</html>
