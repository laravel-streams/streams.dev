{{-- Cycles system → light → dark. The initial class is set before paint by partials/head. --}}
<button
    type="button"
    {{ $attributes->merge(['class' => 'st-btn st-btn--ghost st-btn--sm st-btn--icon']) }}
    x-data="{
        mode: window.StreamsTheme ? window.StreamsTheme.get() : 'system',
        labels: { system: 'Theme: system', light: 'Theme: light', dark: 'Theme: dark' },
        cycle() {
            this.mode = { system: 'light', light: 'dark', dark: 'system' }[this.mode];
            window.StreamsTheme.set(this.mode);
        },
    }"
    @click="cycle()"
    :aria-label="labels[mode]"
    :title="labels[mode]"
    aria-label="Toggle color theme"
>
    <svg x-show="mode === 'system'" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="6.5" stroke="currentColor" stroke-width="1.5"/><path d="M10 3.5a6.5 6.5 0 010 13z" fill="currentColor"/></svg>
    <svg x-show="mode === 'light'" x-cloak viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="3.25" stroke="currentColor" stroke-width="1.5"/><path d="M10 2v1.5m0 13V18m8-8h-1.5m-13 0H2m13.66-5.66l-1.06 1.06M5.4 14.6l-1.06 1.06m11.32 0L14.6 14.6M5.4 5.4L4.34 4.34" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
    <svg x-show="mode === 'dark'" x-cloak viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M16.5 12.2A6.75 6.75 0 017.8 3.5a6.75 6.75 0 108.7 8.7z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
</button>
