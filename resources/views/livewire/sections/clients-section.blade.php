<div>
    @php
        // preview inmediato de imágenes
        if (!empty($data['_temp_images'])) {
            foreach ($data['_temp_images'] as $i => $img) {
                $data['items'][$i]['image'] = $img;
            }
        }

        $items = $data['items'] ?? [];
    @endphp

    <div class="challenges-area pt-110 pb-95" id="clientes">
        <div class="container">

            {{-- TÍTULO --}}
            <div class="row justify-content-center">
                <div class="col-xxl-8 col-xl-8 col-lg-10">
                    <div class="blog-section-title text-center z-index pb-40">
                        <h2 class="tp-section__title mb-10">
                            {{ $data['title'] ?? '' }}
                        </h2>
                    </div>
                </div>
            </div>

            {{-- CARRUSEL --}}
            <div class="row">
                <div class="col-lg-12">
                    <div class="brand-inner">
                        <div class="owl-carousel all-brand-carsouel">

                            @foreach ($items as $item)
                                <div class="brand-single-item">
                                    <div class="brand-single-item-cell">
                                        @if (!empty($item['image']))
                                            <img src="{{ asset($item['image']) }}" alt="cliente">
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
