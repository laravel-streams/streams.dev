<div class="inline-flex flex-col gap-2">
    <p>{{ $action->getLabel() }}</p>
    <ul>
        @foreach ($action->getActions() as $item)
        <li>{!! $item->render() !!}</li>
        @endforeach
    </ul>
</div>
