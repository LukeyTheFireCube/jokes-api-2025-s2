<x-admin-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Role Admin') }}
        </h2>
    </x-slot>

    <section class="py-12 mx-12 space-y-4">

        <nav class="flex flex-row justify-between">
            <x-link-primary-button class="bg-gray-500!" href="{{ route('admin.roles.index') }}">
                All Roles
            </x-link-primary-button>
        </nav>

        <form class="flex flex-col gap-4"
              method="post"
              action="{{ route('admin.roles.update', $role) }}">
            @csrf
            @method('patch')

            <h3>Edit Role Details</h3>

            <div class="flex flex-col gap-1">
                <x-input-label for="name" value="Name"/>
                <x-text-input id="name" class="block mt-1 w-full"
                              type="text" name="name"
                              value="{{ old('name') ?? $role->name }}"/>
                <x-input-error :messages="$errors->get('name')" class="mt-2"/>
            </div>

            <div class="flex flex-col gap-1">
                <x-input-label for="description" value="Description"/>
                <x-text-input id="description" class="block mt-1 w-full"
                              type="text" name="description"
                              value="{{ old('description') ?? $role->description }}"/>
                <x-input-error :messages="$errors->get('description')" class="mt-2"/>
            </div>

            <div class="flex flex-row justify-start">
                <x-primary-button type="submit" class="mr-6 px-12">
                    <i class="fa-solid fa-save pr-2 text-lg"></i>
                    Save
                </x-primary-button>

                <x-link-secondary-button href="{{ route('admin.roles.index') }}">
                    <i class="fa-solid fa-cancel pr-2 text-lg"></i>
                    Cancel
                </x-link-secondary-button>
            </div>

        </form>

    </section>

</x-admin-layout>
