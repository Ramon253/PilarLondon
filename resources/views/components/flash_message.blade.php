@if( session()->has('message'))
    <div class="flashMessage bg-red-500 text-white p-4"> {{session()->get('message')}} </div>
@endif
