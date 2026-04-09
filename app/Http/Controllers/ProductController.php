<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate; // ✅ tambahkan ini

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate(10);
        return view('product.index', compact('products'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get(); 
        return view('product.create', compact('users'));
    }

    public function store(Request $request)
    {
         $validated = $request->validate([
        'name'     => 'required|string|max:255',
        'quantity' => 'required|integer',
        'price'    => 'required|numeric',
    ]);

    $validated['qty'] = $validated['quantity'];
    unset($validated['quantity']);

    // ✅ Jika admin memilih owner dari dropdown, pakai itu
    // Jika tidak, pakai user yang sedang login
    $validated['user_id'] = $request->input('user_id') ?? auth()->id();

    Product::create($validated);

    return redirect()->route('product.index')
        ->with('success', 'Product created successfully.');
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('product.view', compact('product'));
    }

    public function edit(Product $product)
    {
        // ✅ Ganti $this->authorize() dengan Gate::authorize()
        Gate::authorize('update', $product);

        $users = User::orderBy('name')->get();
        return view('product.edit', compact('product', 'users'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // ✅ Ganti $this->authorize() dengan Gate::authorize()
        Gate::authorize('update', $product);

        $validated = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'quantity' => 'sometimes|integer',
            'price'    => 'sometimes|numeric',
        ]);

        if (isset($validated['quantity'])) {
            $validated['qty'] = $validated['quantity'];
            unset($validated['quantity']);
        }

        $product->update($validated);

        return redirect()->route('product.index')
            ->with('success', 'Product updated successfully.');
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);

        // ✅ Ganti $this->authorize() dengan Gate::authorize()
        Gate::authorize('delete', $product);

        $product->delete();

        return redirect()->route('product.index')
            ->with('success', 'Product berhasil dihapus');
    }
}