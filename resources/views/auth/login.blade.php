<x-guest-layout>
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
            Bon retour !
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
            Entrez vos identifiants pour accéder à votre espace.
        </p>
    </div>

    <x-auth-session-status class="mb-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 text-center text-sm font-medium text-green-600" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div class="space-y-1">
            <x-input-label for="email" :value="__('Adresse Email')" class="text-xs uppercase tracking-wider font-semibold text-gray-600 dark:text-gray-400" />
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>
                </div>
                <x-text-input id="email" class="block pl-10 w-full bg-gray-50 dark:bg-gray-800/50 border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-transparent rounded-xl transition-all duration-200" type="email" name="email" :value="old('email')" required autofocus placeholder="nom@exemple.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div class="space-y-1">
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Mot de passe')" class="text-xs uppercase tracking-wider font-semibold text-gray-600 dark:text-gray-400" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline" href="{{ route('password.request') }}">{{ __('Oublié ?') }}</a>
                @endif
            </div>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                </div>
                <x-text-input id="password" class="block pl-10 w-full bg-gray-50 dark:bg-gray-800/50 border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-transparent rounded-xl transition-all duration-200" type="password" name="password" required placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="flex items-center pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-gray-200 transition-colors">{{ __('Rester connecté') }}</span>
            </label>
        </div>

        <div class="pt-2">
            <button class="w-full inline-flex items-center justify-center px-8 py-3.5 bg-indigo-600 border border-transparent rounded-xl font-bold text-white hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition-all duration-200">
                {{ __('Se connecter') }}
            </button>
        </div>

        <div class="relative flex py-4 items-center">
            <div class="flex-grow border-t border-gray-200 dark:border-gray-700"></div>
            <span class="flex-shrink mx-4 text-gray-400 text-xs uppercase tracking-widest">ou</span>
            <div class="flex-grow border-t border-gray-200 dark:border-gray-700"></div>
        </div>

        <div>
            <a href="{{ route('register') }}" class="w-full inline-flex items-center justify-center px-8 py-3 border-2 border-indigo-600 dark:border-indigo-500 border-dashed rounded-xl font-bold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all duration-200 text-sm">
                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                {{ __('Créer un nouveau compte') }}
            </a>
        </div>
    </form>
</x-guest-layout>