<div wire:ignore>
    <div id="app"></div>

    <script>
        window.__INITIAL_DATA__ = {!! json_encode($initialData) !!};
        window.__CURRENT_USER__ = {!! json_encode($currentUser) !!};
        window.__MODE__ = "{{ (Auth::check() && Auth::user()->isVendor() && (request()->is('owner*') || request()->has('owner'))) ? 'owner' : 'student' }}";
    </script>
    <script src="{{ asset('js/coworklibrary.js') }}?v={{ time() }}"></script>
</div>
