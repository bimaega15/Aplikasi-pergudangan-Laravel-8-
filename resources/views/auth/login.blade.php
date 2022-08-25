@extends('login')

@section('title')
Login page
@endsection

@section('content')
<?php 
$configuration = CheckHelp::configuration();
?>
<div class="authincation-content">
    <div class="row no-gutters">
        <div class="col-xl-12">
            <div class="auth-form">
                <div class="text-center mb-3">
                    <a href="{{ url('/login') }}"><img
                            src="{{ asset('image/konfigurasi/'.$configuration->picture_configuration) }}"
                            alt="Gambar logo" height="150px;" style="border-radius: 5px;"></a>
                </div>
                <h4 class="text-center mb-4 text-white">Sign in your account</h4>
                <form action="{{ route('login') }}" method="post">
                    @csrf
                    <div class="form-group">
                        <label class="mb-1 text-white"><strong>Email</strong></label>
                        <input type="text" name="email" class="form-control" value="{{ old('email') }}"
                            placeholder="Email address...">
                        @error('email')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="mb-1 text-white"><strong>Password</strong></label>
                        <input type="password" class="form-control" value="" name="password" placeholder="Password...">
                        @error('password')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-row d-flex justify-content-between mt-4 mb-2">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox ml-1 text-white">
                                <input type="checkbox" class="custom-control-input" id="basic_checkbox_1"
                                    name="remember">
                                <label class="custom-control-label" for="basic_checkbox_1">Remember my
                                    preference</label>
                            </div>
                        </div>
                        {{-- <div class="form-group">
                            <a class="text-white" href="page-forgot-password.html">Forgot Password?</a>
                        </div> --}}
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn bg-white text-primary btn-block">Sign Me In</button>
                    </div>
                </form>
                <div class="new-account mt-3">
                    <p class="text-white">Don't have an account? <a class="text-white" href="{{ route('login') }}">Sign
                            up</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection