<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pilar london</title>
    @vite('resources/css/app.css')
</head>
<body>
<nav class="flex relative flex-col bg-slate-500 p-5 drop-shadow-2xl">

    <div class="flex justify-between  items-center">
        <a href="/"><img class="size-24 aspect-square" src="{{asset('images/logo.png')}}" alt=""/></a>

        @auth
            <details
                class="absolute top-10 right-10 h-fit mr-10 flex-col rounded-3xl drop-shadow-md p-4 pt-2 items-start bg-slate-800 w-60 max-w-1/5 self-start">
                {{--Summary--}}
                <summary
                    class="flex p-2 pr-4  items-center list-none text-center text-white appearance-none cursor-pointer">
                    <span>
                        <img class="aspect-square rounded-3xl w-16" src="{{ asset('images/default-profile.jpg')}}" alt="">
                    </span>
                    <span class="w-full font-bold">
                        {{auth()->user()->name }}
                    </span>
                </summary>
                {{--Info--}}
                <ul class="bg-slate-900 rounded-xl divide-y text-gray-100">
                    <li class=" h-fit p-4 w-full ">
                        <a href="/user" class="group hover:text-white flex items-center space-x-2">
                            <i class="transition duration-300 ease-in-out group-hover:text-red-500 group-hover:rotate-180 group-hover:scale-110 fa-solid fa-gear"></i>
                            <span class="transition duration-300 ease-in-out group-hover:font-medium">Profile</span>
                        </a>
                    </li>
                    <li class=" h-fit  p-4 border-gray-500 w-full">
                        <form method="POST" action="/logout">
                            @csrf
                            <button class="group hover:text-white flex items-center space-x-2" type="submit">
                                <i class="transition duration-300 ease-in-out group-hover:text-red-500 group-hover:scale-110 fa-solid fa-door-closed"></i>
                                <span class="transition duration-300 ease-in-out group-hover:font-medium">Log out</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </details>
        @else
            <ul>
                <li>
                    <a href="/user/create" class="hover:text-red-50"><i class="fa-solid fa-user-plus"></i> Register</a>
                </li>
                <li>
                    <a href="/login" class="hover:text-red-50"><i class="fa-solid fa-arrow-right-to-bracket"></i> Login</a>
                </li>
                @endauth
            </ul>
    </div>
    {{--Navbar--}}
    <div class="w-full flex justify-center">
        <ul class="flex bg-slate-800 text-white shadow-2xl  items-center rounded h-10 w-96 justify-evenly space-x-10 justify-self-center self-end">
            <li class="flex rounded items-center h-2/3 p-2 hover:scale-110 hover:-translate-y-1 transition ease-in-out"><a href="">Tienda</a></li>
            <li class="flex rounded items-center h-2/3 p-2 hover:scale-110 hover:-translate-y-1 transition ease-in-out"><a href="/cart">Carrito</a></li>
            <li class="flex rounded items-center h-2/3 p-2 hover:scale-110 hover:-translate-y-1 transition ease-in-out"><a href="">Perfil</a></li>
        </ul>
    </div>
</nav>

<main>
    {{$slot}}
</main>
</body>
</html>
