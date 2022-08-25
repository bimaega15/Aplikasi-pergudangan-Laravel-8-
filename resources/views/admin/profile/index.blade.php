@extends('layout.admin.index')

@section('title')
My profile page
@endsection

@section('content')
<!--**********************************
            Content body start
        ***********************************-->
<div class="content-body">
    <div class="container-fluid">
        <div class="page-titles">
            {{ Breadcrumbs::render('profile') }}
        </div>
        <!-- row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="profile card card-body px-3 pt-3 pb-0">
                    <div class="profile-head">
                        <div class="photo-content">
                            <div class="cover-photo"></div>
                        </div>
                        <div class="profile-info">
                            <div class="profile-photo">
                                <?php 
                                $gambarProfile = asset('image/users/default.png');
                                if($profile->picture_profile != null):
                                    if(file_exists(public_path().'/image/users/'.$profile->picture_profile)):
                                        $gambarProfile = asset('image/users/'.$profile->picture_profile);
                                    endif;
                                endif;
                                ?>
                                <div id="gambar_profile">
                                    <img src="{{ $gambarProfile }}" class="img-fluid rounded-circle img-profile"
                                        alt="Gambar profile">
                                </div>
                            </div>
                            <div class="profile-details">
                                <div class="profile-name px-3 pt-2">
                                    <h4 class="text-primary mb-0">{{ $users->name }}</h4>
                                    <p>{{ $users->role }}</p>
                                </div>
                                <div class="profile-email px-2 pt-2">
                                    <h4 class="text-muted mb-0">{{ $users->email }}</h4>
                                    <p>Email</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <div class="profile-tab">
                            @if ($profile->status_profile == 'not complete')
                            <small class="text-info"><i class="flaticon-381-edit-1"></i> Lengkapi profile anda:
                                *</small>
                            @endif
                            <div class="custom-tab-1">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item"><a href="#about-me" data-toggle="tab"
                                            class="nav-link active show">About
                                            Me</a>
                                    </li>
                                    <li class="nav-item"><a href="#profile-settings" data-toggle="tab"
                                            class="nav-link">Setting</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div id="about-me" class="tab-pane fade active show">
                                        <div class="profile-personal-info mt-3">
                                            <h4 class="text-primary mb-4">Personal Information</h4>
                                            <div class="row mb-2">
                                                <div class="col-sm-3 col-5">
                                                    <h5 class="f-w-500">Nama <span class="pull-right">:</span>
                                                    </h5>
                                                </div>
                                                <div class="col-sm-9 col-7"><span>{{ $users->name }}</span>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-sm-3 col-5">
                                                    <h5 class="f-w-500">Email <span class="pull-right">:</span>
                                                    </h5>
                                                </div>
                                                <div class="col-sm-9 col-7"><span>{{ $users->email }}</span>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-sm-3 col-5">
                                                    <h5 class="f-w-500">Level <span class="pull-right">:</span>
                                                    </h5>
                                                </div>
                                                <div class="col-sm-9 col-7"><span>{{ $users->role }}</span>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-sm-3 col-5">
                                                    <h5 class="f-w-500">Jenis kelamin <span class="pull-right">:</span>
                                                    </h5>
                                                </div>
                                                <div class="col-sm-9 col-7"><span>{{ $profile->gender_profile }}</span>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-sm-3 col-5">
                                                    <h5 class="f-w-500">Telepon <span class="pull-right">:</span></h5>
                                                </div>
                                                <div class="col-sm-9 col-7"><span>{{ $profile->telephone_profile
                                                        }}</span>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-sm-3 col-5">
                                                    <h5 class="f-w-500">Alamat <span class="pull-right">:</span></h5>
                                                </div>
                                                <div class="col-sm-9 col-7"><span>{{ $profile->address_profile }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="profile-settings" class="tab-pane fade">
                                        <div class="pt-3">
                                            <div class="settings-form">
                                                <div id="error_form"></div>
                                                <h4 class="text-primary">Setting profile</h4>
                                                <form method="post" action="{{ route('admin.profile.store') }}"
                                                    id="form-submit">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $users->id }}">
                                                    <input type="hidden" name="password_old"
                                                        value="{{ $users->password }}">
                                                    <div class="form-row">
                                                        <div class="form-group col-md-12">
                                                            <label>Email</label>
                                                            <input type="email" placeholder="Email" class="form-control"
                                                                name="email" value="{{ $users->email }}">
                                                        </div>
                                                    </div>
                                                    <div class="form-row">
                                                        <div class="form-group col-md-6">
                                                            <label>Password</label>
                                                            <input type="password" placeholder="Password"
                                                                class="form-control" name="password">
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label>Confirm Password</label>
                                                            <input type="password" placeholder="Confirm Password"
                                                                class="form-control" name="password_confirmation">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Nama</label>
                                                        <input type="text" placeholder="Nama profile"
                                                            class="form-control" name="name_profile"
                                                            value="{{ $users->name }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Jenis kelamin</label> <br>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="gender_profile" id="gender_profile1" value="L" {{
                                                                $profile->gender_profile == 'L' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="gender_profile1">
                                                                Laki-laki
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="gender_profile" id="gender_profile2" value="P" {{
                                                                $profile->gender_profile == 'P' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="gender_profile2">
                                                                Perempuan
                                                            </label>
                                                        </div>
                                                    </div>

                                            </div>
                                            <div class="form-group">
                                                <label>Telepon</label>
                                                <input type="text" placeholder="Telephone" class="form-control"
                                                    name="telephone_profile" value="{{ $profile->telephone_profile }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Alamat</label>
                                                <input type="text" placeholder="Alamat" class="form-control"
                                                    name="address_profile" value="{{ $profile->address_profile }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Gambar</label>
                                                <input type="file" class="form-control" name="picture_profile">
                                            </div>

                                            <button class="btn btn-danger" type="reset"><i class="fas fa-undo"></i>
                                                Reset</button>
                                            <button class="btn btn-primary btn-submit" type="submit"><i
                                                    class="fas fa-paper-plane"></i> Submit</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!--**********************************
            Content body end
        ***********************************-->
@endsection
@push('js')
<script>
    $(document).ready(function(){
        function resetForm() {
            $('#error_form').html('');
            // $('#form-submit').trigger("reset");
        }

        $(document).on('click','.btn-submit',function(e){
            e.preventDefault();
            var form = $('#form-submit')[0];
            var data = new FormData(form);
            var action = $('#form-submit').attr('action');
            
            $.ajax({
                url: action,
                type: "POST",
                data: data,
                enctype: 'multipart/form-data',
                processData: false, // Important!
                contentType: false,
                cache: false,
                dataType: 'json',
                success: function(data) {
                    const {result} = data;
                    if (data.status == 200) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Successfully',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        })
                        resetForm();
                        let urlAction = "{{ url()->current() }}";
                        let urlLoad = urlAction + ' #gambar_profile > *';
                        $('#gambar_profile').load(urlLoad);
                    }

                    if (data.status == 400) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        })

                        resetForm();
                    }
                },
                error: function(xhr) {
                    let output = '';
                    $('#error_form').html('');
                    $.each(xhr.responseJSON.result, function(key,value) {
                        output += `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Fail! </strong> ${value}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        `;
                    }); 
                    $('#error_form').html(output);
                }
            });
        })

    })
</script>
@endpush