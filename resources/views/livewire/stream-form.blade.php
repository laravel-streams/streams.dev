<div class="grid gap-8">
    {!! $form->render() !!}

    <div class="flex flex-wrap items-center gap-3">
        @foreach ($buttons as $button)
            <x-button wire:click="onSubmit" type="button">{{ $button['text'] ?? 'Submit' }}</x-button>
        @endforeach
        @if ($submitted)
            <x-pill dot>Submitted. This sandbox does not save entries.</x-pill>
        @endif
    </div>
</div>
