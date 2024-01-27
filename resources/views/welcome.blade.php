<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Welcome to {{ env('APP_NAME') }}</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.css"  rel="stylesheet" />
<style type="text/css">
    body {
        background: url({{ url('CGFlag.png') }}) no-repeat center center fixed; 
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
    }
</style>
    </head>
    <body class="antialiased bg-white">
        <div class="grid grid-cols-3 gap-3 pt-6">
            <div class="text-start">
            </div>
            <div class="text-center">
            </div>
            <div class="text-right">
                <h4 class="mb-4 text-4xl text-center font-bold leading-none tracking-tight text-gray-900">Grid Status</h4>
                <table class="w-full table-fixed text-3xl">
                    <tbody>
                        @foreach(App\Models\Robust::getGridStats() as $name => $val)
                            <tr class="odd:bg-white bg-gray-300 border-b">
                                <td class="text-right">{{ number_format($val) }}</td>
                                <th class="text-left">{{ $name }}</th>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
    </body>
</html>
