{{-- <!-- Modernizer JS -->
{!! Assets::script('public::assets/js/vendor/modernizr.min.js') !!}
<!-- jQuery JS -->
{!! Assets::script('public::assets/js/vendor/jquery.js') !!}
<!-- Bootstrap JS -->
{!! Assets::script('public::assets/js/vendor/bootstrap.min.js') !!}
{!! Assets::script('public::assets/js/vendor/stellar.js') !!}
{!! Assets::script('public::assets/js/vendor/particles.js') !!}
{!! Assets::script('public::assets/js/vendor/masonry.js') !!}
{!! Assets::script('public::assets/js/vendor/stickysidebar.js') !!}
{!! Assets::script('public::assets/js/vendor/contactform.js') !!}
{!! Assets::script('public::assets/js/plugins/plugins.min.js') !!}
<!-- Main JS -->
{!! Assets::script('public::assets/js/main.js') !!} --}}

<script src="{{ mix('/js/app.js') }}"></script>


<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-179956604-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-179956604-1');
</script>
