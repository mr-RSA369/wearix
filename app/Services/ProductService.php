<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductMedia;
use App\Models\SizeGuide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    public function getAll($filters)
    {
        $cacheKey = 'products_' . md5(json_encode($filters));

        return Cache::remember($cacheKey, 60, function () use ($filters) {

            $query = Product::with(['category', 'media']);

            if (!empty($filters['category'])) {
                $categoryIds = Category::where('id', $filters['category'])
                    ->orWhere('parent_id', $filters['category'])
                    ->pluck('id');

                $query->whereIn('category_id', $categoryIds);
            }

            if (!empty($filters['search'])) {
                $query->where('name', 'LIKE', '%' . $filters['search'] . '%');
            }

            return $query->latest()->paginate(10);
        });
    }

    /* ================= CREATE ================= */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            $product = Product::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'category_id' => $data['category_id'],
                'price' => 0
            ]);

            $colorMap = $this->storeColors($product, $data['colors'] ?? []);
            $this->storeMedia($product, $data, $colorMap);
            $this->syncSizes($product, $data['sizes'] ?? []);
            $this->storeDetails($product, $data['details'] ?? []);
            $this->storeSizeGuide($product, $data['size_guide'] ?? null);

            $this->updateProductPrice($product, $data);

            Cache::flush();

            return $product->load([
                'colors.media',
                'media',
                'sizes',
                'details',
                'sizeGuide'
            ]);
        });
    }

    /* ================= UPDATE ================= */

public function update(Product $product, Request $request)
{
    return DB::transaction(function () use ($product, $request) {

        // BASIC INFO
        $product->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'category_id' => $request->input('category_id'),
        ]);

        // COLORS
        $colorMap = $this->syncColors($product, $request->input('colors', []));

        // MEDIA (UPDATED)
        $this->syncMedia($product, $request, $colorMap);

        // SIZES
        $this->syncSizes($product, $request->input('sizes', []));

        // DETAILS
        $product->details()->delete();
        $this->storeDetails($product, $request->input('details', []));

        // SIZE GUIDE
        $this->storeSizeGuide($product, $request->input('size_guide'));

        // PRICE
        $this->updateProductPrice($product, $request->all());

        Cache::flush();

        return $product->load([
            'colors.media',
            'media',
            'sizes',
            'details',
            'sizeGuide'
        ]);
    });
}

private function syncColors(Product $product, array $colors)
{
    $map = [];
    $existingIds = [];

    foreach ($colors as $tempId => $color) {

        // ✅ EXISTING COLOR (only pure integer IDs)
        if (ctype_digit((string)$tempId)) {

            $existing = $product->colors()->find($tempId);

            if ($existing) {
                $existing->update([
                    'name' => $color['name'],
                    'price' => $color['price']
                ]);

                $map[$tempId] = $existing->id;
                $existingIds[] = $existing->id;
            }

        } else {
            // ✅ NEW COLOR
            $created = $product->colors()->create([
                'name' => $color['name'],
                'price' => $color['price']
            ]);

            $map[$tempId] = $created->id;
            $existingIds[] = $created->id;
        }
    }

    // ✅ DELETE REMOVED COLORS
    $product->colors()
        ->whereNotIn('id', $existingIds)
        ->delete();

    return $map;
}

