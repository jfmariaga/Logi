<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            border: 1px solid #000;
            padding: 5px;
        }

        .no-border {
            border: none;
        }

        .header-title {
            background: #e6e6e6;
            font-weight: bold;
            text-align: center;
            font-size: 13px;
        }

        .sub-header {
            background: #f2f2f2;
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .small {
            font-size: 10px;
        }

        .firma-box {
            height: 65px;
            text-align: center;
            vertical-align: middle;
        }
    </style>
</head>

<body>

    {{-- ===================== ENCABEZADO ===================== --}}
    <table>
        <tr>
            <td width="25%" class="center">
                <img src="{{ public_path('img-logisticarga/logo.png') }}" height="55">
            </td>

            <td width="75%" class="header-title">
                REGISTRO ENTREGA DE ELEMENTOS DE<br>
                PROTECCIÓN PERSONAL (EPP)
            </td>
        </tr>

        <tr>
            <td class="center">
                <strong>FORMATO NRO:</strong><br>
                LGT-FT-SST-032
            </td>

            <td>
                <strong>Fecha de Creación:</strong> marzo 2025<br>
                <strong>Versión:</strong> 001
            </td>
        </tr>
    </table>

    <br>

    {{-- ===================== DATOS DEL TRABAJADOR ===================== --}}
    <table>
        <tr>
            <td width="50%">
                <strong>Nombre completo:</strong><br>
                {{ $entrega->usuario->name }}
            </td>

            <td width="50%">
                <strong>Nro. Documento:</strong><br>
                {{ $entrega->usuario->document ?? '' }}
            </td>
        </tr>

        <tr>
            <td>
                <strong>Área:</strong><br>
                {{ $entrega->usuario->area->nombre ?? '' }}
            </td>

            <td>
                <strong>Cargo:</strong><br>
                {{ $entrega->usuario->cargo->nombre ?? '' }}
            </td>
        </tr>
    </table>

    <br>

    {{-- ===================== TABLA ITEMS ===================== --}}
    <table>
        <tr class="sub-header center">
            <th width="5%">Item</th>
            <th width="45%">EPP Entregado</th>
            <th width="10%">Cantidad</th>
            <th width="15%">Fecha</th>
            <th width="25%">Firma</th>
        </tr>

        @foreach ($entrega->items as $index => $item)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $item->producto->nombre }}</td>
                <td class="center">{{ $item->cantidad }}</td>
                <td class="center">{{ $entrega->created_at->format('d/m/Y') }}</td>
                <td class="firma-box">
                    @if ($firmaEmpleado)
                        <img src="{{ public_path('storage/' . $firmaEmpleado) }}" height="55">
                    @endif
                </td>
            </tr>
        @endforeach

        {{-- Rellenar filas hasta 18 como tu formato --}}
        @for ($i = count($entrega->items); $i < 10; $i++)
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @endfor

    </table>

    <br>

    {{-- ===================== TEXTO LEGAL ===================== --}}
    <table>
        <tr>
            <td class="small center">
                He recibido por parte de la empresa LOGISTICARGA JM S.A.S, los siguientes elementos
                de protección personal aquí relacionados, me comprometo a dar uso y mantenimiento
                adecuado de estos.
            </td>
        </tr>
    </table>

    <br>

    {{-- ===================== DATOS RESPONSABLE ===================== --}}
    <table>
        <tr class="sub-header center">
            <th colspan="2">
                Datos del Responsable de la Entrega de los EPP
            </th>
        </tr>

        <tr>
            <td width="50%">
                <strong>Nombre completo:</strong><br>
                {{ $entrega->responsable->name }}
            </td>

            <td width="50%">
                <strong>Nro. Documento:</strong><br>
                {{ $entrega->responsable->document ?? '' }}
            </td>
        </tr>

        <tr>
            <td>
                <strong>Cargo:</strong><br>
                {{ $entrega->responsable->cargo->nombre ?? '' }}
            </td>

            <td class="firma-box">
                <strong>Firma:</strong><br><br>
                @if ($firmaResponsable)
                    <img src="{{ public_path('storage/' . $firmaResponsable) }}" height="55">
                @endif
            </td>
        </tr>
    </table>

</body>

</html>
