<div>

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <div class="row">
                    <div id="content_notificaciones" class="col-md-3">
                         <div class="card" style="min-height: 35vh;">

                            @livewire('notificaciones.notificaciones')

                        </div>
                    </div>
                    <div id="content_calendario" class="col-md-9">
                        <div class="card" style="height: 100%;">

                            @livewire('dashboard.calendario')

                        </div>
                    </div>
                </div>
            </div>
        </div>
 
    </div>
    {{-- @can('ver dashboard')  
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-header row">
                    <div class="content-header-left col-md-6 col-12 mb-2 breadcrumb-new">
                        <h3 class="content-header-title mb-0 d-inline-block br_none">Dashboard</h3>
                    </div>
                </div>

                <div class="content-body">
                    <div class="card p-3">
                        <b>Filtrar Dashboard</b>
                        <div class="d-flex f_right">
                            <div class="col-md-3">
                                <label for="startDate">Fecha de Inicio:</label>
                                <input type="date" wire:model="startDate" id="startDate" class="form-control shadow-sm">
                            </div>

                            <div class="col-md-3">
                                <label for="endDate">Fecha de Fin:</label>
                                <input type="date" wire:model="endDate" id="endDate" class="form-control shadow-sm">
                            </div>

                            <div class="ml-2">
                                <button type="button" wire:click="actualizarDatos"
                                    class="btn btn-outline-dark ml-2">Filtrar</button>
                            </div>
                        </div>
                        <hr>

                        <div class="d-flex justify-content-around flex-wrap">
                            <!-- Tarjetas con información -->
                            <div class="card card-custom text-center p-2 box-shadow-2 bg-light border-danger rounded"
                                style="flex: 1 0 15%; max-width: 180px;">
                                <div class="icon mb-2">
                                    <i class="fa fa-money-bill-wave fa-2x text-danger"></i>
                                </div>
                                <h5>Gastos Totales</h5>
                                <h3 class="text-danger">$ {{ number_format($gastos_totales, 2) }}</h3>
                            </div>

                            <div class="card card-custom text-center p-2 box-shadow-2 bg-light border-info rounded"
                                style="flex: 1 0 15%; max-width: 180px;">
                                <div class="icon mb-2">
                                    <i class="fa fa-chart-line fa-2x text-success"></i>
                                </div>
                                <h5>Ganancia Real</h5>
                                <h3 class="text-success">$ {{ number_format($ganancia_real, 2) }}</h3>
                            </div>


                            <div class="card card-custom text-center p-2 box-shadow-2 bg-light border-info rounded"
                                style="flex: 1 0 15%; max-width: 180px;">
                                <div class="icon mb-2">
                                    <i class="fa fa-shopping-cart fa-2x text-info"></i>
                                </div>
                                <h5>Compras Totales</h5>
                                <h3 class="text-info">$ {{ number_format($compras_totales, 2) }}</h3>
                            </div>

                            <div class="card card-custom text-center p-2 box-shadow-2 bg-light border-warning rounded"
                                style="flex: 1 0 15%; max-width: 180px;">
                                <div class="icon mb-2">
                                    <i class="fa fa-store fa-2x text-warning"></i>
                                </div>
                                <h5>Ventas Totales</h5>
                                <h3 class="text-warning">$ {{ number_format($ventas_totales, 2) }}</h3>
                            </div>

                            <div class="card card-custom text-center p-2 box-shadow-2 bg-light border-primary rounded"
                                style="flex: 1 0 15%; max-width: 180px;">
                                <div class="icon mb-2">
                                    <i class="fa fa-boxes fa-2x text-primary"></i>
                                </div>
                                <h5>Valor del Inventario</h5>
                                <h3 class="text-primary">$ {{ number_format($valor_inventario, 2) }}</h3>
                            </div>
                        </div>

                        <!-- Gráficas -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card box-shadow-2">
                                    <div class="card-body">
                                        <h5>Producto Más Vendido</h5>
                                        <div id="grafica_producto_mas_vendido" style="height: 300px"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card box-shadow-2">
                                    <div class="card-body">
                                        <h5>Ranking de Productos por Ganancias</h5>
                                        <div id="grafica_ranking_ganancias" style="height: 300px"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="card box-shadow-2">
                                    <div class="card-body">
                                        <h5>Ventas Diarias</h5>
                                        <div id="grafica_ventas_diarias" style="height: 300px"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan --}}
    @push('js_extra')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    @endpush
</div>
