<x-layout>
    @auth
        <section class="min-h-screen flex flex-col max-w-screen-xl mx-auto">
            <header>
                <h1>Vienvenido {{ auth()->user()->name}}</h1>
            </header>
            <div>
                <h2>Tus classes</h2>
                <ul>
                    @foreach( $groups as $group)
                        <li>{{$group->level}}</li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endauth
</x-layout>
