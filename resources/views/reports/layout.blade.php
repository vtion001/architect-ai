<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        @page { size: A4; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #f0f2f5; display: flex; justify-content: center; padding: 20px; font-family: sans-serif; }
        .report-wrapper { 
            width: 210mm; 
            min-height: 297mm; 
            background: white; 
            box-shadow: 0 0 20px rgba(0,0,0,0.1); 
            margin: 0 auto;
            position: relative;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        @media print {
            body { padding: 0; background: white; }
            .report-wrapper { box-shadow: none; margin: 0; border-radius: 0; }
        }
        @yield('styles')
    </style>
</head>
<body>
    <div class="report-wrapper @yield('container_class')">
        @yield('content')
    </div>
</body>
</html>
