<?php

namespace App\Livewire\Mobile\Business;

use App\Models\Business;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Products extends Component
{
     use WithFileUploads;

    public $image =null;
    public ?string $existingImage = null;
    public Business $business;
    public ?int $editingId = null;
    public string $name = '';
    public string $description = '';
    public float|string $price = '';
    public int|string $stock = 0;
    public bool $active = true;

    public function mount(): void
    {
        $this->business = Business::where('user_id', auth()->id())->firstOrFail();
    }

    public function edit(Product $product): void
    {
        abort_unless(
            (int) $product->business_id === (int) $this->business->id,
            403
        );

        $this->resetValidation();

        $this->editingId = $product->id;
        $this->name = $product->name ?? '';
        $this->description = $product->description;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->active = (bool) $product->is_active;

        // Ruta guardada actualmente en la base de datos.
        $this->existingImage = $product->image;

        // Debe quedar null hasta seleccionar un archivo nuevo.
        $this->image = null;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:5120'],
            'active' => ['boolean'],
        ]);

        $product = $this->editingId
            ? $this->business->products()
                ->whereKey($this->editingId)
                ->firstOrFail()
            : new Product();

        $imagePath = $product->image ?? null;

        if ($this->image) {
            $newImagePath = $this->image->store(
                'products/'.$this->business->id,
                'public'
            );

            if (
                $imagePath &&
                ! str_starts_with($imagePath, 'http') &&
                Storage::disk('public')->exists($imagePath)
            ) {
                Storage::disk('public')->delete($imagePath);
            }

            $imagePath = $newImagePath;
        }

        $product->fill([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'image' => $imagePath,
            'is_active' => $validated['active'],
        ]);

        $product->business_id = $this->business->id;
        $product->save();

        $this->resetForm();

        session()->flash(
            'success',
            $this->editingId
                ? 'Producto actualizado correctamente.'
                : 'Producto creado correctamente.'
        );
    }

    public function resetForm(): void
    {
        $this->reset([
            'editingId',
            'name',
            'description',
            'price',
            'stock',
            'image',
            'existingImage',
        ]);

        $this->active = true;
        $this->stock = 0;

        $this->resetValidation();
    }


    public function delete(Product $product): void
    {
        abort_unless($product->business_id === $this->business->id, 403);
        $product->delete();
    }

  

    public function render()
    {
        return view('livewire.mobile.business.products', [
            'products' => $this->business->products()->latest()->get(),
        ])->layout('components.mobile.app', ['title' => 'Productos', 'activeTab' => 'business']);
    }
}
