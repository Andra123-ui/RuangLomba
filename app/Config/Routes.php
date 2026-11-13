<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->get('cari', 'Cari::index');

$routes->get('peluang/(:num)', 'Cari::detail/$1');
$routes->get('peluang/(:num)/daftar', 'Participant\Registrations::form/$1', ['filter' => 'auth:participant']);
$routes->post('peluang/(:num)/daftar', 'Participant\Registrations::store/$1', ['filter' => 'auth:participant']);
$routes->get('pendaftaran-saya', 'Participant\Registrations::index', ['filter' => 'auth:participant']);
$routes->get('tersimpan', 'Participant\Bookmarks::index', ['filter' => 'auth:participant']);
$routes->post('peluang/(:num)/simpan', 'Participant\Bookmarks::toggle/$1', ['filter' => 'auth:participant']);

// Notifikasi (semua peran)
$routes->get('notifikasi', 'Notifications::index', ['filter' => 'auth']);
$routes->post('notifikasi/baca-semua', 'Notifications::readAll', ['filter' => 'auth']);
$routes->post('notifikasi/(:num)/baca', 'Notifications::read/$1', ['filter' => 'auth']);

$routes->get('tentang-kami', 'Pages::tentang');
$routes->get('syarat-ketentuan', 'Pages::syarat');
$routes->get('kebijakan-privasi', 'Pages::privasi');
$routes->get('bantuan', 'Pages::bantuan');

$routes->get('untuk-organizer', 'Panduan::organizer');

// Auth
$routes->get('masuk', 'Auth::login');
$routes->post('masuk', 'Auth::attemptLogin');
$routes->get('daftar', 'Auth::register');
$routes->post('daftar', 'Auth::attemptRegister');
$routes->post('keluar', 'Auth::logout');

$routes->get('verifikasi', 'Auth::verify');
$routes->post('verifikasi', 'Auth::attemptVerify');
$routes->post('verifikasi/kirim-ulang', 'Auth::resendVerify');

$routes->get('lupa-password', 'Auth::forgot');
$routes->post('lupa-password', 'Auth::attemptForgot');

$routes->get('reset-password', 'Auth::reset');
$routes->post('reset-password', 'Auth::attemptResetOtp');
$routes->post('reset-password/simpan', 'Auth::attemptResetPassword');
$routes->post('reset-password/kirim-ulang', 'Auth::resendReset');

// Area penyelenggara
$routes->group('organizer', ['filter' => 'auth:organizer'], static function ($routes) {
    $routes->get('dashboard', 'Organizer\Dashboard::index');

    $routes->get('lomba', 'Organizer\Competitions::index');
    $routes->get('lomba/baru', 'Organizer\Competitions::create');
    $routes->post('lomba', 'Organizer\Competitions::store');
    $routes->get('lomba/(:num)/edit', 'Organizer\Competitions::edit/$1');
    $routes->post('lomba/(:num)', 'Organizer\Competitions::update/$1');
    $routes->post('lomba/(:num)/hapus', 'Organizer\Competitions::delete/$1');

    $routes->get('lomba/(:num)/pendaftar', 'Organizer\Registrations::index/$1');
    $routes->post('pendaftar/(:num)/verifikasi', 'Organizer\Registrations::verify/$1');
    $routes->post('pendaftar/(:num)/tolak', 'Organizer\Registrations::reject/$1');
});

// Area admin
$routes->group('admin', ['filter' => 'auth:admin'], static function ($routes) {
    $routes->get('peluang', 'Admin\Competitions::index');
    $routes->get('peluang/(:num)', 'Admin\Competitions::show/$1');
    $routes->post('peluang/(:num)/setujui', 'Admin\Competitions::approve/$1');
    $routes->post('peluang/(:num)/tolak', 'Admin\Competitions::reject/$1');
});