<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lato:wght@400;600;700&display=swap">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    {{--Product tour code--}}
    <link rel="stylesheet" href="/css/tour.css">
    {{--END product tour code--}}

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#fad038">
    <meta name="msapplication-TileColor" content="#fad038">
    <meta name="theme-color" content="#ffffff">
@stack('styles')
@livewireStyles

<!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>
<body class="antialiased font-sans">
<div class="relative min-h-screen bg-gray-50">
    <header class="pb-48 bg-gradient-to-r"
            style="--tw-gradient-from: {{ current_team()->getColors()[0] }}; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(255, 214, 3, 0)); --tw-gradient-to: {{ current_team()->getColors()[1] }};">

        @if(!isset($hideNavigation))
            @include('layouts.navigation')
        @else
            {{ $hideNavigation }}
        @endif

        @if(session()->has('account-verified'))
            <div class="bg-green-300 p-4">
                <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:max-w-7xl lg:px-8">
                    {{ session()->get('account-verified') }}
                </div>
            </div>
        @endif

        @if(isset($header))
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:max-w-7xl lg:px-8">
                <div class="relative flex flex-wrap items-center justify-center lg:justify-between">
                    <div class="flex flex-wrap items-center w-full text-left h-40">
                        {{ $header }}
                    </div>
                </div>
            </div>
        @endif
    </header>
    <main class="-mt-48 pb-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:max-w-7xl lg:px-8">
            <h1 class="sr-only">Profile</h1>
            <!-- Main 3 column grid -->
            <div class="grid grid-cols-1 gap-8 items-start lg:grid-cols-3 lg:gap-8">

                @if(!empty($sidebar))
                    <div class="grid grid-cols-1 gap-8">
                        {{ $sidebar }}
                    </div>
                @endif
                <div class="grid grid-cols-1 gap-8 {{ !empty($sidebar) ? 'lg:col-span-2' : 'lg:col-span-3' }}">
                    @if(!empty($extras))
                        {{ $extras }}
                    @endif
                    <section class="bg-white shadow">
                        {{ $slot }}
                    </section>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 lg:max-w-7xl">
            <div class="border-t border-gray-200 py-8 text-sm text-gray-500 text-center sm:text-left"><span
                        class="block sm:inline">© {{ \Carbon\Carbon::now()->year }} TrackingCoach.</span> <span
                        class="block sm:inline">All rights reserved.</span>
            </div>
        </div>
    </footer>
</div>

@if(request()->routeIs('dashboard'))
    @include('tours.dashboard')
@elseif(request()->routeIs('customers'))
    @include('tours.customer')
@elseif(request()->routeIs('customers.create'))
    @include('tours.customer-create')
@endif

@livewireScripts
@stack('scripts')

<script>
    function disableFormButtons(e) {
        e.querySelectorAll("button[type='submit']").forEach((button) => {
            button.setAttribute('disabled', true);
        });
    }
</script>

{{--Product tour code--}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="/js/tour.js"></script>
{{--END product tour code--}}

@if(request()->routeIs(['dashboard', 'customers', 'customers.create']) && current_team()->users()->isCustomer()->count() === 0)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const routeIs = '{{ request()->route()->getName() }}';

            if (!getCookie('tour-' + routeIs)) {
                setTimeout(() => {
                    document.getElementById('cd-tour-trigger').click();
                    setCookie('tour-' + routeIs, 1, 1);
                }, 1)
            }
        });

        function setCookie(cname, cvalue, exdays) {
          const d = new Date();
          d.setTime(d.getTime() + (exdays*24*60*60*1000));
          let expires = "expires="+ d.toUTCString();
          document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }

        function getCookie(cname) {
          let name = cname + "=";
          let decodedCookie = decodeURIComponent(document.cookie);
          let ca = decodedCookie.split(';');
          for(let i = 0; i <ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
              c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
              return c.substring(name.length, c.length);
            }
          }
          return "";
        }
    </script>
@endif

</body>
</html>
