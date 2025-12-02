<x-admin-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('User Admin') }}
        </h2>
    </x-slot>

    <section class="py-12 mx-12 space-y-4">

        <nav class="flex flex-row justify-between">
            <x-link-primary-button href="{{ route('admin.users.index') }}">
                All Users
            </x-link-primary-button>
        </nav>

        <form class="flex flex-col gap-4"
              method="post"
              action="{{ route('admin.users.update', $user) }}"
        >
            @csrf
            @method('patch')

            <h3>Edit User Details</h3>

            <div class="flex flex-col gap-1">
                <x-input-label for="given_name" :value="__('Given Name')" />
                <x-text-input id="given_name" class="block mt-1 w-full"
                              type="text" name="given_name"
                              value="{{ old('given_name') ?? $user->given_name }}" />
                <x-input-error :messages="$errors->get('given_name')" class="mt-2" />
            </div>

            <div class="flex flex-col gap-1">
                <x-input-label for="family_name" :value="__('Family Name')" />
                <x-text-input id="family_name" class="block mt-1 w-full"
                              type="text" name="family_name"
                              value="{{ old('family_name') ?? $user->family_name }}" />
                <x-input-error :messages="$errors->get('family_name')" class="mt-2" />
            </div>

            <div class="flex flex-col gap-1">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full"
                              type="email" name="email"
                              value="{{ old('email') ?? $user->email }}" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- Password optional --}}
            <div class="flex flex-col gap-1">
                <x-input-label for="password" :value="__('Password (optional)')" />
                <x-text-input id="password" class="block mt-1 w-full"
                              type="password" name="password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            {{-- Role --}}
            <div class="flex flex-col gap-1">
                <x-input-label for="role" :value="__('Role')" />
                <select name="role" id="role"
                        class="block mt-1 w-full border-gray-300 rounded">
                    <option value="">None</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}"
                            {{ (old('role') ?? $user->roles->first()->name ?? '') === $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            <div class="flex flex-row justify-start">
                <x-primary-button type="submit" class="mr-6 px-12">
                    <i class="fa-solid fa-save pr-2 text-lg"></i>
                    Save
                </x-primary-button>

                <x-link-secondary-button href="{{ route('admin.users.index') }}">
                    <i class="fa-solid fa-cancel pr-2 text-lg"></i>
                    Cancel
                </x-link-secondary-button>
            </div>

        </form>

    </section>

</x-admin-layout>
