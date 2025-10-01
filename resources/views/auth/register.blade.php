<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div class="form-group">
            <label for="name" class="form-label">Full Name</label>
            <input id="name" 
                   class="form-input" 
                   type="text" 
                   name="name" 
                   :value="old('name')" 
                   required 
                   autofocus 
                   autocomplete="name"
                   placeholder="Enter your full name">
            <x-input-error :messages="$errors->get('name')" class="form-error" />
        </div>

        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input id="email" 
                   class="form-input" 
                   type="email" 
                   name="email" 
                   :value="old('email')" 
                   required 
                   autocomplete="username"
                   placeholder="Enter your email address">
            <x-input-error :messages="$errors->get('email')" class="form-error" />
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <div class="relative">
                <input id="password" 
                       class="form-input pr-10" 
                       type="password"
                       name="password"
                       required 
                       autocomplete="new-password"
                       placeholder="Create a strong password">
                <button type="button" 
                        onclick="togglePassword('password')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                    <svg id="password-eye" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="form-error" />
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <div class="relative">
                <input id="password_confirmation" 
                       class="form-input pr-10" 
                       type="password"
                       name="password_confirmation" 
                       required 
                       autocomplete="new-password"
                       placeholder="Confirm your password">
                <button type="button" 
                        onclick="togglePassword('password_confirmation')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                    <svg id="password_confirmation-eye" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="form-error" />
        </div>

        <!-- Role Selection -->
        <div class="form-group">
            <label for="role" class="form-label">I am a</label>
            <select id="role" 
                    name="role" 
                    class="form-input" 
                    required>
                <option value="">Select your role</option>
                <option value="renter" {{ old('role') == 'renter' ? 'selected' : '' }}>Renter (Looking for a place)</option>
                <option value="landlord" {{ old('role') == 'landlord' ? 'selected' : '' }}>Landlord (Have properties to rent)</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="form-error" />
        </div>

        <!-- Terms Agreement -->
        <div class="form-group">
            <label class="flex items-start">
                <input type="checkbox" 
                       class="form-checkbox mt-1" 
                       required>
                <span class="ml-2 text-sm text-gray-600">
                    I agree to the 
                    <a href="{{ route('legal.terms') }}" class="text-primary-600 hover:text-primary-700">Terms of Service</a> 
                    and 
                    <a href="{{ route('legal.privacy') }}" class="text-primary-600 hover:text-primary-700">Privacy Policy</a>
                </span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-4">
            <button type="submit" class="btn btn-primary w-full">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                Create Account
            </button>
        </div>

        <!-- Login Link -->
        <div class="text-center">
            <p class="text-sm text-gray-600">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-primary-600 hover:text-primary-700 font-medium transition-colors">
                    Sign in here
                </a>
            </p>
        </div>
    </form>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const eye = document.getElementById(inputId + '-eye');
            
            if (input.type === 'password') {
                input.type = 'text';
                eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>';
            } else {
                input.type = 'password';
                eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }
    </script>
</x-guest-layout>