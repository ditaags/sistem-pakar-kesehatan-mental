@props([
    'name',
    'options' => [],
    'model' => null,
    'selected' => null,
    'idPrefix' => null,
    'cols' => 'grid-cols-3 sm:grid-cols-6',
    'shape' => 'rounded-full',
])

@php
    $prefix = $idPrefix ?? \Illuminate\Support\Str::slug(str_replace(['[', ']', '\'', '"'], '_', $name));
@endphp

<div class="grid {{ $cols }} gap-2">
    @foreach ($options as $opt)
        @php
            $val = (string) ($opt['val'] ?? $opt['value']);
            $label = $opt['label'];
            $sublabel = $opt['num'] ?? $opt['sublabel'] ?? null;
            $radioId = $prefix . '_' . str_replace(['.', '-', ' '], '_', $val);
            $isChecked = ((string) $selected) === $val;
            $activeClass = $opt['activeClass'] ?? 'peer-checked:bg-[#1A3831] peer-checked:text-white peer-checked:border-[#1A3831] font-semibold';
        @endphp
        <div class="relative">
            <input type="radio"
                   id="{{ $radioId }}"
                   name="{{ $name }}"
                   value="{{ $val }}"
                   @if($model) x-model="{{ $model }}" @endif
                   @if($isChecked) checked @endif
                   class="peer sr-only">
            <label for="{{ $radioId }}"
                   class="flex flex-col items-center justify-center min-h-[44px] py-2 px-1 text-center bg-white border border-[#DCE5E0] {{ $shape }} cursor-pointer transition-all duration-120 select-none hover:border-stone-400 hover:bg-stone-50/70 active:scale-[0.97] peer-focus-visible:ring-2 peer-focus-visible:ring-[#1A3831] peer-focus-visible:ring-offset-2 {{ $activeClass }} shadow-2xs">
                <span class="text-xs font-semibold leading-tight block">{{ $label }}</span>
                @if ($sublabel !== null)
                    <span class="text-[10px] opacity-80 font-mono leading-tight block mt-0.5">{{ $sublabel }}</span>
                @endif
            </label>
        </div>
    @endforeach
</div>
