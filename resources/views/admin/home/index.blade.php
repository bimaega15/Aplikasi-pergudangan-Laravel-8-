@extends('layout.admin.index')

@section('title')
Home page
@endsection

@section('content')
<!--**********************************
            Content body start
        ***********************************-->
<div class="content-body">
    <!-- row -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

            </div>
            <div class="col-xl-6 col-xxl-12">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="card avtivity-card">
                            <div class="card-body">
                                <div class="media align-items-center">
                                    <span class="activity-icon bgl-success mr-md-4 mr-3">
                                        <i class="fa-solid fa-location-crosshairs fa-2x" style="padding-top: 25px;"></i>
                                    </span>
                                    <div class="media-body">
                                        <p class="fs-14 mb-2">Lokasi barang</p>
                                        <span class="title text-black font-w600">{{ $location }}</span>
                                    </div>
                                </div>

                            </div>
                            <div class="effect bg-success"></div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card avtivity-card">
                            <div class="card-body">
                                <div class="media align-items-center">
                                    <span class="activity-icon bgl-secondary  mr-md-4 mr-3">
                                        <i class="fa-solid fa-table fa-2x" style="padding-top: 25px;"></i>
                                    </span>
                                    <div class="media-body">
                                        <p class="fs-14 mb-2">Jenis tipe</p>
                                        <span class="title text-black font-w600">{{ $uniteType }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="effect bg-secondary"></div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card avtivity-card">
                            <div class="card-body">
                                <div class="media align-items-center">
                                    <span class="activity-icon bgl-secondary  mr-md-4 mr-3">
                                        <i class="fa-solid fa-box-open fa-2x" style="padding-top: 25px;"></i>
                                    </span>
                                    <div class="media-body">
                                        <p class="fs-14 mb-2">Barang</p>
                                        <span class="title text-black font-w600">{{ $item }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="effect bg-secondary"></div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card avtivity-card">
                            <div class="card-body">
                                <div class="media align-items-center">
                                    <span class="activity-icon bgl-success mr-md-4 mr-3">
                                        <i class="fa-solid fa-inbox fa-2x" style="padding-top: 25px;"></i>
                                    </span>
                                    <div class="media-body">
                                        <p class="fs-14 mb-2">Barang masuk</p>
                                        <span class="title text-black font-w600">{{ $incomingGoods }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="effect bg-success"></div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card avtivity-card">
                            <div class="card-body">
                                <div class="media align-items-center">
                                    <span class="activity-icon bgl-warning  mr-md-4 mr-3">
                                        <i class="fa-solid fa-arrow-right-from-bracket fa-2x"
                                            style="padding-top: 25px;"></i>
                                    </span>
                                    <div class="media-body">
                                        <p class="fs-14 mb-2">Barang keluar</p>
                                        <span class="title text-black font-w600">{{ $exitItem }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="effect bg-warning"></div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card avtivity-card">
                            <div class="card-body">
                                <div class="media align-items-center">
                                    <span class="activity-icon bgl-dark  mr-md-4 mr-3">
                                        <i class="fa-solid fa-user-lock fa-2x" style="padding-top: 25px;"></i>
                                    </span>
                                    <div class="media-body">
                                        <p class="fs-14 mb-2">User</p>
                                        <span class="title text-black font-w600">{{ $users }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="effect bg-dark"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-xxl-12">
                <div class="card">
                    <div class="card-header d-sm-flex d-block pb-0 border-0">
                        <div class="mr-auto pr-3 mb-sm-0 mb-3">
                            <h4 class="text-black fs-20">Laporan Transaksi</h4>
                            <p class="fs-13 mb-0 text-black">Berikut transaksi barang masuk dan keluar</p>
                        </div>
                    </div>
                    <div class="card-body pt-0 pb-0">
                        <canvas id="myChart" style="width: 100%;" height="500px;"></canvas>
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
<script></script>
<script>
    var speedCanvas = document.getElementById("myChart");

    Chart.defaults.global.defaultFontFamily = "Lato";
    Chart.defaults.global.defaultFontSize = 16;

    var dataFirst = {
        label: "Barang keluar",
        data: {{ $valueExitItem }},
        borderColor: 'red',
        backgroundColor: 'rgba(255, 99, 132, 0.2)',
        borderWidth: 4

    };

    var dataSecond = {
        label: "Barang masuk",
        data: {{ $valueIncomingGoods }},
        borderColor: 'blue',
        backgroundColor: 'rgba(66, 38, 224, 0.3)',
        borderWidth: 4
    };

    var speedData = {
        labels: ["{{ $januari }}", "{{ $februari }}", "{{ $maret }}", "{{ $april }}", "{{ $mei }}", "{{ $juni }}", "{{ $juli }}", "{{ $agustus }}", "{{ $september }}", "{{ $oktober }}","{{ $november }}","{{ $desember }}"],
        datasets: [dataFirst, dataSecond]
    };

    var chartOptions = {
        legend: {
            display: true,
            position: 'top',
            labels: {
                boxWidth: 40,
                fontColor: 'black'
            }
        }
    };

    var lineChart = new Chart(speedCanvas, {
        type: 'line',
        data: speedData,
        options: chartOptions
    });
</script>
@endpush