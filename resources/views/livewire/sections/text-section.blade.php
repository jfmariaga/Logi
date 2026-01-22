    <div class="services-area tp-services__ptb pt-120 pb-90 p-relative fix">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="services-section-title z-index  pb-20">
                        <div class="tp-section__subtitle tp-section__subtitle-before mb-15 p-relative wow fadeInUp"
                            data-wow-duration=".9s" data-wow-delay=".3s">Conócenos
                        </div>
                        <h2 class="tp-section__title mb-10 wow fadeInUp" data-wow-duration=".9s" data-wow-delay=".4s">
                            {{ $data['title'] ?? '' }}
                        </h2>
                    </div>
                    <div class="tp-services__item p-relative fix mb-30  wow fadeInUp" data-wow-duration=".9s"
                        data-wow-delay=".5s">
                        <div class="tp-services__wrap z-index-2 d-flex align-items-start">
                            <div class="tp-services__content">
                                {!! $data['content'] ?? '' !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
