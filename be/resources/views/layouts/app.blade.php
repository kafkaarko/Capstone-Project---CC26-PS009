<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                background-color: #0a1628;
                color: #e2e8f0;
                min-height: 100vh;
            }

            /* Navbar */
            .app-navbar {
                background-color: #0d1f3c;
                border-bottom: 1px solid rgba(42, 111, 196, 0.3);
                padding: 0 24px;
                height: 60px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                position: sticky;
                top: 0;
                z-index: 100;
            }
            .app-navbar .brand {
                font-size: 18px;
                font-weight: 600;
                color: #fff;
                letter-spacing: 0.3px;
            }
            .app-navbar .brand span {
                color: #2a6fc4;
            }
            .app-navbar .nav-links {
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .app-navbar .nav-links a {
                font-size: 13px;
                color: #94a3b8;
                padding: 6px 12px;
                border-radius: 6px;
                text-decoration: none;
                transition: background 0.15s, color 0.15s;
            }
            .app-navbar .nav-links a:hover {
                background: rgba(42, 111, 196, 0.15);
                color: #fff;
            }
            .app-navbar .nav-right {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .app-navbar .user-badge {
                display: flex;
                align-items: center;
                gap: 8px;
                background: rgba(42, 111, 196, 0.15);
                border: 1px solid rgba(42, 111, 196, 0.3);
                border-radius: 8px;
                padding: 5px 12px;
                font-size: 13px;
                color: #cbd5e1;
                cursor: pointer;
            }
            .app-navbar .avatar {
                width: 28px;
                height: 28px;
                border-radius: 50%;
                background: #2a6fc4;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: 600;
                color: #fff;
            }

            /* Page Header */
            .app-page-header {
                background: linear-gradient(135deg, #0d2545, #1a3a6b);
                border-bottom: 1px solid rgba(42, 111, 196, 0.3);
            }
            .app-page-header .inner {
                max-width: 1100px;
                margin: 0 auto;
                padding: 20px 24px;
            }
            .app-page-header h2 {
                font-size: 18px;
                font-weight: 600;
                color: #fff;
                margin: 0;
            }

            /* Main Content */
            .app-main {
                max-width: 1100px;
                margin: 0 auto;
                padding: 32px 24px;
            }

            /* Card utility */
            .app-card {
                background: #0d1f3c;
                border: 1px solid rgba(42, 111, 196, 0.2);
                border-radius: 12px;
                padding: 24px;
                color: #e2e8f0;
            }
        </style>
    </head>
    <body class="font-sans antialiased">

        <!-- Navbar -->
        <nav class="app-navbar">
            <div class="brand">
                {{ config('app.name', 'Laravel') }}
            </div>

            <div class="nav-links">
                @include('layouts.navigation')
            </div>

            <div class="nav-right">
                @auth
                <div class="user-badge">
                    <div class="avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    {{ Auth::user()->name }}
                </div>
                @endauth
            </div>
        </nav>

        <!-- Page Heading -->
        @isset($header)
            <div class="app-page-header">
                <div class="inner">
                    {{ $header }}
                </div>
            </div>
        @endisset

        <!-- Page Content -->
        <main class="app-main">
            {{ $slot }}
        </main>

    </body>
</html>