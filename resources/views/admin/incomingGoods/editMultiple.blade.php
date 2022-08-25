@extends('layout.admin.index')

@section('title')
Edit Transaksi barang masuk page
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
            {{ Breadcrumbs::render('editMultipleIncomingGoods') }}
        </div>
        <div class="row">
            @include('session')
            <div class="col-12">
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
                        <h4 class="card-title">Daftar Transaksi Barang Masuk</h4>
                    </div>
                    <form action="{{ route('admin.incomingGoods.updateMultiple') }}" method="post">
                        <div class="card-body">
                            <div class="form-group">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Submit
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table id="dataTable" class="display min-w850">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Lokasi Barang</th>
                                            <th>Kode barang</th>
                                            <th>Nama barang</th>
                                            <th>Tipe</th>
                                            <th>Stock</th>
                                            <th>Gambar</th>
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
                url: '{{route("admin.incomingGoods.loadDataTableEditMultiple")}}',
            },
            order: [[1, 'asc']],
            columns: [
                {data: 'DT_RowIndex', name: 'no'},
                {data: 'date_of_entry_incoming_goods', name: 'date_of_entry_incoming_goods'},
                {data: 'name_location', name: 'name_location'},
                {data: 'code_item', name: 'code_item'},
                {data: 'name_item', name: 'name_item'},
                {data: 'name_unite_type', name: 'name_unite_type'},
                {data: 'stock_incoming_goods', name: 'stock_incoming_goods'},
                {data: 'picture_item', name: 'picture_item', orderable: false, searchable: false},
            ]
        });

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