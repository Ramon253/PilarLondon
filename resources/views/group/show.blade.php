<x-layout>
    <div class="flex flex-col max-w-screen-xl mx-auto text-center font-medium gap-10">
        <h1 class="text-6xl">Bienvenido a tu clase </h1>
        <x-group_card :group="$group"></x-group_card>
        <div>
            <h2 class="text-3xl">Posts</h2>
            <ul>
                @foreach($posts as $post)

                <x-post_card :post="$post">
                </x-post_card>
                @endforeach
            </ul>
        </div>
    </div>


</x-layout>
