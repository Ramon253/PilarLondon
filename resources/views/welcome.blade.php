<x-layout>
    <div class="flex flex-col items-center gap-10 max-w-screen-xl mx-auto text-center">
        <h1 class="text-4xl">Vienbenido a Pilar London</h1>
        <div>
            <p>En Pilar London, creemos que el aprendizaje del inglés abre puertas a un mundo de oportunidades. Nos
                enorgullece ofrecer una experiencia educativa de alta calidad, diseñada para adaptarse a estudiantes de
                todas las edades y niveles de habilidad. Nuestra misión es proporcionar una educación en inglés
                accesible, efectiva y personalizada que permita a nuestros estudiantes alcanzar sus objetivos
                personales, académicos y profesionales.</p>
        </div>
        <button class="border-2 p-3 rounded border-blue-500 text-blue-500 transition hover:scale-110 hover:bg-blue-500 hover:text-gray-100">
            <a href="/student/create">Unirte</a>
        </button>
    </div>
    @auth
        <section class=" flex flex-col gap-10 items-center max-w-screen-xl mx-auto ">
            <header class="w-full text-center">
                <h1 class="text-6xl bg-cyan-50 p-4 rounded-2xl font-bold">Vienvenido {{ auth()->user()->name}}</h1>
            </header>
            @if(isset( $isStudent))
                <div class="bg-blue-200 p-10 rounded-2xl w-full">
                    <h2 class="font-medium text-5xl text-center">Tus classes</h2>
                    <ul class="text-center appearance-none flex flex-col p-4">
                        @foreach( $yourGroups as $group)
                            <li>
                                <x-group_card :group="$group">
                                    <form action="/group/{{$group->id}}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="student_id">
                                        <button
                                            class="border-2 p-4 text-black border-black rounded hover:scale-110 transition hover:bg-black hover:text-white font-medium"
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
    @endauth

</x-layout>
