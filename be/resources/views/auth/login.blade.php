<x-guest-layout>
    <style>
        .auth-wrapper {
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            background: #0a1628;
        }
        .auth-card {
            display: flex;
            width: 620px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 40px rgba(0,0,0,0.5);
        }
        .auth-left {
            flex: 0 0 220px;
            background: linear-gradient(135deg, #0d2545, #1a3a6b);
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding: 24px;
        }
        .bar-chart { display: flex; align-items: flex-end; gap: 6px; }
        .bar {
            width: 18px;
            background: rgba(42,111,196,0.5);
            border-radius: 3px 3px 0 0;
            border: 1px solid rgba(42,111,196,0.7);
        }
        .auth-right {
            flex: 1;
            background: #fff;
            padding: 36px 32px;
        }
        .auth-right h1 {
            font-size: 22px;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 24px;
        }
        .field-group { margin-bottom: 16px; }
        .field-group label {
            display: block;
            font-size: 12px;
            color: #888;
            margin-bottom: 5px;
        }
        .field-group input[type=email],
        .field-group input[type=password] {
            width: 100%;
            border: none;
            border-bottom: 1.5px solid #ddd;
            padding: 6px 0;
            font-size: 14px;
            color: #333;
            outline: none;
            background: transparent;
        }
        .field-group input:focus { border-bottom-color: #2a6fc4; }
        .auth-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 12px 0 20px;
        }
        .remember-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #888;
        }
        .btn-login {
            width: 100%;
            padding: 10px;
            background: #2a6fc4;
            color: #fff;
            font-size: 14px;
            font-weight: 500;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-login:hover { background: #1e5aaa; }
        .signup-text {
            text-align: center;
            margin-top: 16px;
            font-size: 12px;
            color: #aaa;
        }
        .signup-text a { color: #2a6fc4; font-weight: 500; }
    </style>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="auth-wrapper">
        <div class="auth-card">
            {{-- Left decorative panel --}}
            <div class="auth-left">
                <div class="bar-chart">
                    <div class="bar" style="height:40px"></div>
                    <div class="bar" style="height:60px"></div>
                    <div class="bar" style="height:45px"></div>
                    <div class="bar" style="height:80px"></div>
                    <div class="bar" style="height:55px"></div>
                    <div class="bar" style="height:90px"></div>
                    <div class="bar" style="height:70px"></div>
                </div>
            </div>

            {{-- Right form panel --}}
            <div class="auth-right">
                <h1>Welcome Back!</h1>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="field-group">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email"
                               value="{{ old('email') }}" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div class="field-group">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password"
                               required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div class="auth-row">
                        <label class="remember-label">
                            <input type="checkbox" name="remember" id="remember_me"
                                   style="accent-color:#2a6fc4;">
                            Remember Me
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" style="font-size:12px;color:#2a6fc4;">
                                Forgot Password?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="btn-login">Login</button>

                    <p class="signup-text">
                        Don't have an account?
                        <a href="{{ route('register') }}">Sign Up Here</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>