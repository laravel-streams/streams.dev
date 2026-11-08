@php
    $code = \App\Support\PlaygroundPhpExample::render($component);
    $filename = \App\Support\PlaygroundPhpExample::filename($component);
@endphp
<section class="playground-config" data-playground-example="{{ $component['id'] }}">
    <div>
        <h2 class="playground-config__label">PHP example</h2>
        <p class="playground-config__help">
            Read-only builder code generated from the {{ $component['label'] }} class and its catalog defaults. Copy it into your own application.
        </p>
    </div>

    <div class="st-code">
        <div class="st-code__chrome">
            <span class="st-code__lights" aria-hidden="true"><span></span><span></span><span></span></span>
            <span class="st-code__title">{{ $filename }}</span>
            <button type="button" data-playground-copy="{{ $component['id'] }}"
                onclick="navigator.clipboard.writeText(this.closest('.st-code').querySelector('code').textContent); this.textContent='Copied'">Copy</button>
        </div>
        <pre class="language-php"><code class="language-php" data-playground-php="{{ $component['id'] }}">{{ $code }}</code></pre>
    </div>
</section>
