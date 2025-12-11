<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Joke') }}
        </h2>
    </x-slot>

    <section class="py-12 mx-12 space-y-4">

        <form class="flex flex-col gap-4" method="post" action="#">
            @csrf

            <h3 class="text-xl my-6">Joke Details</h3>

            <div class="grid grid-cols-12 gap-8">
                <p class="col-span-2 bg-gray-200 p-2">{{ __('Title') }}</p>
                <p class="col-span-9 p-2">{{ $joke->title }}</p>
            </div>

            <div class="grid grid-cols-12 gap-8">
                <p class="col-span-2 bg-gray-200 p-2">{{ __('Content') }}</p>
                <div class="col-span-9 p-2 prose max-w-none">
                    {!! nl2br(e($joke->content ?? '-')) !!}
                </div>
            </div>

            <div class="grid grid-cols-12 gap-8">
                <p class="col-span-2 bg-gray-200 p-2">{{ __('Author') }}</p>
                <p class="col-span-9 p-2">{{ $joke->user->name ?? '-' }}</p>
            </div>

            <div class="flex items-center gap-4 mt-4">
                <form action="{{ route('votes.store', $joke) }}" method="POST">
                    @csrf
                    <input type="hidden" name="value" value="1">
                    <button type="submit" class="text-green-500 hover:text-green-700">
                        <i class="fa-solid fa-thumbs-up"></i> Like
                        <span>{{ $joke->votes()->where('value', 1)->count() }}</span>
                    </button>
                </form>
                <span id="like-count-{{ $joke->id }}">{{ $joke->votes()->where('value', 1)->count() }}</span>

                <form action="{{ route('votes.store', $joke) }}" method="POST">
                    @csrf
                    <input type="hidden" name="value" value="-1">
                    <button type="submit" class="text-red-500 hover:text-red-700">
                        <i class="fa-solid fa-thumbs-down"></i> Dislike
                        <span>{{ $joke->votes()->where('value', -1)->count() }}</span>
                    </button>
                </form>
                <span id="dislike-count-{{ $joke->id }}">{{ $joke->votes()->where('value', -1)->count() }}</span>
            </div>

            <div class="grid grid-cols-12 gap-8">
                <p class="col-span-2 bg-gray-200 p-2">{{ __('Categories') }}</p>
                <p class="col-span-9 p-2">
                    @foreach($joke->categories as $cat)
                        <span class="inline-block px-2 py-1 mr-1 mt-1 text-xs bg-gray-200 rounded">{{ $cat->title }}</span>
                    @endforeach
                </p>
            </div>

            <div class="flex flex-row justify-start">
                <x-link-primary-button href="{{ route('jokes.edit', $joke) }}" class="mr-6 px-12">
                    <i class="fa-solid fa-edit pr-2 text-lg"></i>
                    {{ __(' Edit') }}
                </x-link-primary-button>

                <x-link-secondary-button href="{{ route('jokes.index') }}" class="mr-6">
                    {{ __(' All Jokes') }}
                </x-link-secondary-button>

                <x-secondary-button type="submit" class="mr-6">
                    <i class="fa-solid fa-delete-left pr-2 text-lg"></i>
                    {{ __('Delete') }}
                </x-secondary-button>
            </div>

        </form>

    </section>

</x-app-layout>
