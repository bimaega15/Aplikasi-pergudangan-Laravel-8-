@extends('layout.admin.index')

@section('title')
Transaksi barang keluar page
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
            {{ Breadcrumbs::render('exitItem') }}
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('session')
                    <div class="card-header">
                        <h4 class="card-title">Transaksi Barang Keluar</h4>
                    </div>
                    <div class="card-body">
                        @can('user-admin')
                        <div class="mb-2">
                            <a href="{{ route('admin.exitItem.create') }}" class="btn-add btn btn-primary shadow mr-1">
                                <i class="fas fa-plus"></i> Tambah data
                            </a>
                            <a href="{{ route('admin.exitItem.editMultiple') }}"
                                class="btn-add btn btn-warning shadow mr-1">
                                <i class="fas fa-pencil"></i> Edit data
                            </a>
                        </div>
                        @endcan

                        <div class="table-responsive">
                            <table id="dataTable" class="display min-w850">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="check_id_all">
                                                <label class="form-check-label" for="check_id_all">
                                                </label>
                                            </div>
                                        </th>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Lokasi Barang</th>
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

<!-- Modal -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormLabel">Form Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-valide form-submit" action="{{ route('admin.exitItem.store') }}" method="post"
                novalidate="novalidate">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="page" value="edit">
                    <input type="hidden" name="id">
                    <div id="error-form-submit"></div>
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="stock_store_id">Data Stock Barang
                                </label>
                                <div class="col-lg-8">
                                    <p class="text-info mt-1 p-0" id="set_stock_store_id"></p>
                                    <input type="hidden" name="old_stock_store_id">
                                    {{-- <select class="form-control select2-stock-store" id="stock_store_id"
                                        name="stock_store_id">
                                        <option value="">-- Data Barang --</option>
                                    </select> --}}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="stock_exit_item">Jumlah stock
                                </label>
                                <div class="col-lg-8">
                                    <input type="number" class="form-control" id="stock_exit_item"
                                        name="stock_exit_item" placeholder="Jumlah stock..." value="">
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

<!--**********************************
            Content body end
        ***********************************-->
@push('js')
<script>
    $(document).ready(function(e){
        var table = $('#dataTable').DataTable({
            ajax: {
                url: '{{route("admin.exitItem.index")}}'
            },
            processing: true,
            serverSide: true,
            columns: [
                {data: 'check_id', name: 'check_id',  orderable: false, searchable: false},
                {data: 'DT_RowIndex', name: 'no'},
                {data: 'out_date_exit_item', name: 'out_date_exit_item'},
                {data: 'name_location', name: 'name_location'},
                {data: 'code_item', name: 'code_item'},
                {data: 'name_item', name: 'name_item'},
                {data: 'name_unite_type', name: 'name_unite_type'},
                {data: 'stock_exit_item', name: 'stock_exit_item'},
                {data: 'picture_item', name: 'picture_item'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });

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

        $('.select2-stock-store').select2({
            theme: "bootstrap",
            ajax: {
                url: '{{ route("admin.exitItem.create") }}',
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

                    $('#set_stock_store_id').html(result.name_location + ' ' + result.code_item+ ' ' +result.name_item+ ' ' + result.name_unite_type);
                    $('input[name="old_stock_store_id"]').val(result.id_stock_store);
                    $('input[name="stock_exit_item"]').val(result.stock_exit_item);
                    $('input[name="id"]').val(result.id);
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
                    console.log(data);
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

        $(document).on('click','#check_id_all', function(e){
            if($(this).is(':checked')){
                $('.check_id').prop('checked',true);
            } else {
                $('.check_id').prop('checked',false);
            }

            let value = [];
            let dataId = [];
            $.each($('.check_id'), function(i,v){
                value[i] = null;
                dataId[i] = $(this).data('id');
                if($(this).is(':checked')){
                    let getValue = $(this).val();
                    value[i] = getValue;
                }
            })

            $.ajax({
                url: '{{ route("admin.exitItem.checkedPostMultiple") }}',
                type: "POST",
                method: 'post',
                data: {
                    id: value,
                    dataId: dataId
                },
                dataType: 'json',
                success: function(data) {
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        })

        $(document).on('click','.check_id', function(){
            let check = $('.check_id').length;
            let checked = $('.check_id:checked').length;

            if(check == checked){
                $('#check_id_all').prop('checked',true);
            } else {
                $('#check_id_all').prop('checked',false);
            }

            let value = null;
            if($(this).is(':checked')){
                value = $(this).val();
            }
            let dataId =  $(this).data('id');
            $.ajax({
                url: '{{ route("admin.exitItem.checkedPost") }}',
                type: "POST",
                method: 'post',
                data: {
                    id: value,
                    dataId: dataId
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
            
        })

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