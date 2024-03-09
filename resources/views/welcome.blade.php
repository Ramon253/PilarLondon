<x-layout>
    <div class="flex flex-col items-center gap-10 max-w-screen-xl mx-auto text-center">
        <h1 class="text-4xl">Vienbenido a Pilar London</h1>
        @auth
            Hola {{auth()->user()->name}}
        @endauth
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

</x-layout>
