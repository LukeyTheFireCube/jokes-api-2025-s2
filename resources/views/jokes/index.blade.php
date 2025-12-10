<x-admin-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Joke Admin') }}
        </h2>
    </x-slot>

    <section class="py-6 mx-12 space-y-4">

        <nav class="flex flex-row justify-between">
            <x-link-primary-button href="{{ route('admin.jokes.create') }}">
                <i class="fa-solid fa-plus pr-2"></i>
                {{ __('Add Joke') }}
            </x-link-primary-button>

            <div class="flex gap-6">
                <x-link-secondary-button href="{{ route('admin.jokes.trash') }}">
                    @if($trashCount>0)
                        <i class="fa-solid fa-trash pr-2 text-black"></i>
                        {{ __('Trash is full') }}
                    @else
                        <i class="fa-solid fa-trash-can pr-2"></i>
                        {{ __('Trash is empty') }}
                    @endif
                </x-link-secondary-button>

                <form action="{{ route('admin.jokes.index') }}" name="searchForm" class="flex flex-inline gap-2 align-top">
                    <x-text-input name="search" class="px-2 py-1 border border-gray-200" :value="$search ?? ''"/>
                    <x-primary-button type="submit">
                        <i class="fa-solid fa-search pr-2"></i>
                        {{ __('Search') }}
                    </x-primary-button>
                </form>

                <x-link-secondary-button href="{{ route('admin.jokes.index') }}">
                    <i class="fa-solid fa-list pr-2"></i>
                    {{ __('Show All') }}
                </x-link-secondary-button>
            </div>
        </nav>

        <table class="table w-full">
            <thead class="bg-black text-gray-200 overflow-hidden">
            <tr>
                <th class="p-2 w-1/4 rounded-tl-lg">{{ __('Title') }}</th>
                <th class="p-2 w-1/4 rounded-tl-lg">{{ __('Content') }}</th>
                <th class="p-2">{{ __('Author') }}</th>
                <th class="p-2">{{ __('Categories') }}</th>
                <th class="p-2">{{ __('Vote') }}</th>
                <th class="p-2 pr-8 text-right">{{ __('Published') }}</th>
                <th class="p-2 w-1/6 rounded-tr-lg">{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @forelse($jokes as $joke)
                <tr>
                    <td class="p-2 font-medium border-b border-b-gray-400">
                        {{ $joke->title }}
                    </td>

                    <td class="p-2 font-medium border-b border-b-gray-400">
                        {{ $joke->content }}
                    </td>

                    <td class="p-2 border-b border-b-gray-400">
                        {{ $joke->user->name ?? '-' }}
                    </td>

                    <td class="p-2 border-b border-b-gray-400">
                        @foreach($joke->categories as $cat)
                            <span class="inline-block px-2 py-1 mr-1 mt-1 text-xs bg-gray-200 rounded">{{ $cat->title }}</span>
                        @endforeach
                    </td>

                    <td class="p-2 border-b border-b-gray-400">
                        <div class="flex items-center gap-2">
                            <form action="{{ route('votes.store', $joke) }}" method="POST">
                                @csrf
                                <input type="hidden" name="value" value="1">
                                <button type="submit" class="text-green-500 hover:text-green-700">
                                    <i class="fa-solid fa-thumbs-up"></i>
                                    <span>{{ $joke->votes()->where('value', 1)->count() }}</span>
                                </button>
                            </form>
                            <span id="like-count-{{ $joke->id }}">{{ $joke->votes()->where('value', 1)->count() }}</span>

                            <form action="{{ route('votes.store', $joke) }}" method="POST">
                                @csrf
                                <input type="hidden" name="value" value="-1">
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <i class="fa-solid fa-thumbs-down"></i>
                                    <span>{{ $joke->votes()->where('value', -1)->count() }}</span>
                                </button>
                            </form>
                            <span id="dislike-count-{{ $joke->id }}">{{ $joke->votes()->where('value', -1)->count() }}</span>
                        </div>
                    </td>

                    <td class="p-2 pr-8 text-right border-b border-b-gray-400">
                        {{ $joke->published_at ? $joke->published_at->format('Y-m-d') : '-' }}
                    </td>

                    <td class="p-2 border-b border-b-gray-400">
                        <form action="{{ route('admin.jokes.delete', $joke) }}" class="grid grid-cols-3 gap-4" method="post">
                            @csrf

                            <x-link-primary-button class="overflow-hidden justify-center" href="{{ route('admin.jokes.show', $joke) }}">
                                <i class="fa-solid fa-eye text-lg"></i>
                                <span class="sr-only">{{ __('Show') }}</span>
                            </x-link-primary-button>

                            <x-link-primary-button class="bg-gray-700! hover:bg-gray-500! overflow-hidden justify-center" href="{{ route('admin.jokes.edit', $joke) }}">
                                <i class="fa-solid fa-edit text-lg"></i>
                                <span class="sr-only">{{ __('Edit') }}</span>
                            </x-link-primary-button>

                            <x-secondary-button class="overflow-hidden justify-center" type="submit">
                                <i class="fa-solid fa-delete-left text-lg"></i>
                                <span class="sr-only">{{ __('Delete') }}</span>
                            </x-secondary-button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-4">{{ __('No jokes available') }}</td>
                </tr>
            @endforelse
            </tbody>
            <tfoot>
            <tr>
                <td colspan="5" class="pt-4">{{ $jokes->links() }}</td>
            </tr>
            </tfoot>
        </table>

    </section>

</x-admin-layout>
