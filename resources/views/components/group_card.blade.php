@props(['group'])
<div>
    <a class="p-4 rounded bg-blue-200 flex flex-col border-black border shadow" href="/group/{{$group->id}}"><span>Nivel : {{$group->level}}</span>
        <span>Hora de las clases : {{($group->lessons_time)}}</span>
        <span>Dias : {{$group->lesson_days}}</span>
        {{$slot}}</a>
</div>
