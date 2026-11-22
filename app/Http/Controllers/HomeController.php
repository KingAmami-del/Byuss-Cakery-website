<?php

namespace App\Http\Controllers;

use App\Support\MenuItems;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredProducts = array_values(array_filter(
            config('byuss.products'),
            static fn (array $product): bool => $product['featured'],
        ));

        return view('home', [
            'outlet' => config('byuss.outlet'),
            'featured' => $featuredProducts,
            'quizItems' => MenuItems::quizItems(),
        ]);
    }
}
