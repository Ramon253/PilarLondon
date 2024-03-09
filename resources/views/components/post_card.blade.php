@props(['post'])
<div class="p-4 items-center rounded bg-blue-200 flex flex-col border-black border gap-5 shadow">

    <span class="text-xl font-medium">{{$post->name}}</span>
    <span>Tema : {{$post->subject}}</span>
    <p class="w-52"><strong>Descripcion : </strong>{{$post->description}}</p>


</div>
