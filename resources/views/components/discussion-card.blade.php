<x-card class="mb-4">
    <div class="flex items-center space-x-4 mb-4">
        <img src="{{ $discussion->user->avatar }}" alt="{{ $discussion->user->name }}" class="w-12 h-12 rounded-full">
        <div>
            <h3 class="font-semibold">{{ $discussion->user->name }}</h3>
            <p class="text-gray-600 text-sm">{{ $discussion->created_at->diffForHumans() }}</p>
        </div>
    </div>

    <h2 class="mb-4 text-xl font-semibold">{{ $discussion->title }}</h2>

    <p class="mb-4 text-gray-600">{{ $discussion->description }}</p>

    {{ $slot }}
</x-card>