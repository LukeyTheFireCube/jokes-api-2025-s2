<x-admin-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Role Admin') }}
        </h2>
    </x-slot>

    <section class="py-6 mx-12 space-y-4">

        <nav class="flex flex-row justify-between">

            <x-link-primary-button
                href="{{ route('admin.roles.create') }}">
                <i class="fa-solid fa-plus pr-2"></i>
                {{ __('Add Role') }}
            </x-link-primary-button>

            <div class="flex gap-6">

                <form action="{{ route('admin.roles.index') }}"
                      name="searchForm"
                      class="flex flex-inline gap-2 align-top">

                    <x-text-input name="search"
                                  class="px-2 py-1 border border-gray-200"
                                  :value="$search??''"/>

                    <x-primary-button type="submit">
                        <i class="fa-solid fa-search pr-2"></i>
                        {{ __('Search') }}
                    </x-primary-button>

                </form>

                <x-link-secondary-button
                    href="{{ route('admin.roles.index') }}">
                    <i class="fa-solid fa-list pr-2"></i>
                    {{ __('Show All') }}
                </x-link-secondary-button>
            </div>

        </nav>

        <table class="table w-full">
            <thead class="bg-black text-gray-200">
            <tr>
                <th class="p-2 w-1/3 rounded-tl-lg">
                    {{ __('Role Name') }}
                </th>
                <th class="p-2 w-1/2">
                    {{ __('Description') }}
                </th>
                <th class="p-2 rounded-tr-lg">
                    {{ __('Actions') }}
                </th>
            </tr>
            </thead>
            <tbody>

            @forelse($roles as $role)
                <tr>
                    <td class="p-2 font-medium border-b border-b-gray-400">
                        {{ $role->name }}
                    </td>

                    <td class="p-2 border-b border-b-gray-400">
                        {{ $role->description ?? '-' }}
                    </td>

                    <td class="p-2 border-b border-b-gray-400">

                        <form class="grid grid-cols-3 gap-4"
                              action="{{ route('admin.roles.delete', $role) }}"
                              method="post">

                            <x-link-primary-button
                                class="overflow-hidden justify-center"
                                href="{{ route('admin.roles.show', $role) }}">
                                <i class="fa-solid fa-eye text-lg"></i>
                                <span class="sr-only">Show</span>
                            </x-link-primary-button>

                            <x-link-primary-button
                                class="bg-gray-700! hover:bg-gray-500! overflow-hidden justify-center"
                                href="{{ route('admin.roles.edit', $role) }}">
                                <i class="fa-solid fa-edit text-lg"></i>
                                <span class="sr-only">Edit</span>
                            </x-link-primary-button>

                            {{-- Disable delete button for super-user --}}
                            @if($role->name !== 'super-user')
                                <x-secondary-button class="overflow-hidden justify-center" type="submit">
                                    <i class="fa-solid fa-delete-left text-lg"></i>
                                    <span class="sr-only">Delete</span>
                                </x-secondary-button>
                            @else
                                <button disabled
                                        class="bg-gray-400 text-white p-2 rounded opacity-70 cursor-not-allowed">
                                    <i class="fa-solid fa-ban"></i>
                                </button>
                            @endif

                        </form>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="p-4">
                        {{ __('No roles available') }}
                    </td>
                </tr>
            @endforelse
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3" class="pt-4">
                    {{ $roles->links() }}
                </td>
            </tr>
            </tfoot>

        </table>

    </section>

</x-admin-layout>
