<x-guest-layout>
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
            Créer un compte
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
            Rejoignez-nous et commencez dès aujourd'hui.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div class="space-y-1">
            <x-input-label for="name" :value="__('Nom complet')" class="text-xs uppercase tracking-wider font-semibold text-gray-600 dark:text-gray-400" />
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <x-text-input id="name" class="block pl-10 w-full bg-gray-50 dark:bg-gray-800/50 border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-transparent rounded-xl transition-all duration-200" type="text" name="name" :value="old('name')" required autofocus placeholder="Jean Dupont" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <div class="space-y-1">
            <x-input-label for="email" :value="__('Adresse Email')" class="text-xs uppercase tracking-wider font-semibold text-gray-600 dark:text-gray-400" />
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </div>
                <x-text-input id="email" class="block pl-10 w-full bg-gray-50 dark:bg-gray-800/50 border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-transparent rounded-xl transition-all duration-200" type="email" name="email" :value="old('email')" required placeholder="nom@exemple.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div class="space-y-1">
            <x-input-label for="password" :value="__('Mot de passe')" class="text-xs uppercase tracking-wider font-semibold text-gray-600 dark:text-gray-400" />
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <x-text-input id="password" class="block pl-10 w-full bg-gray-50 dark:bg-gray-800/50 border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-transparent rounded-xl transition-all duration-200" type="password" name="password" required placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="space-y-1">
            <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" class="text-xs uppercase tracking-wider font-semibold text-gray-600 dark:text-gray-400" />
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <x-text-input id="password_confirmation" class="block pl-10 w-full bg-gray-50 dark:bg-gray-800/50 border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-transparent rounded-xl transition-all duration-200" type="password" name="password_confirmation" required placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>
        <div class="mt-4">
            <x-input-label for="invite_code" value="Code d’accès" />
            <x-text-input
                id="invite_code"
                name="invite_code"
                type="password"
                class="block mt-1 w-full"
                required
                autocomplete="new-password"
                inputmode="none" />
            <x-input-error :messages="$errors->get('invite_code')" class="mt-2" />
        </div>

        <div class="pt-4">
            <button class="w-full inline-flex items-center justify-center px-8 py-3.5 bg-indigo-600 border border-transparent rounded-xl font-bold text-white hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition-all duration-200">
                {{ __('Créer mon compte') }}
            </button>
        </div>

        <div class="relative flex py-4 items-center">
            <div class="flex-grow border-t border-gray-200 dark:border-gray-700"></div>
            <span class="flex-shrink mx-4 text-gray-400 text-xs uppercase tracking-widest">ou</span>
            <div class="flex-grow border-t border-gray-200 dark:border-gray-700"></div>
        </div>

        <div>
            <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center px-8 py-3 border-2 border-indigo-600 dark:border-indigo-500 rounded-xl font-bold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all duration-200 text-sm">
                {{ __('Déjà inscrit ? Se connecter') }}
            </a>
        </div>
    </form>
</x-guest-layout>