<?php

use App\Models\Country;
use App\Models\Language;
use App\Models\User;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component {

    use Toast;
    use WithFileUploads;

    public User $user;

    #[Rule('required')]
    public string $name = '';

    #[Rule('required|email')]
    public string $email = '';

    #[Rule('sometimes')]
    public ?int $country_id = null;

    #[Rule('required')]
    public array $selectedLanguages = [];

    #[Rule('nullable|image|max:1024')]
    public $avatar;

    public function with(): array
    {
        return [
            'countries' => Country::all(),
            'languages' => Language::all(),
        ];
    }

    public function mount(): void
    {
        $this->fill($this->user);

        $this->selectedLanguages = $this->user->languages->pluck('id')->all();
    }

    public function save(): void
    {
        $data = $this->validate();

        $this->user->update($data);

        $this->user->languages()->sync($this->selectedLanguages);

        if ($this->avatar) {
            $url = $this->avatar->store('users', 'public');
            $this->user->update(['avatar' => "/storage/$url"]);
        }

        $this->success('User details updated.', redirectTo: route('users.index'));
    }

}; ?>

<div>
    <x-header title="Update {{ $user->name }}" separator />

    <x-form wire:submit="save">

        <div class="grid md:grid-cols-2 gap-4">
            <div class="space-y-4">
                <x-input label="Name" wire:model="name" />

                <x-input label="Email" wire:model="email" />

                <x-select label="Country" wire:model="country_id" :options="$countries" placeholder="---" />
            </div>
            <div>
                <x-file label="Avatar" wire:model="avatar" accept="image/png, image/jpeg" crop-after-change>
                    <img src="{{ $user->avatar ?? '/empty-user.jpg' }}" class="h-40 rounded-lg" />
                </x-file>

                <x-choices-offline label="Languages" wire:model="selectedLanguages" :options="$languages" searchable />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="{{ route('users.index') }}" />
            <x-button label="Save" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>
