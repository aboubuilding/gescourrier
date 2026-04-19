<!DOCTYPE html>
<html lang="fr" data-theme="light" data-layout="horizontal">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Système de Gestion des Courriers Officiels — République du Togo">
    
    <title>@yield('title', 'Dashboard') — Courriers Officiels</title>

    <!-- Favicon & PWA -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('app/assets/img/favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('app/assets/img/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Core CSS (dépendances) -->
    <link rel="stylesheet" href="{{ asset('app/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('app/assets/css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('app/assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('app/assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('app/assets/plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('app/assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('app/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('app/assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('app/assets/plugins/@simonwep/pickr/themes/nano.min.css') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <!-- App CSS (styles de l'app) -->
    <link rel="stylesheet" href="{{ asset('app/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('app/assets/css/mystyle.css') }}">
    
    <!-- Layout CSS personnalisé (dans public/) -->
    <link rel="stylesheet" href="{{ asset('css/layout.css?v='.filemtime(public_path('css/layout.css'))) }}">

    {{-- CSS supplémentaire depuis les vues enfants --}}
    @stack('css')
    @yield('css')
</head>

<body class="menu-horizontal">

    {{-- Toast notifications container --}}
    <div id="toast-container" aria-live="polite" aria-atomic="true"></div>

    {{-- Composants réutilisables (partials) --}}
    @include('layouts.partials._search-modal')
    @include('layouts.partials._header')

    {{-- Wrapper principal --}}
    <div class="page-wrapper">

        {{-- Barre de contexte (titre + breadcrumb) --}}
        @hasSection('page_title')
        <div class="page-header-bar">
            <div class="page-header-left">
                <h1 class="page-title-main">
                    <span class="title-icon">
                        <i class="fas @yield('page_icon', 'fa-file-alt')"></i>
                    </span>
                    @yield('page_title')
                </h1>
                @hasSection('breadcrumb')
                <nav aria-label="Fil d'Ariane">
                    <ul class="breadcrumb-custom">
                        <li><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                        @yield('breadcrumb')
                    </ul>
                </nav>
                @endif
            </div>
            <div class="page-header-right">
                @yield('page_actions')
            </div>
        </div>
        @endif

        {{-- Contenu principal injecté par les vues enfants --}}
        <main class="content-area" role="main">
            @yield('contenu')
        </main>

        {{-- Footer --}}
        @include('layouts.partials._footer')

    </div>

    {{-- Scripts requis (dépendances) --}}
    <script src="{{ asset('app/assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="{{ asset('app/assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('app/assets/js/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('app/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('app/assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('app/assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('app/assets/plugins/chartjs/chart.min.js') }}"></script>
    <script src="{{ asset('app/assets/plugins/chartjs/chart-data.js') }}"></script>
    <script src="{{ asset('app/assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('app/assets/plugins/apexchart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('app/assets/plugins/apexchart/chart-data.js') }}"></script>
    <script src="{{ asset('app/assets/plugins/@simonwep/pickr/pickr.es5.min.js') }}"></script>
    <script src="{{ asset('app/assets/js/theme-colorpicker.js') }}"></script>
    <script src="{{ asset('app/assets/js/script.js') }}"></script>

    {{-- JS supplémentaire depuis les vues enfants --}}
    @stack('js')
    @yield('js')

    {{-- Layout JS personnalisé (dans public/) --}}
    <script src="{{ asset('js/layout.js?v='.filemtime(public_path('js/layout.js'))) }}"></script>

</body>
</html>