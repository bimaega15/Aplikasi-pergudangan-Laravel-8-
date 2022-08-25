@extends('layout.admin.index')

@section('title')
Stok gudang page
@endsection

@section('content')
@push('css')
<link href="{{ asset('backend/xhtml') }}/css/style.css" rel="stylesheet">
<link href="/css/app.css" rel="stylesheet">
<style>
    .photoviewer-modal {
        background-color: transparent;
        border: none;
        border-radius: 0;
        box-shadow: 0 0 6px 2px rgba(0, 0, 0, .3);
    }

    .photoviewer-header .photoviewer-toolbar {
        background-color: rgba(0, 0, 0, .5);
    }

    .photoviewer-stage {
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-color: rgba(0, 0, 0, .85);
        border: none;
    }

    .photoviewer-footer .photoviewer-toolbar {
        background-color: rgba(0, 0, 0, .5);
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }

    .photoviewer-header,
    .photoviewer-footer {
        border-radius: 0;
        pointer-events: none;
    }

    .photoviewer-title {
        color: #ccc;
    }

    .photoviewer-button {
        color: #ccc;
        pointer-events: auto;
    }

    .photoviewer-header .photoviewer-button:hover,
    .photoviewer-footer .photoviewer-button:hover {
        color: white;
    }
</style>
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
            {{ Breadcrumbs::render('stockStore') }}
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Stok gudang</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">

                            @can('user-admin')
                            <a href="#" class="btn-add btn btn-primary shadow mr-1" data-toggle="modal"
                                data-target="#modalForm">
                                <i class="fas fa-plus"></i> Tambah data
                            </a>
                            <a href="#" class="btn-add btn btn-success shadow mr-1" data-toggle="modal"
                                data-target="#modalImport">
                                <i class="fas fa-file-excel"></i> Import data
                            </a>
                            @endcan

                        </div>
                        <div class="table-responsive">
                            <table id="dataTable" class="display min-w850">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama lokasi</th>
                                        <th>Kode barang</th>
                                        <th>Nama barang</th>
                                        <th>Tipe</th>
                                        <th>Stock</th>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormLabel">Form Stok gudang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-valide form-submit" action="{{ route('admin.stockStore.store') }}" method="post"
                novalidate="novalidate">
                @csrf
                <input type="hidden" name="page">
                <input type="hidden" name="id">
                <div class="modal-body">
                    <div id="error-form-submit"></div>
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="form-group">
                                <label>
                                    Lokasi barang
                                </label>
                                <p class="text-info m-0 p-0" id="set_location_id"></p>
                                <input type="hidden" name="old_location_id">
                                <select class="form-control select2-location" id="location_id" name="location_id">
                                    <option value="">-- Lokasi Barang --</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>
                                    Nama barang
                                </label>
                                <p class="text-info m-0 p-0" id="set_item_id"></p>
                                <input type="hidden" name="old_item_id">
                                <select class="form-control select2-item" id="item_id" name="item_id">
                                    <option value="">-- Nama Barang --</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>
                                    Jenis tipe
                                </label>
                                <p class="text-info m-0 p-0" id="set_unite_type_id"></p>
                                <input type="hidden" name="old_unite_type_id">
                                <select class="form-control select2-uniteType" id="unite_type_id" name="unite_type_id">
                                    <option value="">-- Jenis tipe --</option>
                                </select>
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

