<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, seed default categories based on existing data
        $categories = ['makanan' => 'Makanan', 'minuman' => 'Minuman', 'tambahan' => 'Tambahan'];
        $categoryIds = [];
        
        foreach ($categories as $key => $name) {
            $id = DB::table('categories')->insertGetId([
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $categoryIds[$key] = $id;
        }

        // Add category_id column
        Schema::table('menus', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('name')->constrained()->cascadeOnDelete();
        });

        // Migrate existing category strings to category_id
        foreach ($categoryIds as $oldCategory => $newCategoryId) {
            DB::table('menus')
                ->where('category', $oldCategory)
                ->update(['category_id' => $newCategoryId]);
        }

        // Make category_id required and drop old columns
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn(['category', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->string('category')->default('makanan');
            $table->boolean('is_available')->default(true);
        });

        // Migrate back
        $categories = DB::table('categories')->get();
        foreach ($categories as $category) {
            $oldKey = strtolower($category->name);
            DB::table('menus')
                ->where('category_id', $category->id)
                ->update(['category' => $oldKey]);
        }

        Schema::table('menus', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
