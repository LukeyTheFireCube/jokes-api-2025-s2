<x-admin-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Role Admin') }}
        </h2>
    </x-slot>

    <section class="py-12 mx-12 space-y-4">

        <form class="flex flex-col gap-4">

            <h3 class="text-xl my-6">Role Details</h3>

            <div class="grid grid-cols-12 gap-8">
                <p class="col-span-2 bg-gray-200 p-2">
                    {{ __('Name') }}
                </p>
                <p class="col-span-9 p-2">
                    {{ $role->name }}
                </p>
            </div>

            <div class="grid grid-cols-12 gap-8">
                <p class="col-span-2 bg-gray-200 p-2">
                    {{ __('Description') }}
                </p>
                <p class="col-span-9 p-2">
                    {{ $role->description ?? '-' }}
                </p>
            </div>

            <div class="flex flex-row justify-start">

                <x-link-primary-button href="{{ route('admin.roles.edit', $role) }}"
                                       class="mr-6 px-12">
                    <i class="fa-solid fa-edit pr-2 text-lg"></i>
                    Edit
                </x-link-primary-button>

                <x-link-secondary-button href="{{ route('admin.roles.index') }}"
                                         class="mr-6">
                    All Roles
                </x-link-secondary-button>

                @if($role->name !== 'super-user')
                    <x-secondary-button type="submit"
                                        formaction="{{ route('admin.roles.delete', $role) }}"
                                        formmethod="post"
                                        class="mr-6">
                        <i class="fa-solid fa-delete-left pr-2 text-lg"></i>
                        Delete
                    </x-secondary-button>
                @endif
            </div>

        </form>

    </section>

</x-admin-layout>
