<?php

namespace App\Services;

use App\Models\News;
use App\Models\Gallery;
use App\Models\Category;
use App\Models\ProdukHukum;

class CategoryService
{
    public function handleCategoryCreationOrUpdate(array $categoryData, int $categoryType = 1): int
    {
        if (!empty($categoryData['new_category'])) {
            $category = Category::create([
                'name' => $categoryData['new_category'],
                'category_type' => $categoryType
            ]);
            return $category->id;
        }

        if (!empty($categoryData['edit_category_id']) && !empty($categoryData['new_category_name'])) {
            $category = Category::find($categoryData['edit_category_id']);
            if ($category) {
                $category->name = $categoryData['new_category_name'];
                $category->save();
                return $category->id;
            }
        }

        return $categoryData['category_id'] ?? null;
    }

    public function handleCategoryDeletion(array $deletingCategoryIds, int $categoryType = 1): array
    {
        $deletionResults = [];

        foreach ($deletingCategoryIds as $deletingCategoryId) {
            $category = Category::find($deletingCategoryId);
            if (!$category) {
                continue;
            }

            $inUseCount = $this->getCategoryUsageCount($category->id, $categoryType);

            if ($inUseCount > 0) {
                $useTypeMessage = ($categoryType === 0)
                    ? "produk hukum"
                    : "news and gallery";

                $deletionResults[$category->name] = "Cannot delete: in use by {$inUseCount} {$useTypeMessage}";
            } else {
                $category->delete();
                $deletionResults[$category->name] = "Deleted successfully";
            }
        }

        return $deletionResults;
    }

    public function getCategoriesWithUsageCounts(int $categoryType = 1)
    {
        $categories = Category::where('category_type', $categoryType)->get();

        foreach ($categories as $category) {
            $category->usage_count = $this->getCategoryUsageCount($category->id, $categoryType);
        }

        return $categories;
    }

    public function getUnusedCategories(int $categoryType = 1)
    {
        $categories = $this->getCategoriesWithUsageCounts($categoryType);

        return $categories->filter(function ($category) {
            return $category->usage_count === 0;
        })->values();
    }

    private function getCategoryUsageCount(int $categoryId, int $categoryType = 1): int
    {
        if ($categoryType === 0) {
            // For ProdukHukum
            return ProdukHukum::where('category_id', $categoryId)->count();
        } else {
            // For News and Gallery
            $newsCount = News::where('category_id', $categoryId)->count();
            $galleryCount = Gallery::where('category_id', $categoryId)->count();
            return $newsCount + $galleryCount;
        }
    }
}
