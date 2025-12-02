<x-admin-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('User Admin') }}
        </h2>
    </x-slot>

    <section class="py-12 mx-12 space-y-4">

        <form class="flex flex-col gap-4"
              method="post"
              action="{{ route('admin.users.delete', $user) }}"
        >
            @csrf
            @method('delete')

            <h3 class="text-xl my-6">User Details</h3>

            <div class="grid grid-cols-12 gap-8">
                <p class="col-span-2 bg-gray-200 p-2">
                    {{ __('Name') }}
                </p>
                <p class="col-span-9 p-2">
                    {{ $user->name }}
                </p>
            </div>

            <div class="grid grid-cols-12 gap-8">
                <p class="col-span-2 bg-gray-200 p-2">
                    {{ __('Email') }}
                </p>
                <p class="col-span-9 p-2">
                    {{ $user->email }}
                </p>
            </div>

            <div class="grid grid-cols-12 gap-8">
                <p class="col-span-2 bg-gray-200 p-2">
                    {{ __('Role') }}
                </p>
                <p class="col-span-9 p-2">
                    {{ $user->roles->pluck('name')->join(', ') ?: '-' }}
                </p>
            </div>

            <div class="grid grid-cols-12 gap-8">
                <p class="col-span-2 bg-gray-200 p-2">
                    {{ __('Status') }}
                </p>
                <p class="col-span-9 p-2 capitalize">
                    {{ $user->status ?? 'active' }}
                </p>
            </div>

            <div class="flex flex-row justify-start">
                <x-link-primary-button href="{{ route('admin.users.edit', $user) }}"
                                       class="mr-6 px-12">
                    <i class="fa-solid fa-edit pr-2 text-lg"></i>
                    {{ __('Edit') }}
                </x-link-primary-button>

                <x-link-secondary-button href="{{ route('admin.users.index') }}"
                                         class="mr-6">
                    {{ __('All Users') }}
                </x-link-secondary-button>

                <x-secondary-button type="submit"
                                    class="mr-6">
                    <i class="fa-solid fa-delete-left pr-2 text-lg"></i>
                    {{ __('Delete') }}
                </x-secondary-button>
            </div>

        </form>

    </section>

</x-admin-layout>
