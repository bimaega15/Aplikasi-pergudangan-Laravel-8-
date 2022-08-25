@extends('layout.admin.index')

@section('title')
Users page
@endsection

@section('content')
@push('css')
<link href="{{ asset('backend/xhtml') }}/css/style.css" rel="stylesheet">
@endpush
<!--**********************************
            Content body start
        ***********************************-->
<div class="content-body">
    <!-- row -->
    <div class="container-fluid">
        <div class="page-titles">
            {{ Breadcrumbs::render('users') }}
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Profile Datatable</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <a href="#" class="btn-add btn btn-primary shadow  mr-1" data-toggle="modal"
                                data-target="#modalForm">
                                <i class="fas fa-plus"></i> Tambah data
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table id="dataTable" class="display min-w850">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Email</th>
                                        <th>Nama</th>
                                        <th>J.K</th>
                                        <th>Telepon</th>
                                        <th>Alamat</th>
                                        <th>Akses</th>
                                        <th>Gambar</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
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

<!-- Modal -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormLabel">Form User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-valide form-submit" action="{{ route('admin.users.store') }}" method="post"
                novalidate="novalidate">
                @csrf
                <input type="hidden" name="page">
                <input type="hidden" name="id">
                <input type="hidden" name="password_old">
                <div class="modal-body">
                    <div id="error-form-submit"></div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="name_profile">Nama
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="name_profile" name="name_profile"
                                        placeholder="Nama profile..">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="gender_profile">Jenis kelamin <span
                                        class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender_profile"
                                            id="gender_profile1" value="L">
                                        <label class="form-check-label" for="gender_profile1">
                                            Laki-laki
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender_profile"
                                            id="gender_profile2" value="P">
                                        <label class="form-check-label" for="gender_profile2">
                                            Perempuan
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="telephone_profile">Telephone
                                </label>
                                <div class="col-lg-6">
                                    <input type="number" class="form-control" id="telephone_profile"
                                        name="telephone_profile" placeholder="Telephone profile..">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="address_profile">Alamat
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="address_profile" name="address_profile"
                                        placeholder="Alamat..">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="picture_profile">Gambar
                                </label>
                                <div class="col-lg-6">
                                    <input type="file" class="form-control" id="picture_profile" name="picture_profile">
                                    <div id="load_picture_profile"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="email">Email
                                </label>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="email" name="email" placeholder="Email">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="password">Password
                                </label>
                                <div class="col-lg-6">
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Password">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="password_confirmation">Confirm Password
                                </label>
                                <div class="col-lg-6">
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" placeholder="Password confirmation">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="role">Role
                                </label>
                                <div class="col-lg-6">
                                    <div class="dropdown bootstrap-select form-control default-select">
                                        <select class="form-control default-select" id="role" name="role"
                                            tabindex="-98">
                                            <option value="">-- Level --</option>
                                            <option value="admin">Admin</option>
                                            <option value="user">User</option>
                                            <option value="guest">Guest</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-danger btn-sm" data-dismiss="modal">
                        <i class="fas fa-window-close"></i> Close
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm btn-submit">
                        <i class="fas fa-paper-plane"></i> Submit
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@push('js')
<script>
    $(document).ready(function(e){
            var table = $('#dataTable').DataTable({
                ajax: '{{route("admin.users.index")}}',
                columns: [
                    {data: 'DT_RowIndex', name: 'no'},
                    {data: 'email', name: 'email'},
                    {data: 'name_profile', name: 'name_profile'},
                    {data: 'gender_profile', name: 'gender_profile'},
                    {data: 'telephone_profile', name: 'telephone_profile'},
                    {data: 'address_profile', name: 'address_profile'},
                    {data: 'role', name: 'role'},
                    {data: 'picture_profile', name: 'picture_profile', orderable: false, searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });
            $(document).on('click', '.btn-add', function(e) {
                e.preventDefault();
                $('.form-submit')[0].reset();
                $('input[name="page"]').val('add');
                resetForm();
            })

        $(document).on('click', '.btn-edit', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const action = $(this).attr('href');
            const root = "{{asset('/')}}";
            $.ajax({
                url: action,
                method: 'get',
                dataType: 'json',
                success: function(data) {
                    const {
                        result
                    } = data;
                    console.log(result);

                    $('input[name="id"]').val(result.users_id);
                    $('input[name="email"]').val(result.email);
                    $('input[name="password_old"]').val(result.password);
                    $('select[name="role"]').val(result.role).trigger('change');

                    $('input[name="name_profile"]').val(result.name_profile);
                    $('input[name="gender_profile"][value="'+result.gender_profile+'"]').attr('checked',true);
                    $('input[name="telephone_profile"]').val(result.telephone_profile);
                    $('input[name="address_profile"]').val(result.address_profile);
                    $('#load_picture_profile').html(`
                    <img class="img-thumbnail" class="w-25" src="${root}image/users/${result.picture_profile}"></img>
                    `);

                    $('#modalForm').modal().show();
                    $('input[name="page"]').val('edit');
                },
                error: function(x, t, m) {
                    console.log(x.responseText);
                }
            })
        })

        function resetForm() {
            $('#error-form-submit').html('');
            $('.form-submit').trigger("reset");
            $('input[name="gender_profile"]').attr('checked',false);
            $('select[name="role"] option').attr('checked',false);
        }
        $(document).on('click', '.btn-submit', function(e) {
            e.preventDefault();
            var form = $('.form-submit')[0];
            var data = new FormData(form);
            var action = $('.form-submit').attr('action');
    
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
                    
                    if (data.status == 200) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Successfully',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        })

                        $('#modalForm').modal('hide');
                        table.ajax.reload();
                        resetForm();
                    }

                    if (data.status == 400) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        })

                        $('#modalForm').modal('hide');
                        table.ajax.reload();
                    }
                },
                error: function(xhr) {
                    console.log(xhr);
                    let output = '';
                        $('#error-form-submit').html('');
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
                        $('#error-form-submit').html(output);
                }
            });
        })
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            const action = $(this).closest("form").attr('action');
            Swal.fire({
                title: 'Deleted',
                text: "Yakin ingin menghapus item ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: action,
                        dataType: 'json',
                        type: 'post',
                        method: 'DELETE',
                        success: function(data) {                            
                            if (data.status == 200) {
                                Swal.fire(
                                    'Deleted!',
                                    data.message,
                                    'success'
                                );
                                table.ajax.reload();

                            } else {
                                Swal.fire(
                                    'Deleted!',
                                    data.message,
                                    'error'
                                )
                            }

                        },
                        error: function(x, t, m) {
                            console.log(x.responseText);
                        }
                    })
                }
            })
        })
    })
</script>
@endpush
@endsection