private function syncMedia(Product $product, Request $request, array $colorMap)
{
    // =========================
    // ✅ GENERAL IMAGES
    // =========================
    $imagesNew = $request->file('images_new', []);
    $imagesExisting = $request->input('images_existing', []);

    if(empty($imagesExisting)) {
         // DELETE OLD Which are removed by user
        $product->media()
            ->whereNull('color_id')
            ->where('type', 'image')
            ->delete();
    }

    if (!empty($imagesNew) || !empty($imagesExisting)) {
        // DELETE OLD Which are removed by user
        $product->media()
            ->whereNull('color_id')
            ->where('type', 'image')
            ->whereNotIn('id', $imagesExisting)
            ->delete();
        $order = 0;

        // ADD NEW
        foreach ($imagesNew as $img) {

            $path = $img->store('products', 'public');

            $product->media()->create([
                'type' => 'image',
                'path' => '/storage/' . $path,
                'order' => $order++,
                'color_id' => null
            ]);
        }
    }

    // =========================
    // ✅ COLOR IMAGES
    // =========================
    $colorImagesNew = $request->file('color_images_new', []);
    $colorImagesExisting = $request->input('color_images_existing', []);

    if(empty($colorImagesExisting)) {
        $product->media()
            ->whereNotNull('color_id')
            ->where('type', 'image')
            ->delete();
    }

    if (!empty($colorImagesNew) || !empty($colorImagesExisting)) {
        // DELETE OLD COLOR IMAGES
        $product->media()
            ->whereNotNull('color_id')
            ->where('type', 'image')
            ->whereNotIn('id', collect($colorImagesExisting)->flatten()->all())
            ->delete();

        foreach ($product->colors as $color) {

            $order = 0;

            // NEW
            foreach ($colorImagesNew[$color->id] ?? [] as $img) {
                $path = $img->store('products', 'public');

                ProductMedia::create([
                    'product_id' => $product->id,
                    'color_id' => $color->id,
                    'type' => 'image',
                    'path' => '/storage/' . $path,
                    'order' => $order++
                ]);
            }
        }
    }


    // =========================
    // ✅ VIDEO
    // =========================
    if ($request->hasFile('video')) {

        $product->media()
            ->where('type', 'video')
            ->delete();

        $path = $request->file('video')->store('products', 'public');

        $product->media()->create([
            'type' => 'video',
            'path' => '/storage/' . $path,
            'order' => 999,
            'color_id' => null
        ]);
    }
    else {
        $product->media()
            ->where('type', 'video')
            ->delete();
    }
}

    /* ================= COLORS ================= */
    private function storeColors(Product $product, array $colors)
    {
        $map = [];

    foreach ($colors as $tempId => $color) {

        $created = $product->colors()->create([
            'name' => $color['name'],
            'price' => $color['price']
        ]);

        $map[$tempId] = $created->id;
    }

    return $map;
    }

    /* ================= MEDIA ================= */
    private function storeMedia(Product $product, array $data, array $colorMap = [])
{
    // GENERAL IMAGES
    if (!empty($data['images'])) {
        foreach ($data['images'] as $index => $img) {

            $path = $img->store('products', 'public');

            $product->media()->create([
                'type' => 'image',
                'path' => '/storage/' . $path,
                'order' => $index,
                'color_id' => null
            ]);
        }
    }

    // COLOR IMAGES
    if (!empty($data['color_images'])) {

        foreach ($data['color_images'] as $tempId => $images) {

            $colorId = $colorMap[$tempId] ?? null;

            if (!$colorId) continue;

            foreach ($images as $i => $img) {

                $path = $img->store('products', 'public');

                ProductMedia::create([
                    'product_id' => $product->id,
                    'color_id' => $colorId,
                    'type' => 'image',
                    'path' => '/storage/' . $path,
                    'order' => $i
                ]);
            }
        }
    }

    // VIDEO
    if (!empty($data['video'])) {

        $path = $data['video']->store('products', 'public');

        $product->media()->create([
            'type' => 'video',
            'path' => '/storage/' . $path,
            'order' => 999,
            'color_id' => null
        ]);
    }
}

    /* ================= DELETE MEDIA ================= */
    private function deleteProductMedia(Product $product)
    {
        $allMedia = ProductMedia::where('product_id', $product->id)->get();

        foreach ($allMedia as $media) {
            $file = str_replace('/storage/', '', $media->path);
            Storage::disk('public')->delete($file);
        }

        ProductMedia::where('product_id', $product->id)->delete();
    }

    /* ================= SIZES ================= */
    private function syncSizes(Product $product, array $sizes)
    {
        $product->sizes()->sync($sizes);
    }

    /* ================= DETAILS ================= */
    private function storeDetails(Product $product, array $details)
    {
        foreach ($details as $detail) {
            $product->details()->create([
                'key' => $detail['key'],
                'value' => $detail['value']
            ]);
        }
    }

    /* ================= SIZE GUIDE ================= */
    private function storeSizeGuide(Product $product, $sizeGuide)
    {
        if (!$sizeGuide) return;

        SizeGuide::updateOrCreate(
            ['product_id' => $product->id],
            ['sizes' => $sizeGuide]
        );
    }

    /* ================= PRICE ================= */
    private function updateProductPrice(Product $product, array $data)
    {
        $minPrice = $product->colors()->min('price');

        $product->update([
            'price' => $minPrice ?? ($data['price'] ?? 0)
        ]);
    }

    /* ================= DELETE ================= */
    public function delete(Product $product)
    {
        $this->deleteProductMedia($product);
        $product->delete();

        Cache::flush();
    }
}
