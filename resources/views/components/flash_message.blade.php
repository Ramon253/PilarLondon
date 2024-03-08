@if( session()->has('message'))
    <div class="flashMessage"> {{session()->get('message')}} </div>
@endif
