<?php

namespace App\Livewire\Mobile\Driver;

use App\Models\DriverProfile;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
// use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

// #[Layout('components.layouts.mobile')]
class Profile extends Component
{
    use WithFileUploads;

    public DriverProfile $driver;

    public string $name = '';

    public ?string $email = null;

    public ?string $phone = null;

    public ?string $vehicle_type = null;

    public ?string $vehicle_model = null;

    public ?string $vehicle_color = null;

    public ?string $plate_number = null;

    public ?string $license_number = null;

    public bool $is_available = false;

    public float|string|null $lat = null;

    public float|string|null $lng = null;

    public TemporaryUploadedFile|string|null $profile_photo = null;

    public ?string $existingProfilePhoto = null;

    public function mount(): void
    {
        abort_unless(Auth::check(), 401);

        abort_unless(
            Auth::user()->role === 'driver',
            403,
            'Esta sección es solamente para conductores.'
        );

        $this->driver = DriverProfile::query()->firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'phone' => Auth::user()->phone ?? null,
                'is_available' => false,
            ]
        );

        $this->loadProfile();
    }

    protected function loadProfile(): void
    {
        $user = Auth::user();

        $this->name = $user->name ?? '';
        $this->email = $user->email;
        $this->phone = $this->driver->phone ?? $user->phone;

        $this->vehicle_type = $this->driver->vehicle_type;
        $this->vehicle_model = $this->driver->vehicle_model;
        $this->vehicle_color = $this->driver->vehicle_color;
        $this->plate_number = $this->driver->plate_number;
        $this->license_number = $this->driver->license_number;

        $this->is_available = (bool) $this->driver->is_available;

        $this->lat = $this->driver->lat;
        $this->lng = $this->driver->lng;

        $this->existingProfilePhoto = $this->driver->profile_photo;
        $this->profile_photo = null;
    }

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:120',
            ],

            'email' => [
                'nullable',
                'email',
                'max:160',
                Rule::unique('users', 'email')->ignore(Auth::id()),
            ],

            'phone' => [
                'required',
                'string',
                'max:30',
            ],

            'vehicle_type' => [
                'required',
                'string',
                'max:50',
            ],

            'vehicle_model' => [
                'required',
                'string',
                'max:100',
            ],

            'vehicle_color' => [
                'nullable',
                'string',
                'max:50',
            ],

            'plate_number' => [
                'required',
                'string',
                'max:30',
            ],

            'license_number' => [
                'nullable',
                'string',
                'max:60',
            ],

            'is_available' => [
                'boolean',
            ],

            'lat' => [
                'nullable',
                'numeric',
                'between:-90,90',
            ],

            'lng' => [
                'nullable',
                'numeric',
                'between:-180,180',
            ],

            'profile_photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ];
    }

    public function updated(string $property): void
    {
        if ($property === 'profile_photo') {
            $this->validateOnly('profile_photo');
        }
    }

    public function save(): void
    {
        $validated = $this->validate();

        DB::transaction(function () use ($validated) {
            $user = Auth::user();

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'] ?? null,
            ]);

            $photoPath = $this->driver->profile_photo;

            if ($this->profile_photo instanceof TemporaryUploadedFile) {
                $newPhotoPath = $this->profile_photo->store(
                    'drivers/'.$this->driver->id,
                    'public'
                );

                if (
                    $photoPath &&
                    Storage::disk('public')->exists($photoPath)
                ) {
                    Storage::disk('public')->delete($photoPath);
                }

                $photoPath = $newPhotoPath;
            }

            $this->driver->update([
                'phone' => $validated['phone'],
                'vehicle_type' => $validated['vehicle_type'],
                'vehicle_model' => $validated['vehicle_model'],
                'vehicle_color' => $validated['vehicle_color'] ?? null,
                'plate_number' => strtoupper($validated['plate_number']),
                'license_number' => $validated['license_number'] ?? null,
                'is_available' => $validated['is_available'],
                'lat' => $validated['lat'] ?? null,
                'lng' => $validated['lng'] ?? null,
                'profile_photo' => $photoPath,
            ]);
        });

        $this->driver->refresh();
        $this->loadProfile();

        session()->flash(
            'success',
            'Perfil del conductor actualizado correctamente.'
        );
    }

    public function toggleAvailability(): void
    {
        $this->is_available = ! $this->is_available;

        $this->driver->update([
            'is_available' => $this->is_available,
        ]);

        $this->driver->refresh();

        session()->flash(
            'success',
            $this->is_available
                ? 'Ahora estás disponible para recibir órdenes.'
                : 'Ya no estás disponible para recibir órdenes.'
        );
    }

    public function removePhoto(): void
    {
        $photoPath = $this->driver->profile_photo;

        if (
            $photoPath &&
            Storage::disk('public')->exists($photoPath)
        ) {
            Storage::disk('public')->delete($photoPath);
        }

        $this->driver->update([
            'profile_photo' => null,
        ]);

        $this->profile_photo = null;
        $this->existingProfilePhoto = null;

        session()->flash(
            'success',
            'Foto eliminada correctamente.'
        );
    }

    #[Computed]
    public function activeOrdersCount(): int
    {
        return $this->driverOrdersQuery()
            ->whereIn('status', [
                'accepted',
                'ready_for_pickup',
                'picked_up',
            ])
            ->count();
    }

    #[Computed]
    public function deliveredOrdersCount(): int
    {
        return $this->driverOrdersQuery()
            ->where('status', 'delivered')
            ->count();
    }

    protected function driverOrdersQuery()
    {
        /*
         * Usa esta versión si orders.driver_id apunta a drivers.id.
         */
        return Order::query()
            ->where('driver_id', $this->driver->id);

        /*
         * Si orders.driver_id apunta a users.id, reemplaza el return anterior por:
         *
         * return Order::query()
         *     ->where('driver_id', Auth::id());
         */
    }

    public function render()
    {
        return view('livewire.mobile.driver.profile')
        ->layout('components.mobile.app', ['title' => 'Perfil del conductor', 'activeTab' => 'driver']);
    }
}