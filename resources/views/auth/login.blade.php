@extends('layouts.guest')

@section('title', 'Login Admin')

@section('content')
<div class="container py-5 mb-6 h-100 object-image">
  <div class="row d-flex justify-content-center align-items-center h-100">
    <div class="col-12 col-md-8 col-lg-6 col-xl-5">
      <div class="card shadow-2-strong" style="border-radius: 1rem;">
        <div class="card-body p-5">
          <h1 class="mb-5 text-danger fw-bold text-center">Sign Up</h1>

          @if ($errors->any())
            <div class="alert alert-danger">
              @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
              @endforeach
            </div>
          @endif

          <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <div class="form-outline mb-4">
              <label for="email" class="form-label">{{ __('Alamat Email') }}</label>
              <input id="email" type="email"
                     class="form-control @error('email') is-invalid @enderror"
                     name="email" value="{{ old('email') }}" required autofocus>
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-outline mb-4">
              <label for="password" class="form-label">{{ __('Password') }}</label>
              <input id="password" type="password"
                     class="form-control @error('password') is-invalid @enderror"
                     name="password" required>
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-check mb-4">
              <input class="form-check-input" type="checkbox" name="remember" id="remember"
                     {{ old('remember') ? 'checked' : '' }}>
              <label class="form-check-label" for="remember">
                {{ __('Ingat Saya') }}
              </label>
            </div>

            <div class="d-grid gap-1">
              <button class="btn btn-success btn-lg" type="submit">Masuk</button>
            </div>

            <hr class="my-4">
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection