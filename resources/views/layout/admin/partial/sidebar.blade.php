<!--**********************************
            Sidebar start
        ***********************************-->
<?php 
$configuration = CheckHelp::configuration();
?>
<div class="deznav">
    <div class="deznav-scroll">
        <ul class="metismenu" id="menu">
            <li @if (request()->routeIs('admin.home.index'))
                class = "mm-active"
                @endif
                >
                <a href="{{ route('admin.home.index') }}" class="ai-icon @if (request()->routeIs('admin.home.index'))
                    mm-active
                    @endif" aria-expanded="false">
                    <i class="flaticon-381-networking"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li @if (request()->routeIs('admin.profile.index'))
                class = "mm-active"
                @endif>
                <a href="{{ route('admin.profile.index') }}" class="ai-icon @if (request()->routeIs('admin.profile.index'))
                    mm-active @endif" aria-expanded="false">
                    <i class="flaticon-381-user"></i>
                    <span class="nav-text">My Profile</span>
                </a>
            </li>

            @can('user-admin')
            <li @if (request()->routeIs('admin.users.index'))
                class = "mm-active"
                @endif>
                <a href="{{ route('admin.users.index') }}" class="ai-icon @if (request()->routeIs('admin.users.index'))
                    mm-active @endif" aria-expanded="false">
                    <i class="flaticon-381-user-9"></i>
                    <span class="nav-text">Users</span>
                </a>
            </li>

            <li @if (request()->routeIs('admin.item.index') || request()->routeIs('admin.uniteType.index') ||
                request()->routeIs('admin.location.index'))
                class = "mm-active"
                @endif
                >
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="flaticon-381-network"></i>
                    <span class="nav-text">Data master</span>
                </a>
                <ul aria-expanded="false">
                    <li @if (request()->routeIs('admin.uniteType.index'))
                        class="mm-active"
                        @endif><a @if (request()->routeIs('admin.uniteType.index'))
                            class="mm-active"
                            @endif href="{{ route('admin.uniteType.index') }}">Jenis tipe</a></li>
                    <li @if (request()->routeIs('admin.location.index'))
                        class="mm-active"
                        @endif><a @if (request()->routeIs('admin.location.index'))
                            class="mm-active"
                            @endif href="{{ route('admin.location.index') }}" >Lokasi barang</a></li>
                    <li @if (request()->routeIs('admin.item.index'))
                        class="mm-active"
                        @endif><a href="{{ route('admin.item.index') }}" @if (request()->routeIs('admin.item.index'))
                            class="mm-active"
                            @endif>Data barang</a></li>
                </ul>
            </li>
            @endcan

            <li @if (request()->routeIs('admin.stockStore.index'))
                class = "mm-active"
                @endif>
                <a href="{{ route('admin.stockStore.index') }}" class="ai-icon @if (request()->routeIs('admin.stockStore.index'))
                    mm-active @endif" aria-expanded="false">
                    <i class="flaticon-381-box"></i>
                    <span class="nav-text">Stok gudang</span>
                </a>
            </li>


            <li @if (request()->routeIs('admin.incomingGoods.index') || request()->routeIs('admin.exitItem.index'))
                class = "mm-active"
                @endif>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="flaticon-381-box-2"></i>
                    <span class="nav-text">Data transaksi</span>
                </a>
                <ul aria-expanded="false">
                    <li @if (request()->routeIs('admin.incomingGoods.index'))
                        class="mm-active"
                        @endif><a @if (request()->routeIs('admin.incomingGoods.index'))
                            class="mm-active"
                            @endif href="{{ route('admin.incomingGoods.index') }}">Transaksi Barang Masuk</a></li>
                    <li @if (request()->routeIs('admin.exitItem.index'))
                        class="mm-active"
                        @endif><a @if (request()->routeIs('admin.exitItem.index'))
                            class="mm-active"
                            @endif href="{{ route('admin.exitItem.index') }}">Transaksi Barang Keluar</a></li>
                </ul>
            </li>

            <li @if (request()->routeIs('admin.reportItemin.index') || request()->routeIs('admin.reportItemOut.index'))
                class = "mm-active"
                @endif>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="flaticon-381-book"></i>
                    <span class="nav-text">Laporan Transaksi</span>
                </a>
                <ul aria-expanded="false">
                    <li @if (request()->routeIs('admin.reportStock.index'))
                        class="mm-active"
                        @endif><a @if (request()->routeIs('admin.reportStock.index'))
                            class="mm-active"
                            @endif href="{{ route('admin.reportStock.index') }}">Laporan Stock Gudang</a>
                    </li>
                    <li @if (request()->routeIs('admin.reportItemIn.index'))
                        class="mm-active"
                        @endif><a @if (request()->routeIs('admin.reportItemIn.index'))
                            class="mm-active"
                            @endif href="{{ route('admin.reportItemIn.index') }}">Laporan Transaksi Barang Masuk</a>
                    </li>
                    <li @if (request()->routeIs('admin.reportItemOut.index'))
                        class="mm-active"
                        @endif><a @if (request()->routeIs('admin.reportItemOut.index'))
                            class="mm-active"
                            @endif href="{{ route('admin.reportItemOut.index') }}">Laporan Transaksi Barang Keluar</a>
                    </li>
                </ul>
            </li>

            @can('user-admin')
            <li @if (request()->routeIs('admin.configuration.index'))
                class = "mm-active"
                @endif>
                <a href="{{ route('admin.configuration.index') }}" class="ai-icon @if (request()->routeIs('admin.configuration.index'))
                    mm-active @endif" aria-expanded="false">
                    <i class="flaticon-381-settings"></i>
                    <span class="nav-text">Konfigurasi</span>
                </a>
            </li>
            @endcan

        </ul>
        <div class="add-menu-sidebar">
            <img src="{{ asset('image/konfigurasi/'.$configuration->picture_configuration) }}" alt="" class="mr-3"
                width="40%;">
            <p class="	font-w500 mb-0">{{ $configuration->name_configuration }}</p>
        </div>
        <div class="copyright">
            <p><strong>{{ $configuration->created_by_configuration }}</strong> © 2022 All Rights Reserved</p>
            <p>Made with <span class="heart"></span> by hand Bima Ega</p>
        </div>
    </div>
</div>
<!--**********************************
            Sidebar end
        ***********************************-->