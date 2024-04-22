<x-layout>
    <x-card>
        <div class="w-full flex flex-col items-center">
            <header class="text-center">
                <h2 class="text-2xl font-bold uppercase mb-1">Login</h2>
            </header>

            <form class="flex flex-col items-center space-y-10" method="POST" action="/api/login">
                @csrf
                <div class="flex flex-col space-y-2">
                    <label for="email" class="text-lg mb-2">Email</label>
                    <input type="email" id="email" class="border border-gray-200 rounded p-2 w-full" name="email"/>

                </div>
                <div class="flex flex-col space-y-2">
                    <label for="name" class="text-lg mb-2">Name</label>
                    <input type="text" id="name" class="border border-gray-200 rounded p-2 w-full" name="name"
                           value="{{old('name')}}"/>

                </div>

                <div class="flex flex-col space-y-2">
                    <label for="password" class="text-lg mb-2">
                        Password
                    </label>
                    <input id="password" type="password" class="border border-gray-200 rounded p-2 w-full"
                           name="password"
                           value="{{old('password')}}"/>

                </div>
                <div><label for="remember">Remember me</label>
                    <input type="checkbox" id="remember" name="remember_me">
                </div>

                <div>
                    <button class="p-3 bg-red-500 rounded" type="submit">
                        Log in
                    </button>
                </div>

                <div>
                    <p>
                        Don't have an account?
                        <a href="user/create" class="text-laravel">Register</a>
                    </p>
                </div>
            </form>
        </div>
    </x-card>
</x-layout>
