@props([
    'name' => '',
    'label' => '',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'help' => '',
    'error' => null,
    'options' => [], // for select
    'multiple' => false,
])

@php
    $error = $error ?? ($name ? $errors->first($name) : null);
    $isSelect = $type === 'select';
    $isTextarea = $type === 'textarea';
    $inputType = $isSelect || $isTextarea ? 'text' : $type;
@endphp

<div class="mb-3">
    @if($label)
        <label for="{{ $name }}" class="form-label @if($required) required @endif">
            {{ $label }}
        </label>
    @endif

    @if($isSelect)
        <select name="{{ $multiple ? $name . '[]' : $name }}"
                id="{{ $name }}"
                class="form-select @if($error) is-invalid @endif"
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($multiple) multiple @endif>
            <option value="">-- Select {{ $label ?: 'Option' }} --</option>
            @foreach($options as $key => $option)
                <option value="{{ $key }}" {{ (is_array($value) ? in_array($key, $value) : old($name, $value) == $key) ? 'selected' : '' }}>
                    {{ $option }}
                </option>
            @endforeach
        </select>

    @elseif($isTextarea)
        <textarea name="{{ $name }}"
                  id="{{ $name }}"
                  class="form-control @if($error) is-invalid @endif"
                  placeholder="{{ $placeholder }}"
                  @if($required) required @endif
                  @if($disabled) disabled @endif
                  rows="3">{{ old($name, $value) }}</textarea>

    @else
        <input type="{{ $inputType }}"
               name="{{ $name }}"
               id="{{ $name }}"
               class="form-control @if($error) is-invalid @endif"
               placeholder="{{ $placeholder }}"
               value="{{ old($name, $value) }}"
               @if($required) required @endif
               @if($disabled) disabled @endif>
    @endif

    @if($error)
        <div class="invalid-feedback">{{ $error }}</div>
    @endif

    @if($help)
        <small class="form-hint">{{ $help }}</small>
    @endif
</div>
