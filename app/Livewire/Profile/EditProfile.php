<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EditProfile extends Component
{
    use WithFileUploads;

    // Storage disk - uses config('filesystems.default') which can be 'public' or 's3'
    protected function getStorageDisk(): string
    {
        return config('filesystems.default', 'public');
    }

    /**
     * Helper para obtener URL de archivo de manera segura
     */
    public function getStorageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        
        try {
            return Storage::disk($this->getStorageDisk())->url($path);
        } catch (\Exception $e) {
            // Fallback to public disk
            return Storage::disk('public')->url($path);
        }
    }

    // User data
    public $email;
    
    // Common profile data
    public $first_name;
    public $last_name;
    public $phone;
    
    // Photo upload
    public $photo;
    public $currentPhoto;
    
    // Profile photo (personal, for all users)
    public $profile_photo;
    public $currentProfilePhoto;
    
    // Admin specific
    public $company_name;
    public $brand_logo;
    public $currentBrandLogo;
    
    // Coach specific
    public $license_number;
    public $experience_years;
    
    // Referee specific
    public $referee_type;
    public $availability = [];
    
    // Player specific
    public $birth_date;
    public $jersey_number;
    public $position;
    
    // Password change
    public $current_password;
    public $password;
    public $password_confirmation;
    
    // UI state
    public $activeTab = 'profile';
    public $showDeleteModal = false;
    public $delete_password;
    
    protected $listeners = ['refreshProfile' => '$refresh'];

    public function mount()
    {
        $user = Auth::user();
        $this->email = $user->email;
        $this->currentProfilePhoto = $user->profile_photo ?? null;
        
        $profile = $user->userable;
        
        if ($profile) {
            // Common fields
            $this->first_name = $profile->first_name ?? '';
            $this->last_name = $profile->last_name ?? '';
            $this->phone = $profile->phone ?? '';
            
            // Role-specific fields
            switch ($user->user_type) {
                case 'admin':
                    $this->company_name = $profile->company_name ?? '';
                    $this->currentBrandLogo = $profile->brand_logo ?? null;
                    break;
                    
                case 'league_manager':
                    // League manager uses common fields
                    break;
                    
                case 'coach':
                    $this->license_number = $profile->license_number ?? '';
                    $this->experience_years = $profile->experience_years ?? '';
                    break;
                    
                case 'referee':
                    $this->referee_type = $profile->referee_type ?? 'main';
                    $this->availability = $profile->availability ?? [];
                    break;
                    
                case 'player':
                    $this->birth_date = $profile->birth_date?->format('Y-m-d') ?? '';
                    $this->jersey_number = $profile->jersey_number ?? '';
                    $this->position = $profile->position ?? '';
                    $this->currentPhoto = $profile->photo ?? null;
                    break;
            }
        }
    }

    public function getUserType()
    {
        return Auth::user()->user_type;
    }

    public function getUserTypeLabel()
    {
        $types = [
            'admin' => 'Administrador',
            'league_manager' => 'Encargado de Liga',
            'coach' => 'Entrenador',
            'referee' => 'Árbitro',
            'player' => 'Jugador',
        ];
        
        return $types[$this->getUserType()] ?? 'Usuario';
    }

    protected function getProfileRules()
    {
        $user = Auth::user();
        $rules = [
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
        ];
        
        switch ($user->user_type) {
            case 'admin':
                $rules['company_name'] = ['nullable', 'string', 'max:255'];
                $rules['brand_logo'] = ['nullable', 'image', 'max:2048'];
                break;
                
            case 'coach':
                $rules['license_number'] = ['nullable', 'string', 'max:50'];
                $rules['experience_years'] = ['nullable', 'integer', 'min:0', 'max:50'];
                break;
                
            case 'referee':
                $rules['referee_type'] = ['required', 'in:main,assistant,fourth'];
                break;
                
            case 'player':
                $rules['birth_date'] = ['nullable', 'date', 'before:today'];
                $rules['jersey_number'] = ['nullable', 'integer', 'min:1', 'max:99'];
                $rules['position'] = ['nullable', 'in:goalkeeper,defender,midfielder,forward'];
                $rules['photo'] = ['nullable', 'image', 'max:2048'];
                break;
        }
        
        return $rules;
    }

    protected function getPasswordRules()
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function updateProfile()
    {
        $this->validate($this->getProfileRules());
        
        $user = Auth::user();
        $profile = $user->userable;
        
        // Update email
        if ($user->email !== $this->email) {
            $user->email = $this->email;
            $user->email_verified_at = null;
        }
        
        // Update profile photo (personal photo for sidebar)
        if ($this->profile_photo) {
            // Delete old photo
            if ($this->currentProfilePhoto) {
                Storage::disk($this->getStorageDisk())->delete($this->currentProfilePhoto);
            }
            $path = $this->profile_photo->store('profile-photos', $this->getStorageDisk());
            $user->profile_photo = $path;
            $this->currentProfilePhoto = $path;
        }
        
        $user->save();
        
        if ($profile) {
            // Common fields
            $profile->first_name = $this->first_name;
            $profile->last_name = $this->last_name;
            $profile->phone = $this->phone;
            
            // Role-specific fields
            switch ($user->user_type) {
                case 'admin':
                    $profile->company_name = $this->company_name;
                    
                    if ($this->brand_logo) {
                        // Delete old logo
                        if ($this->currentBrandLogo) {
                            Storage::disk($this->getStorageDisk())->delete($this->currentBrandLogo);
                        }
                        $path = $this->brand_logo->store('brand-logos', $this->getStorageDisk());
                        $profile->brand_logo = $path;
                        $this->currentBrandLogo = $path;
                    }
                    break;
                    
                case 'coach':
                    $profile->license_number = $this->license_number;
                    $profile->experience_years = $this->experience_years ?: null;
                    break;
                    
                case 'referee':
                    $profile->referee_type = $this->referee_type;
                    $profile->availability = $this->availability;
                    break;
                    
                case 'player':
                    $profile->birth_date = $this->birth_date ?: null;
                    $profile->jersey_number = $this->jersey_number ?: null;
                    $profile->position = $this->position ?: null;
                    
                    if ($this->photo) {
                        // Delete old photo
                        if ($this->currentPhoto) {
                            Storage::disk($this->getStorageDisk())->delete($this->currentPhoto);
                        }
                        $path = $this->photo->store('player-photos', $this->getStorageDisk());
                        $profile->photo = $path;
                        $this->currentPhoto = $path;
                    }
                    break;
            }
            
            $profile->save();
        }
        
        $this->brand_logo = null;
        $this->photo = null;
        $this->profile_photo = null;
        
        session()->flash('profile-updated', true);
        $this->dispatch('notify', type: 'success', message: 'Perfil actualizado correctamente.');
    }

    public function updatePassword()
    {
        $this->validate($this->getPasswordRules());
        
        $user = Auth::user();
        $user->password = Hash::make($this->password);
        $user->save();
        
        $this->reset(['current_password', 'password', 'password_confirmation']);
        
        session()->flash('password-updated', true);
        $this->dispatch('notify', type: 'success', message: 'Contraseña actualizada correctamente.');
    }

    public function confirmDeleteAccount()
    {
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->delete_password = '';
    }

    public function deleteAccount()
    {
        $this->validate([
            'delete_password' => ['required', 'current_password'],
        ]);
        
        $user = Auth::user();
        
        // Delete profile photo/logo if exists
        if ($user->user_type === 'player' && $this->currentPhoto) {
            Storage::disk($this->getStorageDisk())->delete($this->currentPhoto);
        }
        if ($user->user_type === 'admin' && $this->currentBrandLogo) {
            Storage::disk($this->getStorageDisk())->delete($this->currentBrandLogo);
        }
        
        // Delete userable profile first
        if ($user->userable) {
            $user->userable->delete();
        }
        
        // Logout and delete user
        Auth::logout();
        $user->delete();
        
        session()->invalidate();
        session()->regenerateToken();
        
        return redirect('/')->with('status', 'Tu cuenta ha sido eliminada.');
    }

    public function removePhoto()
    {
        $user = Auth::user();
        $profile = $user->userable;
        
        if ($user->user_type === 'player' && $this->currentPhoto) {
            Storage::disk($this->getStorageDisk())->delete($this->currentPhoto);
            $profile->photo = null;
            $profile->save();
            $this->currentPhoto = null;
            $this->dispatch('notify', type: 'success', message: 'Foto eliminada.');
        }
    }

    public function removeProfilePhoto()
    {
        $user = Auth::user();
        
        if ($this->currentProfilePhoto) {
            Storage::disk($this->getStorageDisk())->delete($this->currentProfilePhoto);
            $user->profile_photo = null;
            $user->save();
            $this->currentProfilePhoto = null;
            $this->dispatch('notify', type: 'success', message: 'Foto de perfil eliminada.');
        }
    }

    public function removeBrandLogo()
    {
        $user = Auth::user();
        $profile = $user->userable;
        
        if ($user->user_type === 'admin' && $this->currentBrandLogo) {
            Storage::disk($this->getStorageDisk())->delete($this->currentBrandLogo);
            $profile->brand_logo = null;
            $profile->save();
            $this->currentBrandLogo = null;
            $this->dispatch('notify', type: 'success', message: 'Logo eliminado.');
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.profile.edit-profile')
            ->layout('layouts.app', ['title' => 'Mi Perfil']);
    }
}
