{{--
    Mac-window code panel.

    Single file:  <x-code-block title="streams/pages.json" lang="json" :code="$json" />
    Tabs:         <x-code-block :tabs="[['label' => 'pages.json', 'lang' => 'json', 'code' => $json], ...]" />
    The slot may be used instead of :code for pre-escaped markup.
--}}
@props([
    'title' => null,
    'lang' => 'none',
    'code' => null,
    'tabs' => [],
    'copy' => true,
])

@php
    $tabs = collect($tabs)->values();
    $hasTabs = $tabs->count() > 1;
@endphp

<figure {{ $attributes->class(['st-code']) }} x-data="{ tab: 0, copied: false, copy() { navigator.clipboard?.writeText(this.$refs['code' + this.tab].innerText).then(() => { this.copied = true; setTimeout(() => this.copied = false, 1600); }); } }">
    <div class="st-code__chrome">
        <div class="st-code__lights" aria-hidden="true"><span></span><span></span><span></span></div>

        @if ($hasTabs)
        <div class="st-code__tabs flex-1" role="tablist">
            @foreach ($tabs as $i => $item)
            <button type="button" role="tab" :aria-selected="(tab === {{ $i }}).toString()" aria-selected="{{ $i === 0 ? 'true' : 'false' }}" @click="tab = {{ $i }}">{{ $item['label'] }}</button>
            @endforeach
        </div>
        @else
        <figcaption class="st-code__title">{{ $title ?? ($tabs->first()['label'] ?? '') }}</figcaption>
        @endif

        @if ($copy)
        <button type="button" class="st-code__copy" @click="copy()" aria-label="Copy code">
            <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true"><rect x="5" y="5" width="8.5" height="8.5" rx="2" stroke="currentColor" stroke-width="1.4"/><path d="M11 5V4a1.5 1.5 0 00-1.5-1.5h-5A1.5 1.5 0 003 4v5a1.5 1.5 0 001.5 1.5H5" stroke="currentColor" stroke-width="1.4"/></svg>
            <span x-text="copied ? 'Copied' : 'Copy'">Copy</span>
        </button>
        @endif
    </div>

    @if ($tabs->isNotEmpty())
        @foreach ($tabs as $i => $item)
        <div @if ($i > 0) x-cloak @endif x-show="tab === {{ $i }}" role="tabpanel">
            <pre class="language-{{ $item['lang'] ?? 'none' }}"><code x-ref="code{{ $i }}" class="language-{{ $item['lang'] ?? 'none' }}">{{ $item['code'] }}</code></pre>
        </div>
        @endforeach
    @else
        <pre class="language-{{ $lang }}"><code x-ref="code0" class="language-{{ $lang }}">@if ($code !== null){{ $code }}@else{{ $slot }}@endif</code></pre>
    @endif
</figure>
