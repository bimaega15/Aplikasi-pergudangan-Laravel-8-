@extends('login')

@section('title')
Register page
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
                    <a href="{{ route('register') }}"><img
                            src="{{ asset('image/konfigurasi/'.$configuration->picture_configuration) }}" alt=""
                            height="150px;" style="border-radius: 5px;"></a>
                </div>
                <h4 class="text-center mb-4 text-white">Silahkan daftar akun</h4>
                <form action="{{ route('register') }}" method="post">
                    @csrf
                    <div class="form-group">
                        <label class="mb-1 text-white"><strong>Nama</strong></label>
                        <input type="text" class="form-control" placeholder="Nama" name="name"
                            value="{{ old('name') }}">
                        @error('name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="mb-1 text-white"><strong>Email</strong></label>
                        <input type="text" class="form-control" placeholder="hello@example.com" name="email"
                            value="{{ old('email') }}">
                        @error('email')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="mb-1 text-white"><strong>Password</strong></label>
                        <input type="password" class="form-control" value="" name="password" placeholder="Password">
                        @error('password')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="mb-1 text-white"><strong>Confirm Password</strong></label>
                        <input type="password" class="form-control" value="" name="password_confirmation"
                            placeholder="Confirm Password">
                        @error('password_confirmation')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn bg-white text-primary btn-block">Sign me
                            up</button>
                    </div>
                </form>
                <div class="new-account mt-3">
                    <p class="text-white">Already have an account? <a class="text-white"
                            href="{{ route('login') }}">Sign
                            in</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection