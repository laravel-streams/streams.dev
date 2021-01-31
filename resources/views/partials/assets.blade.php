{!! Assets::tag('/vendor/streams/core/js/index.js') !!}
{!! Assets::tag('/vendor/streams/api/js/index.js') !!}

{!! Assets::tag('/js/app.js') !!}

<script>
    window.streams.core.app.bootstrap({
        providers: [
            window.streams.core.StreamsServiceProvider,
            window.streams.api.ApiServiceProvider,
            window.app.AppServiceProvider
        ]
    }).then(app => {
        return app.boot();
    }).then(app => {
        return app.start();
    });
</script>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-179956604-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-179956604-1');
</script>
