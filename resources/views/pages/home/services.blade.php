<div class="rn-service-area rn-section-gap bg_color--5" id="service">
    <div class="container">
        
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title service-style--3 text-center mb--20 mb_sm--0 md_md--0">
                    <h2 class="title">Problems I Work On</h2>
                    <p>Here are some of the kinds of things I can do.</p>
                </div>
            </div>
        </div>

        <div class="row d-flex flex-row justify-content-center">

            @foreach (\Anomaly\Streams\Platform\Streams\Facades\Streams::make('abilities')->repository()->all() as $ability)
            <div class="col-lg-6 col-md-6 col-sm-6 col-12 mt--40">
                <div class="single-service service__style--2 bg-color-gray h-100">
                    <a href="service-details.html">
                        <div class="service">
                            <div class="icon">
                            <i data-feather="{{ $ability->icon }}"></i>
                            </div>
                            <div class="content">
                                <h3 class="title">{{ $ability->name }}</h3>
                                <p>{{ $ability->description }}</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            @endforeach

        </div>
        
    </div>
</div>