<div class="modal fade" id="modalImport" tabindex="-1" aria-labelledby="modalImportLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalImportLabel">Form Import</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-valide form-submit-import" action="{{ route('admin.stockStore.import') }}" method="post"
                novalidate="novalidate" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div id="error-form-submit-import"></div>
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="form-group">
                                <label>Import
                                </label>
                                <input type="file" class="form-control" id="import" name="import">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-danger btn-sm" data-dismiss="modal">
                        <i class="fas fa-window-close"></i> Close
                    </button>

                    <button type="submit" class="btn btn-primary btn-sm btn-import">
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
                ajax: {
                    url: '{{route("admin.stockStore.index")}}'
                },
                processing: true,
                serverSide: true,
                columns: [
                    {data: 'DT_RowIndex', name: 'no'},
                    {data: 'name_location', name: 'name_location'},
                    {data: 'code_item', name: 'code_item'},
                    {data: 'name_item', name: 'name_item'},
                    {data: 'name_unite_type', name: 'name_unite_type'},
                    {data: 'store_stock_store', name: 'store_stock_store'},
                    {data: 'picture_item', name: 'picture_item'},
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
           
                    $('input[name="id"]').val(result.id_stock_store); 
                    $('#set_location_id').html(result.name_location); 
                    $('#set_item_id').html(result.code_item + ' ' + result.name_item); 
                    $('#set_unite_type_id').html(result.name_unite_type); 

                    $('input[name="old_location_id"]').val(result.id_location); 
                    $('input[name="old_item_id"]').val(result.id_item); 
                    $('input[name="old_unite_type_id"]').val(result.id_unite_type);

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
            $('select[name="location_id"] option').val('').trigger('change');
            $('select[name="item_id"] option').val('').trigger('change');
            $('select[name="unite_type_id"] option').val('').trigger('change');
        }

        function resetFormImport() {
            $('#error-form-submit-import').html('');
            $('.form-submit-import').trigger("reset");
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

        $(document).on('click', '.btn-import', function(e) {
            e.preventDefault();
            var form = $('.form-submit-import')[0];
            var data = new FormData(form);
            var action = $('.form-submit-import').attr('action');
    
            $.ajax({
                url: action,
                type: "POST",
                data: data,
                enctype: 'multipart/form-data',
                processData: false, // Important!
                contentType: false,
                cache: false,
                dataType: 'json',
                beforeSend:function(data){
                    $('.loading-data').removeClass('d-none');
                },
                success: function(data) {
                    if (data.status == 200) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Successfully',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        })

                        $('#modalImport').modal('hide');
                        table.ajax.reload();
                        resetFormImport();
                    }

                    if (data.status == 400) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        })

                        $('#modalImport').modal('hide');
                        table.ajax.reload();
                    }
                },
                error: function(xhr) {
                    let output = '';
                        $('#error-form-submit-import').html('');
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
                        $('#error-form-submit-import').html(output);
                },
                complete:function(){
                    $('.loading-data').addClass('d-none');
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

        $('.select2-location').select2({
            theme: "bootstrap",
            ajax: {
                url: '{{ route("admin.stockStore.getLocation") }}',
                type: 'get',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    var query = {
                        search: params.term,
                        page: params.page || 1
                    }
                    return query;
                },
                processResults: function (data, params) {
                    const limit = 15;
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: (params.page * limit) < data.total_count
                        }
                    };
                },
                cache: true,
            },
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });

        $('.select2-item').select2({
            theme: "bootstrap",
            ajax: {
                url: '{{ route("admin.stockStore.getItem") }}',
                type: 'get',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    var query = {
                        search: params.term,
                        page: params.page || 1
                    }
                    return query;
                },
                processResults: function (data, params) {
                    const limit = 15;
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: (params.page * limit) < data.total_count
                        }
                    };
                },
                cache: true,
            },
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });

        $('.select2-uniteType').select2({
            theme: "bootstrap",
            ajax: {
                url: '{{ route("admin.stockStore.getUniteType") }}',
                type: 'get',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    var query = {
                        search: params.term,
                        page: params.page || 1
                    }
                    return query;
                },
                processResults: function (data, params) {
                    const limit = 15;
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: (params.page * limit) < data.total_count
                        }
                    };
                },
                cache: true,
            },
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });

        function formatRepo (repo) {
            if (repo.loading) {
                return repo.text;
            }

            var $container = $(
                "<div class='select2-result-repository clearfix'>" +
                "<div class='select2-result-repository__meta'>" +
                    "<div class='select2-result-repository__title'></div>" +
                    "</div>" +
                "</div>" +
                "</div>"
            );

            $container.find(".select2-result-repository__title").text(repo.text);
            return $container;
        }

        function formatRepoSelection (repo) {
            return repo.text;
        }

         // initialize manually with a list of links
         $(document).on('click','[data-gallery="photoviewer"]',function (e) {
            e.preventDefault();
                var items = [],
                options = {
                    index: $(this).index(),
                };

                $('[data-gallery="photoviewer"]').each(function () {
                    items.push({
                        src: $(this).attr('href'),
                        title: $(this).attr('data-title')
                    });
                });

                new PhotoViewer(items, options);
            });
    })
</script>
@endpush
@endsection