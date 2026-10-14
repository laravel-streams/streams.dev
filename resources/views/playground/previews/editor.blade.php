<x-dynamic-component :component="$field->getFieldWrapperView()" :field="$field" class="flex">
    <textarea
        id="{{ $field->getId() }}"
        rows="6"
        class="block w-full rounded-md px-3 py-2"
    >{{ $field->getState() }}</textarea>
</x-dynamic-component>
