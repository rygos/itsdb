@php
    $isPassword = $isPassword ?? false;
    $copyValue = $copyValue ?? '';
    $displayValue = $displayValue ?? $copyValue;
@endphp

<div class="itsdb-secret-field" @if($isPassword) data-secret-field @endif>
    <button
        type="button"
        class="itsdb-copy-button"
        data-copy-value="{{ $copyValue }}"
        data-copy-tooltip="Kopiert"
        title="{{ $isPassword ? 'Passwort kopieren' : 'Benutzernamen kopieren' }}"
    >
        <span
            class="itsdb-secret-text"
            @if($isPassword)
                data-secret-text
                data-hidden-text="-hidden-"
                data-visible="false"
            @endif
        >{{ $isPassword ? '-hidden-' : $displayValue }}</span>
    </button>
    @if($isPassword)
        <button
            type="button"
            class="itsdb-secret-toggle"
            data-secret-toggle
            aria-pressed="false"
            title="Passwort anzeigen"
        >
            <span class="itsdb-eye-icon" aria-hidden="true">&#128065;</span>
        </button>
    @endif
</div>
