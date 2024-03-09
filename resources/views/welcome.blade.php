<x-layout>
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
                                <div class="p-4 rounded bg-blue-200 flex flex-col border-black border shadow">
                                    <span>Nivel : {{$group->level}}</span>
                                    <span>Hora de las clases : {{($group->lessons_time)}}</span>
                                    <span>Dias : {{$group->lesson_days}}</span>
                                    <form action="/group/leave" method="post">
                                        @csrf
                                        <input type="hidden" name="student_id">
                                        <button
                                            class="border-2 p-4 text-black border-black rounded hover:scale-110 transition hover:bg-black hover:text-white font-medium"
                                            type="submit">
                                            Salirse
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </section>
    @endauth
    <div class="gap-4 grid grid-cols-2">
        @foreach($groups as $group)
            <div class="p-4 rounded bg-blue-200 flex flex-col border-black border shadow">
                <span>Nivel : {{$group->level}}</span>
                <span>Hora de las clases : {{($group->lessons_time)}}</span>
                <span>Dias : {{$group->lesson_days}}</span>
                <form action="/group/join/{{$group->id}}" method="post">
                    @csrf
                    <button
                        class="border-2 p-4 text-black border-black rounded hover:scale-110 transition hover:bg-black hover:text-white font-medium"
                        type="submit">
                        Unirse
                    </button>
                </form>
            </div>
        @endforeach
    </div>
</x-layout>
