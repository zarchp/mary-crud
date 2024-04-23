<?php

use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new class extends Component {
    #[Validate(['required', 'email'])]
    public string $email;

    #[Validate('required')]
    public string $password;

    public $remember;

    public function login()
    {
        $this->validate();

        if (!auth()->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->flash('login_error', __('auth.failed'));
        }

        session()->flash('message', 'You are Login successful.');

        return $this->redirect('/', navigate: true);
    }
}; ?>

<x-layouts.app>
    @volt
        <div class="flex items-center justify-center lg:h-screen">
            <div class="w-full shadow lg:w-3/4 card lg:card-side bg-base-100">
                <figure class="w-full lg:w-1/2">
                    <img src="{{ asset('login.png') }}" alt="Album" />
                </figure>
                <div class="justify-center card-body">
                    <h2 class="justify-center mb-4 text-2xl card-title">{{ __('Login') }}</h2>

                    @if (session('login_error'))
                        <div class="alert alert-error">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 stroke-current shrink-0" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('login_error') }}</span>
                        </div>
                    @endif

                    <form wire:submit="login" class="flex flex-col gap-2">
                        <div class="form-control">
                            <x-input type="email" label="Email" name="email" wire:model="email" required="required"
                                autofocus="autofocus" />
                        </div>
                        <div class="form-control">
                            <x-input type="password" label="Password" name="password" wire:model="password"
                                required="required" autocomplete="current-password" icon-right="o-eye" />
                        </div>
                        <div class="mt-2">
                            <x-checkbox name="remember" :label="__('Remember Me')" wire:model="remember" value="1" />
                        </div>
                        <div class="justify-end card-actions">
                            <x-button type="submit" class="btn-primary" icon="o-paper-airplane">
                                {{ __('Login') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endvolt
</x-layouts.app>
