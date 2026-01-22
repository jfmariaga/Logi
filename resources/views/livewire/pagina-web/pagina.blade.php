<div>
    @include('partials.header', [
        'data' => $page->headerSettings(),
    ])

    @foreach ($page->sections->where('type', '!=', 'header')->sortBy('order') as $section)
        @livewire('sections.' . $section->type . '-section', ['section' => $section], key($section->id))
    @endforeach
</div>
