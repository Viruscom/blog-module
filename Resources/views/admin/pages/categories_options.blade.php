<option value="{{ route('admin.category-page.pages', ['id' => $category->id]) }}" {{ (old('category_id') === $category->id || isset($productCategoryId) && $productCategoryId === $category->id) ? 'selected':'' }}>
    {{ $category->title }}
</option>
