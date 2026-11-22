<?php

namespace App\Http\Controllers;

use App\Support\MenuItems;
use Illuminate\Contracts\View\View;

class CatalogController extends Controller
{
    public function index(): View
    {
        $items = collect(MenuItems::all())
            ->filter(static fn (array $item): bool => $item['price'] !== null
                || $item['ice'] !== null
                || $item['hot'] !== null)
            ->values();

        // Kelompokkan per kategori, urutan mengikuti urutan di config
        $groups = $items->groupBy('group')->map(fn ($list) => [
            'name' => $list->first()['group'],
            'type' => $list->first()['type'],
            'items' => $list->values()->all(),
        ])->values();

        return view('catalog', [
            'groups' => $groups->all(),
            'foodGroups' => $groups->where('type', 'food')->values()->all(),
            'drinkGroups' => $groups->where('type', 'drink')->values()->all(),
            'total' => $items->count(),
            'foodCount' => $items->where('type', 'food')->count(),
            'drinkCount' => $items->where('type', 'drink')->count(),
        ]);
    }
}
