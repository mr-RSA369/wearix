<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Services\ProductService;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Size;
use Inertia\Inertia;

class ProductController extends Controller
{
    protected $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            return response()->json([
                'status' => true,
                'data' => $this->service->getAll($request->all())
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(ProductRequest $request)
    {
        try {
            $product = $this->service->create($request->validated());

            return response()->json([
                'status' => true,
                'message' => 'Product created successfully',
                'data' => $product
            ], 201);

        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => 'Product creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }


public function show($id)
{
    $product = Product::with([
        'colors.media',
        'media',
        'sizes',
        'details',
        'sizeGuide'
    ])->findOrFail($id);

    return Inertia::render('Products/Show', [
        'product' => $product
    ]);
}

public function edit($id)
    {
        $product = Product::with([
            'colors.media',
            'media',
            'sizes',
            'details',
            'sizeGuide'
        ])->findOrFail($id);

        return Inertia::render('Products/Edit', [
            'product' => $product
        ]);
    }

    public function update(ProductRequest $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            $updated = $this->service->update($product, $request);

            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully',
                'data' => $updated
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => 'Product update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);

            $this->service->delete($product);

            return response()->json([
                'status' => true,
                'message' => 'Product deleted successfully'
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => 'Delete failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function sizes()
    {
        return Size::select('id', 'name')
            ->orderBy('id')
            ->get();
    }
}
