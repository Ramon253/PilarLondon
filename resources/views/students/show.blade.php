<x-layout>
    <div class="max-w-screen-xl mx-auto flex-col flex items-center text-xl font-medium space-y-4">
        <span>Nombre : {{$student->full_name}}</span>
        <span>Apellido : {{$student->surname}}</span>
        <span>level : {{$student->level}}</span>
        <span>Birth date : {{$student->birth_date}}</span>
        <button class="p-4 rounded border-2 border-black text-black hover:scale-110 hover:bg-black hover:text-gray-100 transition">
            <a href="/student/edit">Edit</a>
        </button>
    </div>
</x-layout>
