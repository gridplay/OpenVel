@php
if (request()->has('error') && !empty(request()->input('error'))) {
    $e = request()->input('error');
    $error = ['type' => 'yellow', 'msg' => $e];
    if (strpos($e, ':')) {
        list($t, $m) = explode(':', $e);
        $error['type'] = $t;
        $error['msg'] = $m;
    }
}
if (!isset($error)){
    //$error = ['type' => 'yellow', 'msg' => 'Recaptcha SHOULD be working again'];
}
@endphp
<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', '') | {{ env('APP_NAME') }}</title>
        
        <meta property="og:title" content="@yield('title', '') | {{ env('APP_NAME') }}" />
        <meta property="og:site_name" content="{{ env('APP_NAME') }}" />
        <meta property="og:url" content="{{ url('/') }}" />
        <meta property="og:image" content="{{ url('favicon.png') }}" />
        <meta property="og:description" content="Virtual world powered by Opensimulator" />
        <meta property="og:locale" content="en_US" />
        <meta property="og:type" content="website" />
        <meta name="description" content="Virtual world powered by Opensimulator" />
        <meta name="keywords" content="roleplay, virtual world, opensim" />
        <meta name="author" content="chrisx84@live.ca" />

        <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.css"  rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="{{ url('app.css') }}">
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.css" />
        <script src="https://cdn.tiny.cloud/1/nxuzzjpdz5k2fiopzrje6qzv91ax922tt7o3ipv84o4tvh7e/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <script>
            function onAccountSubmit(token) {
                document.getElementById("accountform").submit();
            }
        </script>
    </head>
    <body class="antialiased bg-gray-100">
        @include('layout.navbar')
        <div class="container-2xl mx-auto">
            @if (isset($error))
                @include('layout.alert', ['error' => $error])
            @endif
            @yield('content')
        </div>
        @include('layout.footer')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.js" data-cfasync="false"></script>
        <script>
        window.cookieconsent.initialise({
          "palette": {
            "popup": {
              "background": "#252e39"
            },
            "button": {
              "background": "#14a7d0"
            }
          },
          "position": "bottom-left"
        });

        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/6589d23e07843602b805760e/1hih5qgnh';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
        })();
        tinymce.init({
            selector: 'textarea',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        });
        </script>
    </body>
</html>
