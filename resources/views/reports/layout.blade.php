<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        @page { size: A4; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #f0f2f5; display: flex; justify-content: center; padding: 40px 0; font-family: 'Inter', sans-serif; line-height: 1.6; }
        
        .report-wrapper { 
            width: 210mm; 
            min-height: 297mm; 
            background: white; 
            box-shadow: 0 0 20px rgba(0,0,0,0.1); 
            margin: 0 auto;
            position: relative;
            display: flex;
            flex-direction: column;
            /* Support standard single-column document flow */
        }

        .page-break { 
            page-break-after: always; 
            break-after: page; 
            margin-top: 40px;
        }

        @media print {
            body { padding: 0; background: white; }
            .report-wrapper { box-shadow: none; margin: 0; width: 100%; border: none; }
            .no-print { display: none; }
        }

        /* Clean Single-Column Flow */
        .report-content { 
            padding: 40px 60px; 
            line-height: 1.8; 
            font-size: 1.05rem; 
            color: #334155;
        }
        .report-content p { margin-bottom: 1.5rem; text-align: justify; }
        .report-content ul, .report-content ol { margin-bottom: 1.5rem; padding-left: 2rem; }
        .report-content li { margin-bottom: 0.5rem; }

        /* Tables - AI can use these */
        table { width: 100%; border-collapse: collapse; margin: 2rem 0; font-size: 0.95rem; }
        th { background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; text-align: left; font-weight: 600; color: #1e3a8a; }
        td { border: 1px solid #e2e8f0; padding: 12px; }
        tr:nth-child(even) { background: #fcfcfc; }

        /* Grid - AI can use these for data side-by-side */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin: 2rem 0; }
        
        /* Highlight Callouts */
        .callout { 
            background: #f0f9ff; 
            border-left: 4px solid #3b82f6; 
            padding: 1.5rem; 
            margin: 2rem 0; 
            border-radius: 0 8px 8px 0; 
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
