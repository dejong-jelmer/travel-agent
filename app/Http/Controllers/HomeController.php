<?php

namespace App\Http\Controllers;

use App\Models\Product;
use GuzzleHttp\Handler\Proxy;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;


class HomeController extends Controller
{
    private String $appName;

    public function __construct() {
        $this->appName = env('APP_NAME');
    }

    public function home(): Response
    {
        return Inertia::render('Home', [
            'products' => Product::with('countries')->get(),
        ]);
    }

    public function about(): Response
    {
        return Inertia::render('About', [
            'title' => "Over mij - {$this->appName}",
        ]);
    }

    public function contact(): Response
    {
        return Inertia::render('Contact', [
            'title' => "Contact - {$this->appName}",
        ]);
    }

    public function showProduct(Product $product): Response
    {
        return Inertia::render('Products/Show', [
            'title' => "{$this->appName} - {$product->name}",
            'product' => $product
        ]);
    }
}
