{!! Assets::tag('/vendor/streams/core/js/index.js') !!}
{!! Assets::tag('/vendor/streams/api/js/index.js') !!}
{!! Assets::tag('/vendor/streams/ui/js/index.js') !!}

{!! Assets::tag('/js/app.js') !!}

<script>

    window.streams.core.app.bootstrap({
        providers: [
            window.streams.core.StreamsServiceProvider,
            window.streams.api.ApiServiceProvider,
            window.streams.ui.UiServiceProvider,
            window.AppServiceProvider
        ]
    }).then(app => {
        return app.boot();
    }).then(app => {
        return app.start();
    });
</script>


<!-- Global site tag (gtag.js) - Google Analytics -->
@if (App::environment() == 'production')
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-179956604-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-179956604-1');
</script>
@endif
