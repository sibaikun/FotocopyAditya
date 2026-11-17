@props(['href', 'icon', 'active' => false])

<a href="{{ $href }}" {{ $attributes->class([
    'flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition',
    'bg-blue-600 text-white shadow-lg shadow-blue-900/30' => $active,
    'text-blue-100/80 hover:bg-white/10 hover:text-white' => ! $active,
]) }}>
    <x-icon :name="$icon" size="w-5 h-5" />
    <span>{{ $slot }}</span>
</a>