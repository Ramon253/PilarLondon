<x-layout>
    <div class="max-w-screen-xl mx-auto space-y-4 ">
        <section class=" flex flex-col gap-10 items-center max-w-screen-xl mx-auto ">
            <header class="w-full text-center">
                <h1 class="text-6xl bg-cyan-50 p-4 rounded-2xl font-bold">Vienvenido {{ auth()->user()->name}}</h1>
            </header>
            @if(isset( $isStudent))
                <div class="bg-blue-800 p-10 text-gray-100 space-y-4 rounded-2xl w-full">
                    <h2 class="font-medium text-5xl text-center">Tus classes</h2>
                    <ul class="text-center appearance-none flex flex-col p-4">
                        @foreach( $yourGroups as $group)
                            <li>
                                <x-group_card class="bg-blue-900 border-white"  :group="$group">
                                    <form action="/group/{{$group->id}}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="student_id">
                                        <button
                                            class="border-2 p-4 text-red-500 border-red-500 rounded hover:scale-110 transition hover:bg-red-500 hover:text-white font-medium"
                                            type="submit">
                                            Salirse
                                        </button>
                                    </form>

                                </x-group_card>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </section>
        <div class="gap-4 grid grid-cols-2">
            @foreach($groups as $group)
                <x-group_card :group="$group">
                    <form action="/group/{{$group->id}}" method="post">
                        @csrf
                        <button
                            class="border-2 p-4 text-black border-black rounded hover:scale-110 transition hover:bg-black hover:text-white font-medium"
                            type="submit">
                            Unirse
                        </button>
                    </form>
                </x-group_card>
            @endforeach
        </div>
    </div>
</x-layout>
