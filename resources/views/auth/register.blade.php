@extends('layouts.guest')

@section('title', 'Register')
@section('header', 'Daftar Akun')
@section('subheader', 'Buat akun baru untuk mulai menggunakan KosLife')

@section('content')
    <form method="POST" action="{{ route('register') }}" class="auth-form">
        @csrf
        
        <!-- Name -->
        <div class="form-group">
            <label for="name" class="form-label">
                <i class="fas fa-user"></i> Nama Lengkap
            </label>
            <input type="text" 
                   name="name" 
                   id="name" 
                   value="{{ old('name') }}" 
                   required 
                   autofocus
                   class="auth-input"
                   placeholder="Masukkan nama Anda">
            @error('name')
                <p class="auth-error">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </p>
            @enderror
        </div>
        
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
                   placeholder="Minimal 8 karakter">
            @error('password')
                <p class="auth-error">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </p>
            @enderror
        </div>
        
        <!-- Confirm Password -->
        <div class="form-group">
            <label for="password_confirmation" class="form-label">
                <i class="fas fa-check-circle"></i> Konfirmasi Password
            </label>
            <input type="password" 
                   name="password_confirmation" 
                   id="password_confirmation" 
                   required
                   class="auth-input"
                   placeholder="Ketik ulang password">
        </div>
        
        <!-- Submit -->
        <button type="submit" class="auth-btn">
            <i class="fas fa-user-plus"></i> Daftar
        </button>
        
        <!-- Login Link -->
        <p class="auth-footer-text">
            Sudah punya akun? 
            <a href="{{ route('login') }}" class="auth-link">Masuk sekarang</a>
        </p>
    </form>
@endsection