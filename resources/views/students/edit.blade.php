<x-layout>
    <x-card>
        <div class="w-full flex flex-col items-center">
            <header class="text-center">
                <h2 class="text-2xl font-bold uppercase mb-1">Edita tu perfil</h2>
            </header>

            <form class="flex flex-col items-center space-y-10" method="POST" action="/student">
                @csrf
                @method('put')
                <div class="flex flex-col space-y-2">
                    <label for="full_name" class="text-lg mb-2">Nombre completo</label>
                    <input type="text" value="{{$student->full_name}}" id="full_name" class="border border-gray-200 rounded p-2 w-full" name="full_name"/>

                    @error('full_name')
                    <p class="text-red-500 text-xs mt-1">{{$message}}</p>
                    @enderror
                </div>
                <div class="flex flex-col space-y-2">
                    <label for="surname" class="text-lg mb-2">Apellidos</label>
                    <input type="text" id="surname" class="border border-gray-200 rounded p-2 w-full" name="surname"
                           value="{{$student->surname}}"/>

                    @error('surname')
                    <p class="text-red-500 text-xs mt-1">{{$message}}</p>
                    @enderror
                </div>

                <div class="flex flex-col space-y-2">
                    <label for='level' class="text-lg mb-2">
                        Nivel de ingles
                    </label>
                    <select name="level"  class="border border-gray-200 rounded p-2 w-full" id="level">
                        @php($options = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'])
                        @foreach($options as $level)
                            <option @if($level === $student->$level) selected @endif value="{{$level}}">{{$level}}</option>
                        @endforeach
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
                           value="{{$student->birth_date}}"/>
                    @error('birth_date')
                    <p class="text-red-500 text-xs mt-1">{{$message}}</p>
                    @enderror
                </div>
                <div>
                    <button class="p-3 bg-red-500 rounded" type="submit">
                        Editar
                    </button>
                </div>

            </form>
        </div>
    </x-card>
</x-layout>
