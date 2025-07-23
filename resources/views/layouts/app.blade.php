<!DOCTYPE html>
<html lang="en">
    <head>
        @include('layouts.components.head')
    </head>
    <body>
        <div class="wrapper">
            @include('layouts.components.header')
            @include('layouts.components.sidebar')

            <div class="content-wrapper px-4 py-2">
                @yield('content')
            </div>

            @include('layouts.components.footer')
            @include('components.modals.delete')
        </div>
    </body>
</html>
