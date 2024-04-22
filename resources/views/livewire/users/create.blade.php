<?php

use App\Models\Country;
use App\Models\Language;
use App\Models\User;
use Livewire\Attributes\Validate;
use Illuminate\Validation\Rules\Password;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;
    use WithFileUploads;

    // public User $user;

    #[Validate('required')]
    public string $name = '';

    #[Validate('required|email|unique:users,email')]
    public string $email = '';

    #[Validate('sometimes')]
    public ?int $country_id = null;

    #[Validate('nullable|image|max:2048')]
    public $photo;

    #[Validate('required')]
    public array $my_languages = [];

    #[Validate('sometimes')]
    public string $bio;

    #[Validate]
    public string $password;

    #[Validate('required|same:password')]
    public string $passwordConfirmation;

    function mount(): void
    {
        //
    }

    function with(): array
    {
        return [
            'countries' => Country::all(),
            'languages' => Language::all(),
        ];
    }

    public function rules()
    {
        return [
            'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
        ];
    }

    function save(): void
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'country_id' => $this->country_id,
            'bio' => $this->bio,
            'password' => Hash::make($this->password),
            'email_verified_at' => now(),
        ]);

        if ($this->photo) {
            $url = $this->photo->store('users', 'public');
            $user->update(['avatar' => "/storage/$url"]);
        }

        $user->languages()->sync($this->my_languages);

        $this->success('User created with success.', redirectTo: '/users');
    }
}; ?>

<div>
    <x-header title="Create User" separator />

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
                <x-input label="Email" type="email" wire:model.blur="email" />
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

        <div class="grid-cols-5 lg:grid">
            <div class="col-span-2">
                <x-header title="Password" subtitle="User password for login" size="text-2xl" />
            </div>
            <div class="grid col-span-3 gap-3">
                <x-input label="Password" wire:model.blur="password" icon-right="o-eye" type="password"
                    hint="Password must contain at least 8 characters long and contain one uppercase, one lowercase, one number and one special character" />
                <x-input label="Confirm Password" wire:model.blur="passwordConfirmation" icon-right="o-eye"
                    type="password" hint="Confirm password must be the same as password" />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/users" />
            <x-button label="Save" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot>
    </x-form>
</div>
