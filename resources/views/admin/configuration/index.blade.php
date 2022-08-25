@extends('layout.admin.index')

@section('title')
Konfigurasi page
@endsection

@section('content')
@push('css')
<link href="{{ asset('backend/xhtml') }}/css/style.css" rel="stylesheet">
<link href="/css/app.css" rel="stylesheet">
@endpush
<!--**********************************
            Content body start
        ***********************************-->
<img src="{{ asset('image/konfigurasi/loading.svg') }}" alt="Loading" width="100px;"
    style="position: fixed; left: 50%; top:15%; z-index:9999;" class="d-none loading-data">
<div class="content-body">
    <!-- row -->
    <div class="container-fluid">
        <div class="page-titles">
            {{ Breadcrumbs::render('configuration') }}
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('session')
                    <div class="card-header">
                        <h4 class="card-title">Konfigurasi</h4>
                    </div>
                    <div class="card-body">
                        <form class="form-valide form-submit" action="{{ route('admin.configuration.store') }}"
                            method="post" novalidate="novalidate" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="page" value="add">
                            <input type="hidden" name="id">
                            <div id="error-form-submit"></div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label for="name_configuration">Nama
                                            aplikasi
                                        </label>
                                        <input type="text" class="form-control" placeholder="Nama aplikasi" value=""
                                            name="name_configuration">
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label for="stock_store_id">Alamat
                                        </label>
                                        <input type="text" class="form-control" placeholder="Alamat"
                                            name="address_configuration">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="form-group">
                                        <label for="picture_configuration">Logo aplikasi
                                        </label>
                                        <input type="file" class="form-control" name="picture_configuration">
                                    </div>
                                    <div id="load_picture_configuration"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="form-group">
                                        <label for="description_configuration">Deskripsi aplikasi
                                        </label>
                                        <input type="text" class="form-control" name="description_configuration"
                                            placeholder="Deskripsi aplikasi">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label for="telephone_configuration">Telepon
                                        </label>
                                        <input type="number" class="form-control" name="telephone_configuration"
                                            placeholder="Deskripsi aplikasi">
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label for="email_configuration">Email
                                        </label>
                                        <input type="text" class="form-control" name="email_configuration"
                                            placeholder="Email">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="form-group">
                                        <label for="created_by_configuration">Created by
                                        </label>
                                        <input type="text" class="form-control" name="created_by_configuration"
                                            placeholder="Pembuat aplikasi">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <button type="reset" class="btn btn-danger btn-sm" data-dismiss="modal">
                                            <i class="fas fa-window-close"></i> Close
                                        </button>
                                        <button type="submit" class="btn btn-primary btn-sm btn-submit">
                                            <i class="fas fa-paper-plane"></i> Submit
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
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
        loadData();
        function loadData()
        {
            $.ajax({
                url: '{{ route("admin.configuration.index") }}',
                type: "get",
                dataType: 'json',
                success: function(data) {
                    let asset = "{{ asset('image/konfigurasi/') }}";
                    const {
                        result
                    } = data;
                    if(data.status == 200){
                        $('input[name="page"]').val('edit');
                        $('input[name="id"]').val(result.data.id);
                        $('input[name="name_configuration"]').val(result.data.name_configuration);
                        $('input[name="address_configuration"]').val(result.data.address_configuration);
                        $('#load_picture_configuration').html(`
                            <img src="${asset}/${result.data.picture_configuration}" width="25%;" class="img-thumbnail"></img>
                        `);
                        $('input[name="description_configuration"]').val(result.data.description_configuration);
                        $('input[name="telephone_configuration"]').val(result.data.telephone_configuration);
                        $('input[name="email_configuration"]').val(result.data.email_configuration);
                        $('input[name="created_by_configuration"]').val(result.data.created_by_configuration);
                    } else{
                        $('input[name="page"]').val('add');

                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        function resetForm() {
            $('#error-form-submit').html('');
            $('.form-submit').trigger("reset");
            $('#load_picture_configuration').html(``);
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
                        resetForm();
                        loadData();
                    }

                    if (data.status == 400) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        })
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
    })
</script>
@endpush