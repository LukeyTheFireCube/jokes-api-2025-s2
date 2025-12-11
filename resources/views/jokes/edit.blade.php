<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Joke') }}
        </h2>
    </x-slot>

    <section class="py-12 mx-12 space-y-4">
        <nav class="flex flex-row justify-between">
            <x-link-primary-button class="bg-gray-500!" href="{{ route('jokes.index') }}">
                All Jokes
            </x-link-primary-button>
        </nav>

        <form class="flex flex-col gap-4" method="post" action="{{ route('jokes.update', $joke) }}">
            @csrf
            @method('patch')

            <h3>Edit Joke Details</h3>

            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-1">
                    <x-input-label for="title" :value="__('Title')" />
                    <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" value="{{ old('title') ?? $joke->title ?? '' }}" />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div class="flex flex-col gap-1">
                    <x-input-label for="content" :value="__('Content')" />
                    <textarea id="content" name="content" rows="8" class="block mt-1 w-full border rounded p-2">{{ old('content') ?? $joke->content ?? '' }}</textarea>
                    <x-input-error :messages="$errors->get('content')" class="mt-2" />
                </div>

                <div class="flex flex-col gap-1">
                    <x-input-label for="categories" :value="__('Categories')" />
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @php
                            $selected = old('categories', $joke->categories->pluck('id')->toArray());
                        @endphp

                        @foreach($categories as $category)
                            <label class="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    name="categories[]"
                                    value="{{ $category->id }}"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm"
                                    {{ in_array($category->id, $selected) ? 'checked' : '' }}
                                >
                                <span>{{ $category->title }}</span>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('categories')" class="mt-2" />
                </div>

                <div class="flex flex-col gap-1">
                    <x-input-label for="published_at" :value="__('Published At (optional)')" />
                    <x-text-input id="published_at" class="block mt-1 w-full" type="datetime-local" name="published_at" value="{{ old('published_at') ?? (isset($joke->published_at) ? $joke->published_at->format('Y-m-d\TH:i') : '') }}" />
                    <x-input-error :messages="$errors->get('published_at')" class="mt-2" />
                </div>
            </div>

            <div class="flex flex-row justify-start">
                <x-primary-button type="submit" class="mr-6 px-12">
                    <i class="fa-solid fa-save pr-2 text-lg"></i> Save
                </x-primary-button>

                <x-link-secondary-button href="{{ route('jokes.index') }}">
                    <i class="fa-solid fa-cancel pr-2 text-lg"></i> Cancel
                </x-link-secondary-button>
            </div>
        </form>
    </section>

</x-app-layout>
