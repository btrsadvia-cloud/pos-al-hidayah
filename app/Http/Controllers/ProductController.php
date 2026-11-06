<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'unit', 'prices'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")->orWhere('barcode', 'like', "%{$s}%"));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->boolean('low_stock')) {
            $query->whereColumn('stock', '<=', 'min_stock');
        }

        $products   = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $units      = Unit::orderBy('name')->get();
        return view('products.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barcode'               => ['nullable', 'string', 'max:50', 'unique:products,barcode'],
            'name'                  => ['required', 'string', 'max:150'],
            'category_id'           => ['required', 'exists:categories,id'],
            'unit_id'               => ['required', 'exists:units,id'],
            'stock'                 => ['required', 'integer', 'min:0'],
            'min_stock'             => ['required', 'integer', 'min:0'],
            'image'                 => ['nullable', 'image', 'max:2048'],
            // Prices
            'price_retail'          => ['required', 'integer', 'min:0'],
            'min_qty_retail'        => ['required', 'integer', 'min:1'],
            'price_wholesale'       => ['required', 'integer', 'min:0'],
            'min_qty_wholesale'     => ['required', 'integer', 'min:1'],
            'price_super_wholesale' => ['required', 'integer', 'min:0'],
            'min_qty_super_wholesale'=> ['required', 'integer', 'min:1'],
        ]);

        return DB::transaction(function () use ($request, $data) {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
            }

            $product = Product::create([
                'barcode'     => $data['barcode'] ?: null,
                'name'        => $data['name'],
                'category_id' => $data['category_id'],
                'unit_id'     => $data['unit_id'],
                'image'       => $imagePath,
                'stock'       => $data['stock'],
                'min_stock'   => $data['min_stock'],
            ]);

            ProductPrice::create([
                'product_id' => $product->id,
                'tier'       => 'retail',
                'min_qty'    => $data['min_qty_retail'],
                'price'      => $data['price_retail'],
            ]);

            ProductPrice::create([
                'product_id' => $product->id,
                'tier'       => 'wholesale',
                'min_qty'    => $data['min_qty_wholesale'],
                'price'      => $data['price_wholesale'],
            ]);

            ProductPrice::create([
                'product_id' => $product->id,
                'tier'       => 'super_wholesale',
                'min_qty'    => $data['min_qty_super_wholesale'],
                'price'      => $data['price_super_wholesale'],
            ]);

            return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
        });
    }

    public function show(Product $product)
    {
        $product->load(['category', 'unit', 'prices', 'stockMovements.user', 'stockMovements.supplier']);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $units      = Unit::orderBy('name')->get();
        $product->load('prices');

        $retail         = $product->prices->firstWhere('tier', 'retail');
        $wholesale      = $product->prices->firstWhere('tier', 'wholesale');
        $superWholesale = $product->prices->firstWhere('tier', 'super_wholesale');

        return view('products.edit', compact('product', 'categories', 'units', 'retail', 'wholesale', 'superWholesale'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'barcode'               => ['nullable', 'string', 'max:50', 'unique:products,barcode,' . $product->id],
            'name'                  => ['required', 'string', 'max:150'],
            'category_id'           => ['required', 'exists:categories,id'],
            'unit_id'               => ['required', 'exists:units,id'],
            'min_stock'             => ['required', 'integer', 'min:0'],
            'image'                 => ['nullable', 'image', 'max:2048'],
            // Prices
            'price_retail'          => ['required', 'integer', 'min:0'],
            'min_qty_retail'        => ['required', 'integer', 'min:1'],
            'price_wholesale'       => ['required', 'integer', 'min:0'],
            'min_qty_wholesale'     => ['required', 'integer', 'min:1'],
            'price_super_wholesale' => ['required', 'integer', 'min:0'],
            'min_qty_super_wholesale'=> ['required', 'integer', 'min:1'],
        ]);

        return DB::transaction(function () use ($request, $data, $product) {
            $imagePath = $product->image;
            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $imagePath = $request->file('image')->store('products', 'public');
            }

            $product->update([
                'barcode'     => $data['barcode'] ?: null,
                'name'        => $data['name'],
                'category_id' => $data['category_id'],
                'unit_id'     => $data['unit_id'],
                'image'       => $imagePath,
                'min_stock'   => $data['min_stock'],
            ]);

            ProductPrice::updateOrCreate(
                ['product_id' => $product->id, 'tier' => 'retail'],
                ['min_qty' => $data['min_qty_retail'], 'price' => $data['price_retail']]
            );

            ProductPrice::updateOrCreate(
                ['product_id' => $product->id, 'tier' => 'wholesale'],
                ['min_qty' => $data['min_qty_wholesale'], 'price' => $data['price_wholesale']]
            );

            ProductPrice::updateOrCreate(
                ['product_id' => $product->id, 'tier' => 'super_wholesale'],
                ['min_qty' => $data['min_qty_super_wholesale'], 'price' => $data['price_super_wholesale']]
            );

            return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
        });
    }

    public function editPrices(Product $product)
    {
        $product->load('prices');
        $retail         = $product->prices->firstWhere('tier', 'retail');
        $wholesale      = $product->prices->firstWhere('tier', 'wholesale');
        $superWholesale = $product->prices->firstWhere('tier', 'super_wholesale');

        return view('products.prices', compact('product', 'retail', 'wholesale', 'superWholesale'));
    }

    public function updatePrices(Request $request, Product $product)
    {
        $data = $request->validate([
            'price_retail'           => ['required', 'integer', 'min:0'],
            'min_qty_retail'         => ['required', 'integer', 'min:1'],
            'price_wholesale'        => ['required', 'integer', 'min:0'],
            'min_qty_wholesale'      => ['required', 'integer', 'min:1'],
            'price_super_wholesale'  => ['required', 'integer', 'min:0'],
            'min_qty_super_wholesale' => ['required', 'integer', 'min:1'],
        ]);

        ProductPrice::updateOrCreate(
            ['product_id' => $product->id, 'tier' => 'retail'],
            ['min_qty' => $data['min_qty_retail'], 'price' => $data['price_retail']]
        );

        ProductPrice::updateOrCreate(
            ['product_id' => $product->id, 'tier' => 'wholesale'],
            ['min_qty' => $data['min_qty_wholesale'], 'price' => $data['price_wholesale']]
        );

        ProductPrice::updateOrCreate(
            ['product_id' => $product->id, 'tier' => 'super_wholesale'],
            ['min_qty' => $data['min_qty_super_wholesale'], 'price' => $data['price_super_wholesale']]
        );

        return redirect()->route('products.index')->with('success', 'Harga produk berhasil diperbarui.');
    }

    public function updateImage(Request $request, Product $product)
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $path = $request->file('image')->store('products', 'public');
        $product->update(['image' => $path]);

        return back()->with('success', "Foto untuk produk '{$product->name}' berhasil diperbarui!");
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
