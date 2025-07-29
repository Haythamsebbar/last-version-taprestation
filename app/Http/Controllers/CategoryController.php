<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Récupère les sous-catégories d'une catégorie donnée.
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function getSubcategories(Category $category): JsonResponse
    {
        return response()->json($category->children);
    }

    /**
     * Récupère toutes les catégories principales (sans parent).
     *
     * @return JsonResponse
     */
    public function getMainCategories(): JsonResponse
    {
        $categories = Category::whereNull('parent_id')->orderBy('name')->get();
        return response()->json($categories);
    }

    /**
     * Récupère une catégorie spécifique avec ses sous-catégories.
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function getCategory(Category $category): JsonResponse
    {
        $category->load('children');
        return response()->json($category);
    }
}