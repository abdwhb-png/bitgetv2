<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, viewport-fit=cover">

    <title inertia>{{ config('app.name', 'ByBit') }}</title>

    <meta name="request-path" content="{{ \Request::path() }}" />

    @include('inc.favicon')

    <!-- font -->
    <link rel="stylesheet" href="{{ asset('app_assets') }}/fonts/fonts.css">
    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('app_assets') }}/fonts/font-icons.css">
    <link rel="stylesheet" href="{{ asset('app_assets') }}/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('app_assets') }}/css/swiper-bundle.min.css">
    <link rel="stylesheet"type="text/css" href="{{ asset('app_assets') }}/css/styles.css" />

    <!-- Head Scripts -->
    @routes
    @php
        $dir = Str::startsWith($page['component'], 'Auth/') ? '' : 'Application/';
    @endphp
    @vite(['resources/js/app.js', "resources/js/Pages/{$dir}{$page['component']}.vue"])
    @inertiaHead
</head>

<body id="application" class="dark primevue-dark">
    <!-- preloade -->
    <div class="preload preload-container">
        <div class="preload-logo" style="background-image: url('{{ asset('app_assets') }}/images/logo/144.png')">
            <div class="spinner"></div>
        </div>
    </div>
    <!-- /preload -->

    @inertia

    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
        var Tawk_API = Tawk_API || {},
            Tawk_LoadStart = new Date();
        (function() {
            var s1 = document.createElement("script"),
                s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/{{ config('app.tawk_token') }}';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();


        // Custom styling of Offset starts here
        Tawk_API.customStyle = {
            visibility: {
                //for desktop only
                desktop: {
                    //   position: 'br', // bottom-right
                    //   xOffset: 15, // 15px away from right
                    //   yOffset: 40 // 40px up from bottom
                },
                // for mobile only
                mobile: {
                    position: 'br', // bottom-right
                    xOffset: 0, // -5px away from left
                    yOffset: 65 // 60px up from bottom
                },
                // change settings of bubble if necessary
                bubble: {
                    //   rotate: '0deg',
                    //   xOffset: -20,
                    //   yOffset: 0
                }
            }
        }
    </script>
    <!--End of Tawk.to Script-->

    {{-- custom tawk styling --}}
    <script>
        const tawkInterval = setInterval(function() {
            // Sélectionner l'iframe à l'intérieur de l'élément avec l'ID "mytawkdiv"
            const tawkIframe = document.querySelector("iframe[title='chat widget'");

            // Vérifier si l'iframe existe et a un contenu
            if (tawkIframe && tawkIframe.contentDocument) {
                // Créer un nouvel élément <style>
                var style = document.createElement("style");
                style.id = "my-custom-tawk-style"
                style.type = "text/css";
                style.textContent = `
                    body.font-lato.tawk-mobile {
                        .tawk-button {
                            width: 92px !important;
                            height: 25px !important;
                        }
                        .tawk-button .tawk-text-bold-3 {
                            font-size: .8rem;
                        }
                    }
                    `;

                // Ajouter l'élément <style> au <head> de l'iframe
                tawkIframe.contentDocument.head.appendChild(style);

                clearInterval(tawkInterval);
            }
        }, 2000);
    </script>

    <!-- BodyClose Scripts -->
    <script type="text/javascript" src="{{ asset('app_assets') }}/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="{{ asset('app_assets') }}/js/jquery.min.js"></script>

    @auth
        {{-- <script type="text/javascript" src="{{ asset('app_assets') }}/js/swiper-bundle.min.js"></script>
        <script type="text/javascript" src="{{ asset('app_assets') }}/js/carousel.js"></script>
        <script type="text/javascript" src="{{ asset('app_assets') }}/js/apexcharts.js"></script>
        <script type="text/javascript" src="{{ asset('app_assets') }}/js/chart.bundle.min.js"></script>
        <script type="text/javascript" src="{{ asset('app_assets') }}/js/line-chart.js"></script> --}}
    @endauth

    <script type="text/javascript" src="{{ asset('app_assets') }}/js/main.js"></script>
</body>

</html>
