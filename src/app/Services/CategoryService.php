<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function getCategories()
    {
        $categories  = Category::all();

        return response()->json([
            'data' => $categories 
        ]);
    }
}
