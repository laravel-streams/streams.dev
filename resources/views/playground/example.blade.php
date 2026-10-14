<article class="playground-example" data-builder="{{ $type }}" @if ($stream) data-stream="{{ $stream }}" @endif>
    <h2>{{ $label }}</h2>

    @if ($description !== '')
        <p>{{ $description }}</p>
    @endif

    @if ($mode === 'table')
        <table>
            <thead>
                <tr>
                    @foreach ($columns as $column)
                        <th scope="col">{{ $column['label'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr data-entry="{{ $row['id'] }}">
                        @foreach ($columns as $column)
                            <td>{{ $row['cells'][$column['handle']] }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif ($mode === 'form')
        <div role="form">
            @foreach ($fields as $field)
                @if ($field['kind'] === 'radio')
                    <fieldset>
                        <legend>{{ $field['label'] }}</legend>
                        @foreach ($field['options'] as $option)
                            <label>
                                <input type="radio" name="{{ $field['handle'] }}" value="{{ $option['value'] }}" @checked((string) $field['value'] === (string) $option['value']) @if ($field['required']) required @endif>
                                {{ $option['label'] }}
                            </label>
                        @endforeach
                    </fieldset>
                @elseif ($field['kind'] === 'select')
                    <label for="playground-{{ $type }}-{{ $field['handle'] }}">{{ $field['label'] }}</label>
                    <select id="playground-{{ $type }}-{{ $field['handle'] }}" name="{{ $field['handle'] }}{{ $field['multiple'] ? '[]' : '' }}" @if ($field['multiple']) multiple @endif @if ($field['required']) required @endif>
                        @unless ($field['multiple'])
                            <option value="">Choose</option>
                        @endunless
                        @foreach ($field['options'] as $option)
                            <option value="{{ $option['value'] }}" @selected($field['multiple'] ? in_array((string) $option['value'], $field['value'], true) : (string) $field['value'] === (string) $option['value'])>{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                @elseif ($field['kind'] === 'textarea')
                    <label for="playground-{{ $type }}-{{ $field['handle'] }}">{{ $field['label'] }}</label>
                    <textarea id="playground-{{ $type }}-{{ $field['handle'] }}" name="{{ $field['handle'] }}" @if ($field['required']) required @endif>{{ $field['value'] }}</textarea>
                @elseif ($field['kind'] === 'checkbox')
                    <label for="playground-{{ $type }}-{{ $field['handle'] }}">
                        <input id="playground-{{ $type }}-{{ $field['handle'] }}" name="{{ $field['handle'] }}" type="checkbox" value="1" @checked($field['checked']) @if ($field['switch']) role="switch" @endif>
                        {{ $field['label'] }}
                    </label>
                @elseif ($field['kind'] === 'file')
                    <label for="playground-{{ $type }}-{{ $field['handle'] }}">{{ $field['label'] }}</label>
                    <input id="playground-{{ $type }}-{{ $field['handle'] }}" name="{{ $field['handle'] }}" type="file" @if ($field['accept']) accept="{{ $field['accept'] }}" @endif>
                @else
                    <label for="playground-{{ $type }}-{{ $field['handle'] }}">{{ $field['label'] }}</label>
                    <input
                        id="playground-{{ $type }}-{{ $field['handle'] }}"
                        name="{{ $field['handle'] }}"
                        type="{{ $field['html_type'] }}"
                        @if ($field['html_type'] === 'password') value="" autocomplete="off" @else value="{{ $field['value'] }}" @endif
                        @if ($field['required']) required @endif
                        @if ($field['min'] !== null) min="{{ $field['min'] }}" @endif
                        @if ($field['max'] !== null) max="{{ $field['max'] }}" @endif
                        @if ($field['step'] !== null) step="{{ $field['step'] }}" @endif
                    >
                @endif
            @endforeach
        </div>
    @elseif ($mode === 'action')
        <button type="button" data-action="{{ $action['name'] }}">{{ $action['label'] }}</button>
        @if ($action['confirm'])
            <p>Asks for confirmation before running. This preview does not change {{ $action['entry'] }}.</p>
        @endif
    @elseif ($mode === 'items')
        <ul>
            @foreach ($items as $item)
                <li>
                    {{ $item['label'] }}
                    @if ($item['meta'] !== '')
                        <span>{{ $item['meta'] }}</span>
                    @endif
                </li>
            @endforeach
        </ul>
    @else
        <dl>
            @foreach ($details as $detail)
                <dt>{{ $detail['label'] }}</dt>
                <dd>{{ $detail['value'] }}</dd>
            @endforeach
        </dl>
    @endif
</article>
