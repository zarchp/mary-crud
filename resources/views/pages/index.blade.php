<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<x-layouts.app>
    @volt
        <div>
            <p>This is homepage.</p>

            @if (auth()->check())
                <p class="mt-4">You are logged in.</p>
            @endif
        </div>
    @endvolt
</x-layouts.app>
