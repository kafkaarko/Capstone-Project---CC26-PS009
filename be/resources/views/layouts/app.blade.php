<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FinBuddy') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/finbuddy-logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            background: #0a1628;
            color: #e2e8f0;
            min-height: 100vh;
            margin: 0;
            font-family: 'Figtree', sans-serif;
        }

        /* ── Navbar ── */
        .app-navbar {
            background: #0d1f3c;
            border-bottom: 1px solid rgba(26, 160, 120, 0.3);
            padding: 0 24px;
            height: 58px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .app-navbar .brand {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 17px;
            font-weight: 600;
            color: #fff;
            text-decoration: none;
        }
        .brand-icon {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            background: linear-gradient(135deg, #1a6fc4, #3ec97a);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
        }
        .brand span { color: #3ec97a; }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .nav-links a {
            font-size: 13px;
            color: #94a3b8;
            padding: 6px 13px;
            border-radius: 7px;
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
        }
        .nav-links a:hover {
            background: rgba(26, 160, 120, 0.12);
            color: #fff;
        }
        .nav-links a.active {
            background: rgba(26, 160, 120, 0.18);
            color: #3ec97a;
        }
        .nav-right { display: flex; align-items: center; gap: 12px; }

        /* ── User Dropdown ── */
        .user-dropdown { position: relative; }
        .user-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(26, 160, 120, 0.1);
            border: 1px solid rgba(26, 160, 120, 0.25);
            border-radius: 8px;
            padding: 5px 13px;
            font-size: 13px;
            color: #cbd5e1;
            cursor: pointer;
            user-select: none;
            transition: background 0.15s, border-color 0.15s;
        }
        .user-badge:hover {
            background: rgba(26, 160, 120, 0.18);
            border-color: rgba(26, 160, 120, 0.45);
        }
        .avatar {
            width: 27px;
            height: 27px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1a6fc4, #3ec97a);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: #fff;
        }
        .chevron {
            width: 14px; height: 14px;
            fill: #64748b;
            transition: transform 0.2s;
            flex-shrink: 0;
        }
        .user-dropdown.open .chevron { transform: rotate(180deg); }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 6px);
            background: #0d1f3c;
            border: 1px solid rgba(26, 160, 120, 0.25);
            border-radius: 10px;
            min-width: 180px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0,0,0,0.35);
            z-index: 200;
        }
        .user-dropdown.open .dropdown-menu { display: block; }

        .dropdown-header {
            padding: 12px 16px 10px;
            border-bottom: 1px solid rgba(26, 160, 120, 0.12);
        }
        .dropdown-header .dh-name {
            font-size: 13px;
            font-weight: 600;
            color: #e2e8f0;
        }
        .dropdown-header .dh-email {
            font-size: 11px;
            color: #475569;
            margin-top: 2px;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 10px 16px;
            font-size: 13px;
            color: #94a3b8;
            text-decoration: none;
            transition: background 0.12s, color 0.12s;
            cursor: pointer;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
        }
        .dropdown-item:hover { background: rgba(26, 160, 120, 0.1); color: #e2e8f0; }
        .dropdown-item.danger:hover { background: rgba(248,113,113,0.08); color: #f87171; }
        .dropdown-item svg { width: 14px; height: 14px; flex-shrink: 0; }

        /* ── Page Header ── */
        .app-page-header {
            background: linear-gradient(135deg, #0d2545 0%, #0d2535 60%, #0a2020 100%);
            border-bottom: 1px solid rgba(26, 160, 120, 0.25);
        }
        .app-page-header .inner {
            max-width: 1100px;
            margin: 0 auto;
            padding: 16px 24px;
        }
        .app-page-header h2 {
            font-size: 16px;
            font-weight: 600;
            color: #fff;
            margin: 0;
        }

        /* ── Main ── */
        .app-main {
            max-width: 1100px;
            margin: 0 auto;
            padding: 28px 24px;
        }
    </style>
</head>
<body class="font-sans antialiased">

    <nav class="app-navbar">
        <a href="{{ route('dashboard.expense') }}" class="brand">
            <div class="brand-icon">FB</div>
            Fin<span>Buddy</span>
        </a>

        <div class="nav-links">
            <a href="{{ route('dashboard.expense') }}"
               class="{{ request()->routeIs('dashboard.expense') ? 'active' : '' }}">
                Dashboard
            </a>
            <a href="{{ route('upload.page') }}"
               class="{{ request()->routeIs('upload.page') ? 'active' : '' }}">
                Upload
            </a>
        </div>

        <div class="nav-right">
            @auth
            <div class="user-dropdown" id="userDropdown">

                {{-- Trigger --}}
                <div class="user-badge" onclick="toggleDropdown()">
                    <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                    {{ Auth::user()->name }}
                    <svg class="chevron" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </div>

                {{-- Dropdown --}}
                <div class="dropdown-menu">

                    {{-- User info --}}
                    <div class="dropdown-header">
                        <div class="dh-name">{{ Auth::user()->name }}</div>
                        <div class="dh-email">{{ Auth::user()->email }}</div>
                    </div>

                    {{-- Profile --}}
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        Profile
                    </a>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item danger">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                            Log Out
                        </button>
                    </form>

                </div>
            </div>
            @endauth
        </div>
    </nav>

    @isset($header)
        <div class="app-page-header">
            <div class="inner">{{ $header }}</div>
        </div>
    @endisset

    <main class="app-main">
        {{ $slot }}
    </main>

    <script>
        function toggleDropdown() {
            document.getElementById('userDropdown').classList.toggle('open');
        }

        // Tutup dropdown kalau klik di luar
        document.addEventListener('click', function (e) {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown && !dropdown.contains(e.target)) {
                dropdown.classList.remove('open');
            }
        });
    </script>

</body>
</html>