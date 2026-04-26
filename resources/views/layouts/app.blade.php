<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SmartAccount')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            margin: 0;
            size: auto;
        }
        
        @media print {
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                height: 100% !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation Header -->
    <nav class="bg-blue-600 shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-white text-xl font-bold">SmartAccount</a>
                </div>
                <div class="flex space-x-6">
                    <a href="{{ route('home') }}" class="text-white hover:text-blue-100 transition">Home</a>
                    <a href="{{ route('account-statement.index') }}" class="text-white hover:text-blue-100 transition">Statements</a>
                    <a href="{{ route('account-statement.import.form') }}" class="text-white hover:text-blue-100 transition">Import</a>
                    <a href="{{ route('import-rules.index') }}" class="text-white hover:text-blue-100 transition">Import Rules</a>
                    <a href="{{ route('flats.index') }}" class="text-white hover:text-blue-100 transition">Flats</a>
                    <a href="{{ route('vendors.index') }}" class="text-white hover:text-blue-100 transition">Vendors</a>
                    <a href="{{ route('categories.index') }}" class="text-white hover:text-blue-100 transition">Categories</a>
                    <a href="{{ route('petty-cash.index') }}" class="text-white hover:text-blue-100 transition">Petty Cash</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white text-center py-4 mt-12">
        <p>&copy; 2026 SmartAccount. All rights reserved.</p>
    </footer>
</body>
</html>
