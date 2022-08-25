@extends('layout.admin.index')

@section('title')
Tambah Transaksi barang keluar page
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
            {{ Breadcrumbs::render('createExitItem') }}
        </div>
        <div class="row">
            <div class="col-12">
                @include('session')
                <div class="card" style="height: auto;">
                    @if($errors->any())
                    @foreach ( $errors->all() as $error)
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Failed !</strong> {!! $error !!}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endforeach
                    @endif

                    <div class="card-header">
                        <h4 class="card-title">Tambah Transaksi Barang Keluar</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-10">
                                <div class="form-group">
                                    <label>
                                        Data Barang di Gudang
                                    </label>
                                    <select class="form-control select2-stock-store" id="stock_store_id"
                                        name="stock_store_id">
                                        <option value="">-- Data Barang --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary btn-choose" style="margin-top: 30px;">
                                        <i class="fas fa-check"></i> Pilih
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-12">
                <div class="card" style="height: auto;">
                    <div class="card-header">
                        <h4 class="card-title">Daftar Transaksi Barang Keluar</h4>
                    </div>
                    <form action="{{ route('admin.exitItem.store') }}" method="post">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <input type="hidden" name="page" value="add">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Submit
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="display min-w850 table table-bordered" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama lokasi</th>
                                            <th>Kode barang</th>
                                            <th>Nama barang</th>
                                            <th>Tipe</th>
                                            <th>Gambar</th>
                                            <th width="15%;">Stock</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
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
                url: '{{route("admin.exitItem.loadDataTable")}}'
            },
            columns: [
                {data: 'DT_RowIndex', name: 'no'},
                {data: 'name_location', name: 'name_location'},
                {data: 'code_item', name: 'code_item'},
                {data: 'name_item', name: 'name_item'},
                {data: 'name_unite_type', name: 'name_unite_type'},
                {data: 'picture_item', name: 'picture_item'},
                {data: 'store_stock_store', name: 'store_stock_store'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });

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

        $(document).on('click','.btn-choose',function(e){
            e.preventDefault();
            let valStockStore = $('#stock_store_id').val();
            $.ajax({
                url: '/admin/exitItem/'+valStockStore+'/postCart',
                type: 'post',
                dataType: 'json',
                success: function(data){
                    table.ajax.reload();
                },
                error: function(x,t,m){
                    console.log(x.responseText);
                }
            })
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

        $(document).on('change','.input-stock',function(e){
            e.preventDefault();
            let val = $(this).val();
            let id = $(this).data('id');

            $.ajax({
                url: '/admin/exitItem/'+id+'/updateCart',
                type: 'post',
                method: 'put',
                dataType: 'json',
                data: {
                    id: id,
                    value: val,
                },
                success:function(data){
                    table.ajax.reload();
                },
                error: function(x,h,r){
                    console.log(x.responseText);
                }
            })
        })

        $(document).on('click','.btn-delete-cart',function(e){
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

        // loadInputStock();
        // function loadInputStock()
        // {
        //     $.ajax({
        //         url: '',
        //         type: 'get',
        //         dataType: 'json',
        //         success:function(data){
        //             const {
        //                 result
        //             } = data;

        //             let arrPush = [];
        //             result.map((v,i) => {
        //                 arrPush.push('numeric-'+v.id);
        //             })

        //             AutoNumeric.multiple(arrPush, 0, {
        //                 decimalPlaces: 0
        //             });
        //         },
        //         error: function(x,h,r){
        //             console.log(x.responseText);
        //         }
        //     })
        // }
    })
</script>
@endpush
@endsection