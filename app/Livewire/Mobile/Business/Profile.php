<?php

namespace App\Livewire\Mobile\Business;

use App\Models\Business;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Profile extends Component
{

    use WithFileUploads;
    public Business $business;
    public string $name = '';
   public $logo_path = null;
    public $cover_path = null;
    public ?string $phone = '';
    public ?string $email = '';
    public ?string $description = '';
    public ?string $address = '';
    public float|string|null $lat = null;
    public float|string|null $lng = null;
    public float|string|null $delivery_fee = null;
    public ?int $estimated_minutes = 35;

    public function mount(): void
    {
        $this->business = Business::where('user_id', auth()->id())->firstOrFail();
        $this->fill($this->business->only([
            'name', 'phone', 'email', 'description', 'address', 'logo_path', 'cover_path',
            'lat', 'lng', 'delivery_fee', 'estimated_minutes',
        ]));
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'address' => ['required', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric', ],
            'lng' => ['nullable', 'numeric',],
            'delivery_fee' => ['required', 'numeric', 'min:0'],
            'estimated_minutes' => ['required', 'integer', 'min:5', 'max:240'],
            'logo_path' => ['nullable', 'image', 'max:2048'],
        ]);

          $imagePath = $business->logo_path ?? null;

        if ($this->logo_path) {
            $newImagePath = $this->logo_path->store(
                'businesses/'.$this->business->id,
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

        $data['slug'] = Str::slug($data['name']).'-'.$this->business->id;

        $this->business->fill([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'],
            'lat' => $data['lat'] ?? null,
            'lng' => $data['lng'] ?? null,
            'delivery_fee' => $data['delivery_fee'],
            'estimated_minutes' => $data['estimated_minutes'],
            'description' => $data['description'] ?? null,
            'slug' => $data['slug'],
            'logo_path' => $imagePath,
        ]);
         $this->business->save();

       

        session()->flash('success', 'Perfil del negocio actualizado.');
    }

    public function render()
    {
        return view('livewire.mobile.business.profile')
            ->layout('components.mobile.app', ['title' => 'Perfil del negocio', 'activeTab' => 'business']);
    }
}
