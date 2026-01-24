<div>
    <div class="tpcontact-area p-relative fix" id="contacto">
        <div class="tpcontact">
            <div class="container-fluid">
                <div class="row">

                    {{-- INFO --}}
                    <div class="col-xl-6 col-lg-6 col-md-6 col-12 p-0">
                        <div class="tpcontact__item-right pt-90 pb-90 text-center text-lg-start"
                            style="min-height:480px;">

                            @if (!empty($data['address']))
                                <h4 class="wow fadeInUp" data-wow-duration=".9s" data-wow-delay=".5s">
                                    <i class="fa-sharp fa-solid fa-map-marker"></i>
                                    {{ $data['address'] }}
                                </h4>
                            @endif

                            @if (!empty($data['email']))
                                <h4 class="wow fadeInUp" data-wow-duration=".9s" data-wow-delay=".5s">
                                    <i class="fa-sharp fa-solid fa-envelope-open"></i>
                                    {{ $data['email'] }}
                                </h4>
                            @endif

                            @if (!empty($data['phone']))
                                <h4 class="wow fadeInUp" data-wow-duration=".9s" data-wow-delay=".5s">
                                    <i class="fa-sharp fa-solid fa-phone"></i>
                                    {{ $data['phone'] }}
                                </h4>
                            @endif

                        </div>
                    </div>

                    {{-- MAPA --}}
                    <div class="col-xl-6 col-lg-6 col-md-6 col-12 p-0">
                        <div class="z-index-2 text-center text-lg-start">
                            @if (!empty($data['map_embed']))
                                {!! $data['map_embed'] !!}
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
