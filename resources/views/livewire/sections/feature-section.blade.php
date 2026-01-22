<div>
    @php
        // preview inmediato de imagen
        if (!empty($data['_temp_image'])) {
            $data['image'] = $data['_temp_image'];
        }

        $items = $data['items'] ?? [];
    @endphp

    <div class="features-area tp-features__bg-color p-relative pt-10 fix">
        <div class="tp-features__class-fix">
            <div class="container mb-110">
                <div class="row align-items-xl-end">

                    {{-- TEXTO --}}
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <div class="tp-features">
                            <div class="tp-features__wrap mt-50">

                                <h2 class="tp-section__title-white mb-40">
                                    {{ $data['title'] ?? '' }}
                                </h2>

                                @if (!empty($data['paragraph']))
                                    <p class="tp-features__paragraph mb-30">
                                        {{ $data['paragraph'] }}
                                    </p>
                                @endif

                                <div class="tp-features__list">
                                    @foreach ($items as $item)
                                        <div class="tp-features__single d-flex align-items-start">
                                            <span class="mr-30">
                                                <img src="{{ asset('pagina_web/assets/img/shap-check.png') }}"
                                                    alt="check" style="width:30px">
                                            </span>
                                            <p>{{ $item['text'] ?? '' }}</p>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- IMAGEN --}}
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <div class="img_pq">
                            @if (!empty($data['image']))
                                <img src="{{ asset($data['image']) }}" alt="equipo">
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
