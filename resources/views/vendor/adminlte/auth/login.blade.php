@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-red: #be123c; /* Deep Crimson Red */
            --hover-red: #9f1239;
            --glow-red: rgba(190, 18, 60, 0.15);
            --dark-panel: #103791; /* Slate 900 */
            --text-title: #0f172a;
            --text-body: #475569;
            --input-bg: #f8fafc;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #0f172a;
            /* Technical matrix grid background from Option 2 */
            background-image: 
                linear-gradient(rgba(220, 38, 38, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(220, 38, 38, 0.03) 1px, transparent 1px);
            background-size: 35px 35px;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Combined Split-Screen Card Window */
        .portal-container {
            display: flex;
            width: 100%;
            max-width: 1000px;
            height: 620px;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 50px rgba(190, 18, 60, 0.1);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Left Side: Tech Brand Accent Panel (Option 1 Layout + Option 2 Theme) */
        .brand-side {
            flex: 1;
            background-color: var(--dark-panel);
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(190, 18, 60, 0.2) 0%, transparent 40%),
                linear-gradient(135deg, var(--dark-panel) 0%, #020617 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 50px;
            color: #ffffff;
            border-right: 1px solid rgba(226, 232, 240, 0.05);
        }

        /* Top-accent crimson indicator line */
        .brand-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 6px;
            background: linear-gradient(180deg, #e11d48, var(--primary-red));
        }


        .brand-title {
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 12px;
            color: #ffffff;
        }

        .brand-title span {
            color: #e11d48;
        }

        .brand-text {
            font-size: 0.95rem;
            color: #94a3b8;
            line-height: 1.6;
            font-weight: 400;
        }

        /* Right Side: Rebalanced Form Panel */
        .form-side {
            flex: 1.2;
            padding: 50px 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        .form-header {
            margin-bottom: 35px;
        }

        .form-header h2 {
            font-size: 1.75rem;
            color: var(--text-title);
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .form-header p {
            color: var(--text-body);
            font-size: 0.9rem;
            margin-top: 6px;
        }

        /* Form Inputs Layout */
        .field-group {
            margin-bottom: 22px;
        }

        .field-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-title);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            transition: color 0.2s;
            font-size: 0.95rem;
        }

        .portal-input {
            width: 100%;
            padding: 14px 16px 14px 45px;
            border: 1.5px solid #e2e8f0;
            background-color: var(--input-bg);
            border-radius: 12px;
            outline: none;
            font-size: 0.95rem;
            color: #1e293b;
            transition: all 0.2s ease;
        }

        .portal-input:focus {
            background-color: #ffffff;
            border-color: var(--primary-red);
            box-shadow: 0 0 0 4px var(--glow-red);
        }

        .portal-input:focus + i {
            color: var(--primary-red);
        }

        /* Password Reveal Switch */
        .view-trigger {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            transition: color 0.2s;
        }

        .view-trigger:hover {
            color: var(--primary-red);
        }

        /* Utilities Controls Row */
        .utility-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 26px 0;
            font-size: 0.85rem;
        }

        .remember-toggle {
            display: flex;
            align-items: center;
            cursor: pointer;
            color: var(--text-body);
            font-weight: 500;
        }

        .remember-toggle input {
            margin-right: 8px;
            accent-color: var(--primary-red);
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .recovery-link {
            color: var(--primary-red);
            text-decoration: none;
            font-weight: 600;
        }

        .recovery-link:hover {
            color: var(--hover-red);
            text-decoration: underline;
        }

        /* Submission Execution Action */
        .action-submit {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #e11d48, var(--primary-red));
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(190, 18, 60, 0.25);
            transition: all 0.2s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .action-submit:hover {
            box-shadow: 0 6px 20px rgba(190, 18, 60, 0.4);
            filter: brightness(1.05);
        }

        .action-submit:active {
            transform: scale(0.99);
        }

        /* Responsive Viewports Optimization */
        @media (max-width: 850px) {
            .brand-side {
                display: none;
            }
            .portal-container {
                max-width: 460px;
                height: auto;
            }
            .form-side {
                padding: 45px 35px;
            }
        }

        /* Keep your standard input wrapper layout */
        .input-wrapper {
            position: relative;
        }

        /* Base style for icons inside the wrapper */
        .input-wrapper i {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.95rem;
        }

        /* Force the key icon to stay left */
        .input-wrapper i.fa-key {
            left: 16px;
        }

        /* FORCE THE EYE ICON TO THE FAR RIGHT */
        .input-wrapper i.eye-icon {
            right: 16px;
            left: auto; /* Overrides any inherited left positioning */
            cursor: pointer;
            z-index: 10; /* Ensures it sits above the input for clean clicking */
        }

        .input-wrapper i.eye-icon:hover {
            color: var(--primary-red);
        }

        /* Make sure your input has right padding so text doesn't hide behind the eye */
        .portal-input {
            width: 100%;
            padding: 14px 45px 14px 45px; /* Left padding 45px, Right padding 45px */
            border: 1.5px solid #e2e8f0;
            background-color: var(--input-bg);
            border-radius: 12px;
            outline: none;
            transition: all 0.2s ease;
        }
    </style>
@stop

@php( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') )
@php( $register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register') )
@php( $password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset') )

@if (config('adminlte.use_route_url', false))
    @php( $login_url = $login_url ? route($login_url) : '' )
    @php( $register_url = $register_url ? route($register_url) : '' )
    @php( $password_reset_url = $password_reset_url ? route($password_reset_url) : '' )
@else
    @php( $login_url = $login_url ? url($login_url) : '' )
    @php( $register_url = $register_url ? url($register_url) : '' )
    @php( $password_reset_url = $password_reset_url ? url($password_reset_url) : '' )
@endif

@section('auth_header', __('adminlte::adminlte.login_message'))


    <div class="portal-container">
        <div class="brand-side">
            <img src="{{ asset(config('adminlte.logo_img')) }}"
                         alt="{{ config('adminlte.logo_img_alt') }}" style="height: 80%; width: 100%">
            <h1 class="brand-title">B-<span>FORMS</span></h1>
            <p class="brand-text">BEVI Beauty Elements Ventures Inc. Streamlining documentation, Online forms approval, and Authorization tasks securely in one central operational hub.</p>
        </div>

        <div class="form-side">
            <div class="form-header">
                <h2>Welcome!</h2>
                <p>Sign In your credentials to access the system or Login using your Company GMail below.</p>
            </div>

            <form action="{{ $login_url }}" method="post">
            @csrf
                <div class="field-group">
                    <label class="field-label">Email</label>
                    <div class="input-wrapper">
                        <input type="email" name="email" value="{{ old('email') }}" class="portal-input @error('email') is-invalid @enderror" placeholder="Enter your email" required autocomplete="email">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                </div>
                <div class="field-group">
                    <label class="field-label">Password</label>
                    <div class="input-wrapper" style="position: relative;">
                        <input type="password" name="password" id="access_key_field" class="portal-input @error('password') is-invalid @enderror" placeholder="Enter your password" required>
                        <i class="fa-solid fa-key"></i>
                        <i class="fa-solid fa-eye view-trigger eye-icon" id="mask_toggle"></i>
                    </div>
                </div>
                @error('email')
                    <span class="field-label text-danger" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

                <select name="type" class="form-control @error('type') is-invalid @enderror" hidden>
                    <option value="live" selected>LIVE SERVER</option>
                    <option value="test">TEST SERVER</option>
                </select>

                <div class="utility-row">
                    <label class="remember-toggle">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        {{ __('adminlte::adminlte.remember_me') }}
                    </label>
                </div>

                <button type="submit" class="action-submit mb-3">
                    <span class="fas fa-sign-in-alt"></span>
                    {{ __('adminlte::adminlte.sign_in') }}
                </button>

                @if (config('adminlte.gmail_login', false))
                    <a href="{{ route('google.login') }}" class="btn btn-primary btn-block">
                        <i class="fab fa-google"></i> Login with Google
                    </a>
                @endif
                
            </form>
           
        </div>
    </div>
