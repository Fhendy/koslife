<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ theme: localStorage.getItem('theme') || 'light' }" 
      :class="theme">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#4F46E5">

    <title>{{ config('app.name', 'KosLife') }} - @yield('title', 'Welcome')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ============================================
                   RESET & BASE
                   ============================================ */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        [x-cloak] { display: none !important; }
        
        body {
            font-family: 'Figtree', 'Inter', -apple-system, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* ============================================
                   BACKGROUND
                   ============================================ */
        .auth-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        
        .auth-bg::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
            animation: floatBG 20s ease-in-out infinite;
        }
        
        .auth-bg::after {
            content: '';
            position: absolute;
            bottom: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 70% 80%, rgba(255,255,255,0.05) 0%, transparent 50%);
            animation: floatBG 25s ease-in-out infinite reverse;
        }
        
        @keyframes floatBG {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(5%, -5%) rotate(2deg); }
            66% { transform: translate(-5%, 5%) rotate(-2deg); }
        }
        
        /* ============================================
                   FLOATING SHAPES
                   ============================================ */
        .floating-shapes {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 1;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            animation: floatShape 15s ease-in-out infinite;
        }
        .shape:nth-child(1) { width: 300px; height: 300px; top: -100px; right: -100px; animation-delay: 0s; }
        .shape:nth-child(2) { width: 200px; height: 200px; bottom: -50px; left: -50px; animation-delay: -5s; }
        .shape:nth-child(3) { width: 150px; height: 150px; top: 50%; left: 50%; transform: translate(-50%, -50%); animation-delay: -10s; }
        
        @keyframes floatShape {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, -30px) scale(1.2); }
        }
        
        /* ============================================
                   WRAPPER & CARD
                   ============================================ */
        .auth-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
        }
        
        .auth-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            transition: all 0.3s ease;
        }
        
        .dark .auth-card {
            background: rgba(17, 24, 39, 0.95);
            border-color: rgba(255,255,255,0.05);
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
        }
        
        .auth-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 30px 60px -15px rgba(0,0,0,0.3);
        }
        
        /* ============================================
                   LOGO
                   ============================================ */
        .auth-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            border-radius: 20px;
            color: #ffffff;
            font-size: 32px;
            font-weight: 800;
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
            transition: all 0.3s ease;
        }
        .auth-logo:hover {
            transform: scale(1.05) rotate(-3deg);
            box-shadow: 0 15px 35px -5px rgba(79, 70, 229, 0.5);
        }
        
        /* ============================================
                   FORM
                   ============================================ */
        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
        }
        .dark .form-label { color: #d1d5db; }
        .form-label i { margin-right: 4px; }
        
        /* ============================================
                   INPUT
                   ============================================ */
        .auth-input {
            width: 100%;
            padding: 12px 16px;
            font-size: 14px;
            font-family: inherit;
            border-radius: 12px;
            border: 1.5px solid #d1d5db;
            background-color: #ffffff;
            color: #1f2937;
            transition: all 0.2s ease;
            outline: none;
        }
        
        .dark .auth-input {
            background-color: #1f2937;
            border-color: #4b5563;
            color: #f3f4f6;
        }
        
        .auth-input:focus {
            border-color: #4F46E5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
        
        .auth-input::placeholder {
            color: #9ca3af;
        }
        .dark .auth-input::placeholder { color: #6b7280; }
        
        /* ============================================
                   ERROR
                   ============================================ */
        .auth-error {
            margin-top: 4px;
            font-size: 13px;
            color: #dc2626;
        }
        .dark .auth-error { color: #f87171; }
        .auth-error i { margin-right: 4px; }
        
        /* ============================================
                   OPTIONS
                   ============================================ */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 4px 0;
        }
        
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 14px;
            color: #4b5563;
        }
        .dark .checkbox-label { color: #9ca3af; }
        
        .auth-checkbox {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 1.5px solid #d1d5db;
            accent-color: #4F46E5;
            cursor: pointer;
        }
        .dark .auth-checkbox {
            border-color: #4b5563;
            background-color: #1f2937;
        }
        
        .checkbox-text {
            font-size: 14px;
            color: #4b5563;
        }
        .dark .checkbox-text { color: #9ca3af; }
        
        /* ============================================
                   BUTTON
                   ============================================ */
        .auth-btn {
            width: 100%;
            padding: 12px 16px;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            color: #ffffff;
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 4px;
        }
        .auth-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);
        }
        .auth-btn:active { transform: scale(0.98); }
        .auth-btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.5);
        }
        
        /* ============================================
                   LINKS
                   ============================================ */
        .auth-link {
            color: #4F46E5;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: color 0.2s ease;
        }
        .dark .auth-link { color: #818cf8; }
        .auth-link:hover { color: #4338ca; }
        .dark .auth-link:hover { color: #6366f1; }
        
        .auth-footer-text {
            text-align: center;
            font-size: 14px;
            color: #6b7280;
            margin-top: 4px;
        }
        .dark .auth-footer-text { color: #9ca3af; }
        
        /* ============================================
                   HEADER
                   ============================================ */
        .auth-header-title {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
        }
        .dark .auth-header-title { color: #ffffff; }
        
        .auth-header-subtitle {
            font-size: 14px;
            color: #6b7280;
            margin-top: 4px;
        }
        .dark .auth-header-subtitle { color: #9ca3af; }
        
        /* ============================================
                   THEME TOGGLE
                   ============================================ */
        .theme-toggle {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 50;
            padding: 10px;
            border-radius: 12px;
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            color: #4b5563;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .dark .theme-toggle {
            background: rgba(31, 41, 55, 0.8);
            color: #f3f4f6;
            border-color: rgba(255,255,255,0.05);
        }
        .theme-toggle:hover { transform: scale(1.05); }
        
        /* ============================================
                   UTILITY
                   ============================================ */
        .text-center { text-align: center; }
        .text-white { color: #ffffff; }
        .text-white\/70 { color: rgba(255, 255, 255, 0.7); }
        .text-white\/60 { color: rgba(255, 255, 255, 0.6); }
        .drop-shadow-lg { filter: drop-shadow(0 10px 8px rgb(0 0 0 / 0.04)) drop-shadow(0 4px 3px rgb(0 0 0 / 0.1)); }
        .font-bold { font-weight: 700; }
        .font-medium { font-weight: 500; }
        .text-sm { font-size: 14px; }
        .text-xs { font-size: 12px; }
        .text-2xl { font-size: 24px; }
        .mt-1 { margin-top: 4px; }
        .mt-4 { margin-top: 16px; }
        .mt-6 { margin-top: 24px; }
        .mt-8 { margin-top: 32px; }
        .mb-6 { margin-bottom: 24px; }
        .mb-8 { margin-bottom: 32px; }
        .mx-auto { margin-left: auto; margin-right: auto; }
        .inline-block { display: inline-block; }
        .inline-flex { display: inline-flex; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-center { justify-content: center; }
        .justify-between { justify-content: space-between; }
        .gap-2 { gap: 8px; }
        .w-full { width: 100%; }
        .max-w-md { max-width: 420px; }
        .transition-all { transition-property: all; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 300ms; }
        .duration-200 { transition-duration: 200ms; }
        .duration-300 { transition-duration: 300ms; }
        .cursor-pointer { cursor: pointer; }
        
        /* ============================================
                   RESPONSIVE
                   ============================================ */
        @media (max-width: 640px) {
            .auth-card { padding: 24px; margin: 0 8px; border-radius: 20px; }
            .auth-logo { width: 60px; height: 60px; font-size: 26px; }
            .auth-bg { padding: 8px; }
            .auth-header-title { font-size: 18px; }
            .auth-btn { font-size: 14px; padding: 10px 16px; }
            .auth-input { padding: 10px 14px; font-size: 13px; }
            .form-options { flex-direction: column; align-items: flex-start; gap: 8px; }
        }
    </style>
    
    @stack('styles')
</head>

<body class="auth-bg">
    
    <!-- Floating Shapes -->
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    
    <!-- Theme Toggle -->
    <button x-data="{ theme: localStorage.getItem('theme') || 'light' }"
            @click="
                theme = theme === 'light' ? 'dark' : 'light';
                document.documentElement.className = theme;
                localStorage.setItem('theme', theme);
            "
            class="theme-toggle"
            aria-label="Toggle theme">
        <i class="fas" 
           :class="theme === 'light' ? 'fa-moon' : 'fa-sun'"
           :style="{ color: theme === 'light' ? '#4B5563' : '#FBBF24' }"></i>
    </button>
    
    <!-- Main Content -->
    <div class="auth-wrapper">
        
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="{{ route('login') }}" class="inline-block">
                <div class="auth-logo mx-auto">K</div>
            </a>
            <h1 class="mt-4 text-2xl font-bold text-white drop-shadow-lg">
                {{ config('app.name', 'KosLife') }}
            </h1>
            <p class="text-white/70 text-sm mt-1 font-medium">
                @yield('subtitle', 'Dashboard Anak Kos')
            </p>
        </div>
        
        <!-- Card -->
        <div class="auth-card">
            
            <!-- Header -->
            <div class="text-center mb-6">
                <h2 class="auth-header-title">
                    @yield('header', 'Selamat Datang')
                </h2>
                <p class="auth-header-subtitle">
                    @yield('subheader', 'Silakan masuk untuk melanjutkan')
                </p>
            </div>
            
            <!-- Content -->
            @yield('content')
            
            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    &copy; {{ date('Y') }} <a href="{{ route('login') }}" class="auth-link">KosLife</a> 
                    - Dibuat dengan <i class="fas fa-heart text-red-500 text-xs"></i> untuk anak kos
                </p>
            </div>
        </div>
        
        <!-- Security Badge -->
        <div class="mt-6 text-center">
            <p class="text-white/60 text-sm">
                <i class="fas fa-shield-alt"></i> Data Anda aman dan terenkripsi
            </p>
        </div>
    </div>
    
    @stack('scripts')
    
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('app', {
                theme: localStorage.getItem('theme') || 'light'
            });
        });
        
        window.showToast = function(message, type = 'success') {
            console.log('[' + type + ']', message);
        };
        
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                setTimeout(() => window.showToast('{{ session('success') }}', 'success'), 500);
            @endif
            @if(session('error'))
                setTimeout(() => window.showToast('{{ session('error') }}', 'error'), 500);
            @endif
        });
    </script>
</body>
</html>