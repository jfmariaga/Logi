<div>
    @php
        $items = $data['items'] ?? [];
    @endphp

    <div class="services-area tp-services__ptb pt-120 pb-90 p-relative fix background_grey" id="servicios">
        <div class="container">
            <div class="row">
                <div class="col-12">

                    {{-- TÍTULOS --}}
                    <div class="services-section-title z-index pb-20">
                        <div class="tp-section__subtitle tp-section__subtitle-before mb-15">
                            {{ $data['subtitle'] ?? '' }}
                        </div>

                        <h2 class="tp-section__title mb-10">
                            {{ $data['title'] ?? '' }}
                        </h2>
                    </div>

                    {{-- SERVICIOS --}}
                    <div class="row">
                        @foreach ($items as $item)
                            <div class="col-md-4 mt-2">
                                <a href="{{ $item['link'] ?? '#' }}">
                                    <div class="card_services">
                                        <div class="content_img_services">
                                            @if (!empty($item['image']))
                                                <img src="{{ asset($item['image']) }}" alt="{{ $item['title'] ?? '' }}">
                                            @endif
                                        </div>

                                        <div class="content_card_services w-100">
                                            {{-- ESTE ES EL TEXTO REAL --}}
                                            <h2 class="fs18">
                                                {{ $item['description'] ?? '' }}
                                            </h2>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
