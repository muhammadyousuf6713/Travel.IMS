@extends('layouts.user_type.guest')

@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .main-container {
            min-height: 100vh;
            width: 100%;
            background: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.85)),
                        url('{{ (!empty($orgSetting->login_bg)) ? asset($orgSetting->login_bg) : "https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" }}') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            flex-direction: column;
            position: relative;
            padding: 20px;
            padding-bottom: 60px;
        }

        .top-left-header {
            padding: 10px;
            z-index: 10;
        }

        @media (min-width: 768px) {
            .top-left-header {
                padding: 20px;
            }
        }

        .content-area {
            display: flex;
            flex-grow: 1;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            z-index: 10;
        }

        .corporate-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            color: #1e293b;
        }

        .auth-header-title {
            text-align: center;
            margin-bottom: 35px;
        }

        .auth-header-title h2 {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 10px;
            font-weight: 700;
            color: #64748b;
        }

        .auth-header-title .login-heading {
            font-size: 26px;
            font-weight: 600;
            color: #0f172a;
            margin: 0;
        }

        .form-label {
            font-size: 13px;
            font-weight: 500;
            color: #475569;
            margin-bottom: 6px;
            display: block;
        }

        .form-control {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #0f172a !important;
            border-radius: 8px !important;
            padding: 12px 16px !important;
            font-size: 14px;
            transition: all 0.2s ease;
            width: 100%;
        }

        .form-control::placeholder {
            color: #94a3b8 !important;
        }

        .form-control:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
            outline: none;
        }

        .btn-login {
            background: #0f172a !important;
            border: none !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            font-size: 14px;
            padding: 14px !important;
            border-radius: 8px !important;
            margin-top: 20px !important;
            width: 100%;
            transition: background 0.2s ease !important;
        }

        .btn-login:hover {
            background: #1e293b !important;
        }

        .link-style {
            color: #3b82f6 !important;
            font-size: 13px !important;
            font-weight: 500;
            text-decoration: none !important;
            transition: color 0.2s;
        }

        .link-style:hover {
            color: #2563eb !important;
            text-decoration: underline !important;
        }

        .footer-copyright {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 15px 20px;
            color: rgba(255, 255, 255, 0.6);
            font-size: 12px;
            text-align: center;
            letter-spacing: 0.5px;
            z-index: 20;
        }
        
        .footer-copyright a {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            text-decoration: none;
        }

        .footer-copyright a:hover {
            text-decoration: underline;
        }

        /* Executive Slide Switch ("Slide Radio") */
        .remember-switch-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f8fafc;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            cursor: pointer;
            user-select: none;
            transition: all 0.2s ease;
            margin-bottom: 16px;
        }

        .remember-switch-container:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .remember-label {
            font-size: 13px;
            font-weight: 500;
            color: #334155;
            margin: 0;
            cursor: pointer;
            user-select: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-label i {
            color: #64748b;
            font-size: 13px;
        }

        .slide-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
            margin: 0;
            cursor: pointer;
            flex-shrink: 0;
        }

        .slide-switch input {
            opacity: 0;
            width: 0;
            height: 0;
            position: absolute;
        }

        .slide-switch .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 24px;
        }

        .slide-switch .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: #ffffff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .slide-switch input:checked + .slider {
            background-color: #0f172a;
        }

        .slide-switch input:checked + .slider:before {
            transform: translateX(20px);
        }
    </style>

    <div class="main-container">
        <div class="top-left-header">
            <div class="logo-container" style="display: flex; align-items: center; gap: 12px;">
                @if(isset($orgSetting) && $orgSetting->logo)
                    <img src="{{ asset($orgSetting->logo) }}" alt="Logo" class="logo-icon" style="width: auto; max-height: 48px; background: #ffffff; padding: 6px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
                @else
                    <svg class="logo-icon" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 48px; height: 48px; background: #ffffff; padding: 6px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
                        <path d="M50 0L55 45L100 50L55 55L50 100L45 55L0 50L45 45L50 0Z" fill="#0f172a"/>
                    </svg>
                @endif
                <h3 class="mb-0 text-white" style="font-weight: 600; font-size: 18px; letter-spacing: 0.5px;">
                    {{ isset($orgSetting) && $orgSetting->name ? $orgSetting->name : 'Flugzeit Aviation' }}
                </h3>
            </div>
        </div>

        <div class="content-area">
            <div class="login-wrapper">
                <div class="corporate-card">
                    <div class="auth-header-title">
                        <h2>{{ isset($orgSetting) && $orgSetting->name ? strtoupper($orgSetting->name) : 'TRAVEL AGENCY INQUIRY MANAGEMENT' }}</h2>
                        <div class="login-heading">Sign in to your account</div>
                    </div>

                    <form method="POST" action="{{ url('session') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label" for="email">Username (Email)</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                placeholder="name@company.com"
                                required
                            >
                            @error('email')
                                <p class="text-danger text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="password">Password</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                placeholder="••••••••"
                                required
                            >
                            @error('password')
                                <p class="text-danger text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="remember-switch-container" onclick="document.getElementById('remember').click();">
                            <label class="remember-label" for="remember" onclick="event.stopPropagation();">
                                <i class="fa-solid fa-shield-halved"></i>
                                <span>Remember me on this device</span>
                            </label>
                            <label class="slide-switch" for="remember" onclick="event.stopPropagation();">
                                <input type="checkbox" id="remember" name="remember" value="1" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <button type="submit" class="btn btn-login">Sign in</button>
                        
                        <div class="text-center mt-4 pt-4" style="border-top: 1px solid #e2e8f0;">
                            <span style="color: #64748b; font-size: 13px;">Need assistance?</span>
                            <span class="link-style ms-1" style="cursor: pointer;">Contact Administrator</span>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="footer-copyright">
            Copyright © {{ date('Y') }} <a href="https://nyrogentechnologies.com" target="_blank" rel="noopener noreferrer">Nyrogen Technologies</a> - Travel Management System
        </div>
    </div>
@endsection
