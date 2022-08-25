<?php

// Home
Breadcrumbs::for('home', function ($trail) {
    $trail->push('Home', route('admin.home.index'));
});

// Home > Profile
Breadcrumbs::for('profile', function ($trail) {
    $trail->parent('home');
    $trail->push('Profile', route('admin.profile.index'));
});

// Home > Users
Breadcrumbs::for('users', function ($trail) {
    $trail->parent('home');
    $trail->push('Users', route('admin.users.index'));
});

// Home > uniteType
Breadcrumbs::for('uniteType', function ($trail) {
    $trail->parent('home');
    $trail->push('Jenis tipe', route('admin.uniteType.index'));
});

// Home > location
Breadcrumbs::for('location', function ($trail) {
    $trail->parent('home');
    $trail->push('Lokasi', route('admin.location.index'));
});

// Home > item
Breadcrumbs::for('item', function ($trail) {
    $trail->parent('home');
    $trail->push('Item', route('admin.item.index'));
});

// Home > stockStore
Breadcrumbs::for('stockStore', function ($trail) {
    $trail->parent('home');
    $trail->push('Stok gudang', route('admin.stockStore.index'));
});

// Home > incomingGoods
Breadcrumbs::for('incomingGoods', function ($trail) {
    $trail->parent('home');
    $trail->push('Transaksi barang masuk', route('admin.incomingGoods.index'));
});

// Home > create incomingGoods
Breadcrumbs::for('createIncomingGoods', function ($trail) {
    $trail->parent('incomingGoods');
    $trail->push('Tambah Transaksi barang masuk', route('admin.incomingGoods.create'));
});

// Home > create incomingGoods
Breadcrumbs::for('editMultipleIncomingGoods', function ($trail) {
    $trail->parent('incomingGoods');
    $trail->push('Edit Transaksi barang masuk', route('admin.incomingGoods.editMultiple'));
});

// Home > exitItem
Breadcrumbs::for('exitItem', function ($trail) {
    $trail->parent('home');
    $trail->push('Transaksi barang keluar', route('admin.exitItem.index'));
});

// Home > create exitItem
Breadcrumbs::for('createExitItem', function ($trail) {
    $trail->parent('exitItem');
    $trail->push('Tambah Transaksi barang keluar', route('admin.exitItem.create'));
});

// Home > create incomingGoods
Breadcrumbs::for('editMultipleExitItem', function ($trail) {
    $trail->parent('exitItem');
    $trail->push('Edit Transaksi barang keluar', route('admin.exitItem.editMultiple'));
});

// Home > reportStock
Breadcrumbs::for('reportStock', function ($trail) {
    $trail->parent('home');
    $trail->push('Laporan Stok Gudang', route('admin.reportStock.index'));
});

// Home > configuration
Breadcrumbs::for('configuration', function ($trail) {
    $trail->parent('home');
    $trail->push('Konfigurasi', route('admin.configuration.index'));
});
