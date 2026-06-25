@extends('layouts.guest')

@section('title', 'Login')
@section('header', 'Masuk ke Akun')
@section('subheader', 'Masukkan email dan password Anda')

@section('content')
    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf
        
        <!-- Email -->
        <div class="form-group">
            <label for="email" class="form-label">
                <i class="fas fa-envelope"></i> Email
            </label>
            <input type="email" 
                   name="email" 
                   id="email" 
                   value="{{ old('email') }}" 
                   required 
                   autofocus
                   class="auth-input"
                   placeholder="contoh@email.com">
            @error('email')
                <p class="auth-error">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </p>
            @enderror
        </div>
        
        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">
                <i class="fas fa-lock"></i> Password
            </label>
            <input type="password" 
                   name="password" 
                   id="password" 
                   required
                   class="auth-input"
                   placeholder="••••••••">
            @error('password')
                <p class="auth-error">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </p>
            @enderror
        </div>
        
        <!-- Remember Me & Forgot -->
        <div class="form-options">
            <label class="checkbox-label">
                <input type="checkbox" name="remember" class="auth-checkbox">
                <span class="checkbox-text">Ingat saya</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="auth-link">
                    Lupa password?
                </a>
            @endif
        </div>
        
        <!-- Submit -->
        <button type="submit" class="auth-btn">
            <i class="fas fa-sign-in-alt"></i> Masuk
        </button>
        
        <!-- Register Link -->
        <p class="auth-footer-text">
            Belum punya akun? 
            <a href="{{ route('register') }}" class="auth-link">Daftar sekarang</a>
        </p>
    </form>
@endsection