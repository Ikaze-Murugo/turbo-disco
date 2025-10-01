<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Report Message') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Message Context -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Message Being Reported</h3>
                        <div class="text-sm text-gray-600 mb-2">
                            <strong>From:</strong> {{ $message->sender->name }}<br>
                            <strong>To:</strong> {{ $message->recipient->name }}<br>
                            <strong>Date:</strong> {{ $message->created_at->format('M j, Y g:i A') }}
                        </div>
                        <div class="bg-white p-3 rounded border">
                            <p class="text-gray-800">{{ $message->body }}</p>
                        </div>
                    </div>

                    <!-- Report Form -->
                    <form method="POST" action="{{ route('message-reports.store', $message) }}">
                        @csrf
                        
                        <div class="space-y-6">
                            <!-- Category -->
                            <div>
                                <x-input-label for="category" :value="__('Report Category')" />
                                <select id="category" name="category" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->name }}" {{ old('category') == $category->name ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('category')" class="mt-2" />
                            </div>

                            <!-- Title -->
                            <div>
                                <x-input-label for="title" :value="__('Report Title')" />
                                <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required />
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <!-- Description -->
                            <div>
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <!-- Priority -->
                            <div>
                                <x-input-label for="priority" :value="__('Priority')" />
                                <select id="priority" name="priority" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                            </div>

                            <!-- Evidence URLs -->
                            <div>
                                <x-input-label for="evidence_urls" :value="__('Evidence URLs (Optional)')" />
                                <div id="evidence-urls-container">
                                    <div class="evidence-url-input mb-2">
                                        <input type="url" name="evidence_urls[]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="https://example.com/evidence">
                                    </div>
                                </div>
                                <button type="button" id="add-evidence-url" class="mt-2 text-sm text-indigo-600 hover:text-indigo-500">+ Add another URL</button>
                                <x-input-error :messages="$errors->get('evidence_urls')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 space-x-4">
                            <a href="{{ url()->previous() }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Submit Report') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('add-evidence-url').addEventListener('click', function() {
            const container = document.getElementById('evidence-urls-container');
            const newInput = document.createElement('div');
            newInput.className = 'evidence-url-input mb-2';
            newInput.innerHTML = '<input type="url" name="evidence_urls[]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="https://example.com/evidence">';
            container.appendChild(newInput);
        });

        // Filter out empty evidence URLs before form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            const evidenceInputs = document.querySelectorAll('input[name="evidence_urls[]"]');
            evidenceInputs.forEach(function(input) {
                if (!input.value || input.value.trim() === '') {
                    input.disabled = true; // Disable empty inputs so they won't be submitted
                }
            });
        });
    </script>
</x-app-layout>
