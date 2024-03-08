<x-layout>
    <x-card>
        <form action="/verify" method="POST" class="flex flex-col space-y-10">
            @csrf
            <div class="flex flex-col items-center space-y-10">
                <label for="code">We sent you an email to verify the login, if you dont see it it may be in your spam folder</label>
                <input class="w-20 border-black border-r-black" type="number" maxlength="6" minlength="6" id="code" name="code">
            </div>
            <button>
                Send
            </button>
        </form>
    </x-card>
</x-layout>
