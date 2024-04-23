<?php

use App\Models\Country;
use App\Models\Language;
use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;
    use WithFileUploads;

    public User $user;

    #[Validate('required')]
    public string $name = '';

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('sometimes')]
    public ?int $country_id = null;

    #[Validate('nullable|image|max:1024')]
    public $photo;

    #[Validate('required')]
    public array $my_languages = [];

    #[Validate('sometimes')]
    public string $bio;

    function mount(): void
    {
        $this->fill($this->user);
        $this->my_languages = $this->user->languages->pluck('id')->all();
    }

    function with(): array
    {
        return [
            'countries' => Country::all(),
            'languages' => Language::all(),
        ];
    }

    function save(): void
    {
        $this->validate();

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'country_id' => $this->country_id,
            'bio' => $this->bio,
        ]);

        if ($this->photo) {
            $url = $this->photo->store('users', 'public');
            $this->user->update(['avatar' => "/storage/$url"]);
        }

        $this->user->languages()->sync($this->my_languages);

        $this->success('User updated with success.', redirectTo: '/users');
    }
}; ?>

<x-layouts.app>
    @volt
        <div>
            <x-header title="Update {{ $user->name }}" separator />

            <x-form wire:submit="save">
                <div class="grid-cols-5 lg:grid">
                    <div class="col-span-2">
                        <x-header title="Basic" subtitle="Basic info from user" size="text-2xl" />
                    </div>
                    <div class="grid col-span-3 gap-3">
                        <x-file label="Avatar" wire:model="photo" accept="image/png, image/jpeg" crop-after-change>
                            <img src="{{ $user->avatar ?? '/empty-user.jpg' }}" class="h-40 rounded-lg">

                        </x-file>
                        <x-input label="Name" wire:model="name" />
                        <x-input label="Email" type="email" wire:model="email" />
                        <x-select label="Country" wire:model="country_id" :options="$countries" placeholder="---" />
                    </div>
                </div>

                <hr class="my-5">

                <div class="grid-cols-5 lg:grid">
                    <div class="col-span-2">
                        <x-header title="Details" subtitle="More about the user" size="text-2xl" />
                    </div>
                    <div class="grid col-span-3 gap-3">
                        <x-choices-offline label="My Languages" wire:model="my_languages" :options="$languages" searchable />
                        <x-editor label="Bio" wire:model="bio" hint="The great biography" />
                    </div>
                </div>

                <x-slot:actions>
                    <x-button label="Cancel" link="/users" />
                    <x-button label="Save" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
                </x-slot>
            </x-form>
        </div>
    @endvolt
</x-layouts.app>
