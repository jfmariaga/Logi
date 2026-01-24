@php
    $background = '';

    if (!empty($data['_temp_background'])) {
        $background = $data['_temp_background'];
    } elseif (!empty($data['background'])) {
        $background = asset($data['background']);
    }

    $color = $data['text_color'] ?? '#ffffff';

    // Detectamos preview si viene override
    $isPreview = array_key_exists('_temp_background', $data) || request()->routeIs('livewire.*');
@endphp

<div class="tpslider__area fix">
    <div class="hero-wrapp" id="inicio">
        <div class="tpslider__item tpslider__height d-flex align-items-center p-relative fix"
            style="
                background-image: url('{{ $background }}');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                min-height: 420px;
             ">

            <div class="container position-relative">
                <div class="row">
                    <div class="col-xl-7">

                        <div class="tpslider__content"
                            style="
                                        visibility: visible !important;
                                        opacity: 1 !important;
                                    ">

                            <h1 style="color: {{ $color }} !important;">
                                {{ $data['title'] ?? '' }}
                            </h1>

                            <h3 style="color: {{ $color }} !important;">
                                {{ $data['subtitle'] ?? '' }}
                            </h3>

                        </div>


                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
