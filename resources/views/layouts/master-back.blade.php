<!--

=========================================================
* Argon Dashboard 2 Tailwind - v1.0.1
=========================================================

* Product Page: https://www.creative-tim.com/product/argon-dashboard-tailwind
* Copyright 2022 Creative Tim (https://www.creative-tim.com)

* Coded by www.creative-tim.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html>
    {{-- Head --}}
    @include('back.partials.head')
    {{-- Head --}}

  <body class="m-0 font-sans text-base antialiased font-normal dark:bg-slate-900 leading-default bg-gray-50 text-slate-500">
    <div class="absolute w-full bg-blue-500 dark:hidden min-h-75"></div>
    <!-- sidenav  -->
    @include('back.partials.sidebar')
    <!-- end sidenav -->

    <main class="relative h-full max-h-screen transition-all duration-200 ease-in-out xl:ml-68 rounded-xl">
      <!-- Navbar -->
        @include('back.partials.navbar')
      <!-- end Navbar -->

      <!-- cards -->
    @yield('content')
    {{-- Foot --}}
    @include('back.partials.footer')
    {{-- Foot --}}
      <!-- end cards -->
    </main>
  </body>
  <!-- plugin for charts  -->
  @include('back.partials.scripts')
</html>
