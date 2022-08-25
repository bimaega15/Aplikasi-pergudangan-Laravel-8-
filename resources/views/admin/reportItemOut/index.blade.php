@extends('layout.admin.index')

@section('title')
Laporan Transaksi barang keluar page
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


    .btn-active {
        background-color: #3054e3 !important;
        color: white !important;
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
                        <h4 class="card-title">Laporan Transaksi Barang Keluar</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-lg-12">
                                <a href="#" class="badge badge-light btn-all">
                                    Semua
                                </a>
                                <a href="#" class="badge badge-light btn-yesterday">
                                    1 Hari Terakhir
                                </a>
                                <a href="#" class="badge badge-light btn-last-week">
                                    Seminggu Terakhir
                                </a>
                                <a href="#" class="badge badge-light btn-last-month">
                                    Sebulan Terakhir
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <input type="text" class="form-control from_date"
                                                placeholder="Dari tanggal">
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <input type="text" class="form-control to_date" placeholder="Dari tanggal">
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-primary btm-sm btn-filter">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @can('user-admin')
                        <div class="mb-2">
                            <a href="{{ route('admin.reportItemOut.export') }}" class="btn btn-success btn-export">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </a>
                        </div>
                        @endcan

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
        loadTableAjax();
        function loadTableAjax(value = null, from_date = null, to_date = null)
        {
            var table = $('#dataTable').DataTable({
                ajax: {
                    url: '{{route("admin.reportItemOut.index")}}',
                    dataType: 'json',
                    data: {
                        value: value,
                        from_date: from_date,
                        to_date: to_date
                    },
                },
                processing: true,
                serverSide: true,
                columns: [
                    {data: 'DT_RowIndex', name: 'no'},
                    {data: 'out_date_exit_item', name: 'out_date_exit_item'},
                    {data: 'name_location', name: 'name_location'},
                    {data: 'code_item', name: 'code_item'},
                    {data: 'name_item', name: 'name_item'},
                    {data: 'name_unite_type', name: 'name_unite_type'},
                    {data: 'stock_exit_item', name: 'stock_exit_item'},
                    {data: 'picture_item', name: 'picture_item', orderable: false, searchable: false},
                ]
            });
            return table;
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
        
            $('.from_date').datepicker({
            todayBtn:  true,
            todayHighlight: true,
            format: 'dd-mm-yyyy'
        });
        
        $('.to_date').datepicker({
            todayBtn:  true,
            todayHighlight: true,
            format: 'dd-mm-yyyy'
        });

        $(document).on('click','.btn-filter',function(e){
            e.preventDefault();
            let from_date = $('.from_date').val();
            let error = false;
            if(from_date == null || from_date == ''){
                error = true;
                alert('Dari tanggal tidak boleh kosong');
            }

            let to_date = $('.to_date').val();
            if(to_date == null || to_date == ''){
                error = true;
                alert('Sampai tanggal tidak boleh kosong');
            }
            if(error){
                return;
            }

            $('#dataTable').DataTable().destroy();
            loadTableAjax(null, from_date, to_date);

            let href = "{{ route('admin.reportItemOut.export') }}";
            href += '?from_date='+from_date+'&to_date='+to_date;
            $('.btn-export').attr('href', href);
        })

        $(document).on('click','.btn-all',function(e){
            e.preventDefault();
            $(this).addClass("btn-active");
            $('.btn-yesterday').removeClass('btn-active');
            $('.btn-last-week').removeClass('btn-active');
            $('.btn-last-month').removeClass('btn-active');

            $('#dataTable').DataTable().destroy();
            let value = 'all';
            loadTableAjax(value);

            let href = "{{ route('admin.reportItemOut.export') }}";
            href += '?filter='+encodeURI(value);
            $('.btn-export').attr('href', href);
        })

        $(document).on('click','.btn-yesterday',function(e){
            e.preventDefault();
            $(this).addClass("btn-active");
            $('.btn-all').removeClass('btn-active');
            $('.btn-last-week').removeClass('btn-active');
            $('.btn-last-month').removeClass('btn-active');

            $('#dataTable').DataTable().destroy();
            let value = '-1 days';
            loadTableAjax(value);

            let href = "{{ route('admin.reportItemOut.export') }}";
            href += '?filter='+encodeURI(value);
            $('.btn-export').attr('href', href);
        })

        $(document).on('click','.btn-last-week',function(e){
            e.preventDefault();
            $(this).addClass("btn-active");
            $('.btn-yesterday').removeClass('btn-active');
            $('.btn-all').removeClass('btn-active');
            $('.btn-last-month').removeClass('btn-active');

            $('#dataTable').DataTable().destroy();
            let value = '-7 days';
            loadTableAjax(value);

            let href = "{{ route('admin.reportItemOut.export') }}";
            href += '?filter='+encodeURI(value);
            $('.btn-export').attr('href', href);
        })

        $(document).on('click','.btn-last-month',function(e){
            e.preventDefault();
            $(this).addClass("btn-active");
            $('.btn-yesterday').removeClass('btn-active');
            $('.btn-last-week').removeClass('btn-active');
            $('.btn-all').removeClass('btn-active');

            $('#dataTable').DataTable().destroy();
            let value = '-1 month';
            loadTableAjax(value);

            let href = "{{ route('admin.reportItemOut.export') }}";
            href += '?filter='+encodeURI(value);
            $('.btn-export').attr('href', href);
        })
    })
</script>
@endpush
@endsection