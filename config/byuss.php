<?php

/*
|--------------------------------------------------------------------------
| Data Byuss Cakery (sementara, sebelum tersambung ke database)
|--------------------------------------------------------------------------
| Semua nilai bertanda [GANTI] adalah contoh. Isi dengan data asli outlet.
| Nanti, produk bisa dipindah ke tabel database (Eloquent) tanpa mengubah
| tampilan, cukup ganti isi HomeController.
*/

return [

    'name' => 'Byuss Cakery',

    // Bar di paling atas halaman
    'announcement' => 'Dibuat dengan hati, untuk momen yang berarti.',

    // Ringkasan outlet
    'outlet' => [
        'name'    => 'House of Byuss',
        'tagline' => 'Tempat duduk hangat, dessert segar, dan cerita yang tidak habis.',
        'address' => 'Jl. Kh. Hasyim Asy\'ari No.116, Banjarmlati, Kec. Mojoroto, Kota Kediri, Jawa Timur 64119',
        'hours'   => [
            ['Senin – Jumat', '10.00 – 21.00'],   // [GANTI]
            ['Sabtu – Minggu', '09.00 – 22.00'],  // [GANTI]
        ],
        'phone'     => '+62 8xx-xxxx-xxxx',       // [GANTI]
        'instagram' => '@byusscakery',            // [GANTI]
        'features'  => ['Dine-in', 'Take away', 'Pre-order kue utuh', 'Ruang foto cantik'], // [GANTI]
    ],

    // Produk (contoh). Yang 'featured' => true tampil di "Favorit pelanggan kami".
    'products' => [
        [
            'slug' => 'strawberry-shortcake', 'name' => 'Strawberry Shortcake', 'category' => 'Cake & Slice',
            'price' => 38000, 'featured' => true, 'badge' => 'Favorit',
            'desc' => 'Sponge lembut, krim vanila, dan stroberi segar.',
        ],
        [
            'slug' => 'basque-burnt-cheesecake', 'name' => 'Basque Burnt Cheesecake', 'category' => 'Cake & Slice',
            'price' => 42000, 'featured' => true, 'badge' => 'Best Seller',
            'desc' => 'Bagian luar karamel, dalamnya creamy dan lumer.',
        ],
        [
            'slug' => 'tiramisu-cup', 'name' => 'Tiramisu Cup', 'category' => 'Dessert Cup',
            'price' => 32000, 'featured' => true, 'badge' => null,
            'desc' => 'Mascarpone halus dengan sentuhan kopi dan kakao.',
        ],
        [
            'slug' => 'chocolate-fudge-cake', 'name' => 'Chocolate Fudge Cake', 'category' => 'Cake & Slice',
            'price' => 36000, 'featured' => true, 'badge' => null,
            'desc' => 'Cokelat pekat berlapis ganache yang legit.',
        ],
        [
            'slug' => 'matcha-mille-crepes', 'name' => 'Matcha Mille Crepes', 'category' => 'Cake & Slice',
            'price' => 40000, 'featured' => false, 'badge' => null,
            'desc' => 'Lapisan crepe tipis dengan krim matcha ringan.',
        ],
        [
            'slug' => 'banana-pudding-cup', 'name' => 'Banana Pudding Cup', 'category' => 'Dessert Cup',
            'price' => 28000, 'featured' => false, 'badge' => null,
            'desc' => 'Pisang, krim, dan biskuit yang meresap lembut.',
        ],
        [
            'slug' => 'lemon-tart', 'name' => 'Lemon Tart', 'category' => 'Pastry & Cookies',
            'price' => 30000, 'featured' => false, 'badge' => null,
            'desc' => 'Kulit tart renyah dengan isian lemon yang segar.',
        ],
        [
            'slug' => 'choco-chip-cookies', 'name' => 'Choco Chip Cookies', 'category' => 'Pastry & Cookies',
            'price' => 18000, 'featured' => false, 'badge' => null,
            'desc' => 'Renyah di pinggir, chewy di tengah.',
        ],
    ],
];
