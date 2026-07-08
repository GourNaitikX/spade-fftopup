<?php
session_start();

define('DATA_DIR', __DIR__ . '/data');
if (!is_dir(DATA_DIR)) mkdir(DATA_DIR, 0777, true);

$FILES = [
    'settings' => DATA_DIR . '/settings.json',
    'packs'    => DATA_DIR . '/packs.json',
    'stats'    => DATA_DIR . '/stats.json',
];

/* ---- default data (created on first run) ---- */
if (!file_exists($FILES['settings'])) {
    file_put_contents($FILES['settings'], json_encode([
        'admin_user'   => 'zerospade',
        'admin_pass'   => 'spadewebs',
        'upi_id'       => 'example@upi',
        'upi_name'     => 'FF TopUp Store',
        'payment_mode' => 'allupi', // 'allupi' or 'razorpay'
        'razorpay_key' => ''
    ], JSON_PRETTY_PRINT));
}
if (!file_exists($FILES['packs'])) {
    file_put_contents($FILES['packs'], json_encode([
        ['id'=>1,'diamonds'=>100,'original'=>149,'offer'=>77],
        ['id'=>2,'diamonds'=>310,'original'=>399,'offer'=>229],
        ['id'=>3,'diamonds'=>520,'original'=>649,'offer'=>379],
        ['id'=>4,'diamonds'=>1060,'original'=>1299,'offer'=>759],
        ['id'=>5,'diamonds'=>2180,'original'=>2599,'offer'=>1499],
        ['id'=>6,'diamonds'=>5600,'original'=>6499,'offer'=>3799],
    ], JSON_PRETTY_PRINT));
}
if (!file_exists($FILES['stats'])) {
    file_put_contents($FILES['stats'], json_encode([
        'visits' => 0,
        'checkouts' => 0
    ], JSON_PRETTY_PRINT));
}

function get_data($key){ global $FILES; return json_decode(file_get_contents($FILES[$key]), true); }
function save_data($key,$d){ global $FILES; file_put_contents($FILES[$key], json_encode($d, JSON_PRETTY_PRINT)); }

function bump_stat($field){
    $s = get_data('stats');
    $s[$field] = ($s[$field] ?? 0) + 1;
    save_data('stats',$s);
}