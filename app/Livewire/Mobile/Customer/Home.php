<?php

namespace App\Livewire\Mobile\Customer;

use App\Models\Business;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

// #[Layout('components.layouts.mobile.app')]
#[Title('Inicio')]
class Home extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $selectedBusinessId = null;

    public string $sort = 'latest';

    public int $perPage = 12;

    /**
     * Cuando cambia el buscador, regresamos a la primera página.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Cuando cambia el negocio seleccionado, regresamos a la primera página.
     */
    public function updatedSelectedBusinessId(): void
    {
        $this->resetPage();
    }

    /**
     * Cuando cambia el orden, regresamos a la primera página.
     */
    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function selectBusiness(?int $businessId = null): void
    {
        $this->selectedBusinessId = $businessId;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset([
            'search',
            'selectedBusinessId',
            'sort',
        ]);

        $this->sort = 'latest';

        $this->resetPage();
    }

    /**
     * Agrega un producto al carrito guardado en la sesión.
     */
    public function addToCart(int $productId): void
    {
        $product = Product::query()
            ->whereKey($productId)
            ->where('is_active', true)
            ->firstOrFail();

        $cart = session()->get('cart', []);

        $currentBusinessId = session()->get('cart_business_id');

        /*
         * Un pedido normalmente pertenece a un solo negocio.
         * Si el usuario agrega un producto de otro negocio,
         * limpiamos el carrito anterior.
         */
        if (
            $currentBusinessId !== null &&
            (int) $currentBusinessId !== (int) $product->business_id
        ) {
            $cart = [];
        }

        $existingQuantity = $cart[$product->id]['quantity'] ?? 0;

        $cart[$product->id] = [
            'product_id' => $product->id,
            'business_id' => $product->business_id,
            'name' => $product->name,
            'price' => (float) $product->price,
            'quantity' => $existingQuantity + 1,
            'image' => $product->image
                ?? $product->image_path
                ?? null,
        ];

        session()->put('cart', $cart);
        session()->put('cart_business_id', $product->business_id);

        $this->dispatch(
            'cart-updated',
            count: $this->cartCount()
        );

        session()->flash(
            'success',
            "{$product->name} fue agregado al carrito."
        );
    }

    public function cartCount(): int
    {
        return collect(session()->get('cart', []))
            ->sum(fn (array $item): int => (int) ($item['quantity'] ?? 0));
    }

    #[Computed]
    public function businesses()
    {
        return Business::query()
            ->where('is_active', true)
            ->where('is_open', true)
            ->withCount([
                'products' => fn (Builder $query) => $query
                    ->where('is_active', true),
            ])
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function featuredBusinesses()
    {
        return Business::query()
            ->where('is_active', true)
            ->where('is_open', true)
            ->withCount([
                'products' => fn (Builder $query) => $query
                    ->where('is_active', true),
            ])
            ->orderByDesc('products_count')
            ->limit(6)
            ->get();
    }

    #[Computed]
    public function products()
    {
        return Product::query()
            ->with('business')
            ->where('is_active', true)
            ->whereHas('business', function (Builder $query): void {
                $query
                    ->where('is_active', true)
                    ->where('is_open', true);
            })
            ->when(
                $this->selectedBusinessId,
                fn (Builder $query, int $businessId) => $query
                    ->where('business_id', $businessId)
            )
            ->when(
                trim($this->search) !== '',
                function (Builder $query): void {
                    $search = trim($this->search);

                    $query->where(function (Builder $subquery) use ($search): void {
                        $subquery
                            ->where('name', 'ilike', "%{$search}%")
                            ->orWhere('description', 'ilike', "%{$search}%");
                    });
                }
            )
            ->when(
                $this->sort === 'price_low',
                fn (Builder $query) => $query
                    ->orderBy('price')
            )
            ->when(
                $this->sort === 'price_high',
                fn (Builder $query) => $query
                    ->orderByDesc('price')
            )
            ->when(
                $this->sort === 'name',
                fn (Builder $query) => $query
                    ->orderBy('name')
            )
            ->when(
                $this->sort === 'latest',
                fn (Builder $query) => $query
                    ->latest()
            )
            ->paginate($this->perPage);
    }

    public function render()
    {
        abort_unless(Auth::check(), 401);
        abort_unless(Auth::user()->role === 'customer', 403);

        return view('livewire.mobile.customer.home')
          ->layout('components.mobile.app', ['title' => 'Inicio', 'activeTab' => 'inicio']);
    }
}