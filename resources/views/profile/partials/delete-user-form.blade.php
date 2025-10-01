<form method="post" action="{{ route('profile.destroy') }}" class="p-6">
    @csrf
    @method('delete')

    <h2 class="text-lg font-medium text-gray-900">
        {{ __('Delete Account') }}
    </h2>

    <p class="mt-1 text-sm text-gray-600">
        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
    </p>

    <div class="mt-6">
        <x-input-label for="password" value="Password" class="sr-only" />

        <x-text-input
            id="password"
            name="password"
            type="password"
            class="mt-1 block w-3/4"
            placeholder="Password"
        />

        <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
    </div>

    <div class="mt-6 flex justify-end">
        <x-danger-button class="ms-3">
            {{ __('Delete Account') }}
        </x-danger-button>
    </div>
</form>
