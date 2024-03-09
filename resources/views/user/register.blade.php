<x-layout>
    <x-card >
        <header class="text-center">
            <h2 class="text-2xl font-bold uppercase mb-1">Register</h2>
        </header>

        <form method="POST" class="flex w-fit rounded p-20  justify-center flex-col bg-gray-300 space-y-10" action="/user">
            @csrf
            <div class="">
                <label for="name" class="text-lg mb-2"> Name </label>
                <input id="name" type="text" class="border border-gray-600 rounded p-2 w-full" name="name" value="{{old('name')}}" />

                @error('name')
                <p class="text-red-500 text-xs mt-1">{{$message}}</p>
                @enderror
            </div>

            <div class="">
                <label for="email" class="inline-block text-lg mb-2">Email</label>
                <input id="email" type="email" class="border border-gray-600 rounded p-2 w-full" name="email" value="{{old('email')}}" />

                @error('email')
                <p class="text-red-500 text-xs mt-1">{{$message}}</p>
                @enderror
            </div>

            <div class="">
                <label for="password" class="inline-block text-lg mb-2">
                    Password
                </label>
                <input id="password" type="password" class="border border-gray-600 rounded p-2 w-full" name="password"
                       value="{{old('password')}}" />

                @error('password')
                <p class="text-red-500 text-xs mt-1">{{$message}}</p>
                @enderror
            </div>

            <div class="">
                <label for="password2" class="inline-block text-lg mb-2">
                    Confirm Password
                </label>
                <input id="password2" type="password" class="border border-gray-600 rounded p-2 w-full" name="password_confirmation"
                       value="{{old('password_confirmation')}}" />

                @error('password_confirmation')
                <p class="text-red-500 text-xs mt-1">{{$message}}</p>
                @enderror
            </div>

            <div class="" >
                <button class="p-4 font-extrabold rounded text-red-500 border-2 border-red-500 transition hover:scale-110 hover:bg-red-500 hover:text-white " type="submit">
                    Sign in
                </button>
            </div>

            <div class="mt-8">
                <p>
                    Already have an account?
                    <a href="/login" class="text-laravel">Login</a>
                </p>
            </div>
        </form>
    </x-card>
</x-layout>
