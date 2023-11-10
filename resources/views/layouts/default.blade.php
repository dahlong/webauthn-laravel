<!doctype html>

<html>

<head>

   @include('includes.head')

</head>

<body>

<div class="">

   <header class="row">

        @include('includes.header')

   </header>

   <div id="main" class="content-body">

        @yield('content')

   </div>

   <footer class="row">

        @include('includes.footer')

   </footer>

</div>
    <script src="{{ asset('js/my_app.js') }}"></script>
    @stack('other-javascripts')
</body>

</html>