<?php
$CAT_URLS = [
    'Creams'               => '/creams',
    'Serums'               => '/serums',
    'Face Wash'            => '/face-wash',
    'Petroleum Jelly'      => '/petroleum-jelly',
    'Lotions'              => '/lotions',
    'Shampoo & Conditioner'=> '/shampoo-conditioner',
    'Baby & Kids'          => '/baby-kids',
    'Hair Care'            => '/hair-care',
    'Essential Oils'       => '/essential-oils',
    'Facial'               => '/facial',
];

function catUrl(string $cat): string {
    global $CAT_URLS;
    return $CAT_URLS[$cat] ?? '/products?cat=' . urlencode($cat);
}
