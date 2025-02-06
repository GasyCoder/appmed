<?php
namespace App\Livewire\Admin;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class CategoriesManagement extends Component
{
    use WithPagination;

    public $name;
    public $description;
    public $parent_id;
    public $showCategoryModal = false;
    public $editingCategory = null;

    protected $rules = [
        'name' => 'required|min:3',
        'description' => 'required',
        'parent_id' => 'nullable|exists:categories,id'
    ];

    public function createCategory()
    {
        $this->validate();

        Category::create([
            'name' => $this->name,
            'description' => $this->description,
            'parent_id' => $this->parent_id
        ]);

        $this->reset(['name', 'description', 'parent_id', 'showCategoryModal']);
        $this->dispatch('notify', ['message' => 'Catégorie créée avec succès', 'type' => 'success']);
    }

    public function editCategory(Category $category)
    {
        $this->editingCategory = $category;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->parent_id = $category->parent_id;
        $this->showCategoryModal = true;
    }

    public function updateCategory()
    {
        $this->validate();

        $this->editingCategory->update([
            'name' => $this->name,
            'description' => $this->description,
            'parent_id' => $this->parent_id
        ]);

        $this->reset(['name', 'description', 'parent_id', 'showCategoryModal', 'editingCategory']);
        $this->dispatch('notify', ['message' => 'Catégorie mise à jour avec succès', 'type' => 'success']);
    }

    public function render()
    {
        return view('livewire.admin.categories-management', [
            'categories' => Category::with('parent', 'children')->paginate(10),
            'parentCategories' => Category::whereNull('parent_id')->get()
        ]);
    }
}
