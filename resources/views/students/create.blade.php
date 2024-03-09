<x-layout>
    <x-card>
        <div class="w-full flex flex-col items-center">
            <header class="text-center">
                <h2 class="text-2xl font-bold uppercase mb-1">Unete</h2>
            </header>

            <form class="flex flex-col items-center space-y-10" method="POST" action="/student">
                @csrf
                <div class="flex flex-col space-y-2">
                    <label for="full_name" class="text-lg mb-2">Nombre completo</label>
                    <input type="text" id="full_name" class="border border-gray-200 rounded p-2 w-full" name="full_name"/>

                    @error('full_name')
                    <p class="text-red-500 text-xs mt-1">{{$message}}</p>
                    @enderror
                </div>
                <div class="flex flex-col space-y-2">
                    <label for="surname" class="text-lg mb-2">Apellidos</label>
                    <input type="text" id="surname" class="border border-gray-200 rounded p-2 w-full" name="surname"
                           value="{{old('surname')}}"/>

                    @error('surname')
                    <p class="text-red-500 text-xs mt-1">{{$message}}</p>
                    @enderror
                </div>

                <div class="flex flex-col space-y-2">
                    <label for='level' class="text-lg mb-2">
                        Nivel de ingles
                    </label>
                    <select name="level"  class="border border-gray-200 rounded p-2 w-full" id="level">
                        <option value="A1">A1</option>
                        <option value="A2">A2</option>
                        <option value="B1">B1</option>
                        <option value="B2">B2</option>
                        <option value="C1">C1</option>
                        <option value="C2">C2</option>
                    </select>

                    @error('level')
                    <p class="text-red-500 text-xs mt-1">{{$message}}</p>
                    @enderror
                </div>
                <div class="flex flex-col space-y-2">
                    <label for='birth_date' class="text-lg mb-2">
                        Fecha de nacimiento
                    </label>
                    <input id='birth_date' type='date' class="border border-gray-200 rounded p-2 w-full"
                           name='birth_date'
                           value="{{old('birth_date')}}"/>
                    @error('birth_date')
                    <p class="text-red-500 text-xs mt-1">{{$message}}</p>
                    @enderror
                </div>
                <div>
                    <button class="p-3 bg-red-500 rounded" type="submit">
                        Unirte
                    </button>
                </div>

            </form>
        </div>
    </x-card>
</x-layout>
