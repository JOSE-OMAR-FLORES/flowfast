<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    @if($currentProfilePhoto)
                        <img src="{{ $this->getStorageUrl($currentProfilePhoto) }}" alt="Foto de perfil" class="h-20 w-20 rounded-full object-cover border-4 border-indigo-100">
                    @else
                        <div class="h-20 w-20 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                            {{ strtoupper(substr($first_name, 0, 1) . substr($last_name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $first_name }} {{ $last_name }}</h1>
                    <p class="text-sm text-gray-500 flex items-center mt-1">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ $this->getUserTypeLabel() }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button wire:click="setActiveTab('profile')" 
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'profile' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <svg class="w-5 h-5 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Información Personal
                    </button>
                    <button wire:click="setActiveTab('security')" 
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'security' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <svg class="w-5 h-5 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Seguridad
                    </button>
                    <button wire:click="setActiveTab('danger')" 
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'danger' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <svg class="w-5 h-5 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Zona de Peligro
                    </button>
                </nav>
            </div>
        </div>

        <!-- Profile Tab -->
        @if($activeTab === 'profile')
        <form wire:submit="updateProfile" class="space-y-6">
            <!-- Basic Information Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Información Básica</h3>
                    <p class="text-sm text-gray-500 mt-1">Actualiza tu información personal y de contacto.</p>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <input type="email" wire:model="email" id="email" 
                                class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        @error('email') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Name Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                            <input type="text" wire:model="first_name" id="first_name" 
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('first_name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Apellido</label>
                            <input type="text" wire:model="last_name" id="last_name" 
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('last_name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <input type="text" wire:model="phone" id="phone" placeholder="+502 1234 5678"
                                class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        @error('phone') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Profile Photo (Personal) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Foto de Perfil</label>
                        <p class="text-xs text-gray-500 mb-3">Esta foto aparecerá en tu menú de usuario en el sidebar.</p>
                        <div class="flex items-center space-x-6">
                            @if($currentProfilePhoto)
                                <div class="relative">
                                    <img src="{{ $this->getStorageUrl($currentProfilePhoto) }}" alt="Foto actual" class="h-20 w-20 rounded-full object-cover border-4 border-indigo-100">
                                    <button type="button" wire:click="removeProfilePhoto" 
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-lg hover:bg-red-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <div class="h-20 w-20 rounded-full bg-gray-200 flex items-center justify-center">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <label for="profile_photo" class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                    <div class="flex flex-col items-center justify-center py-4">
                                        <svg class="w-6 h-6 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-sm text-gray-500"><span class="font-medium text-indigo-600">Subir foto</span></p>
                                        <p class="text-xs text-gray-400">PNG, JPG hasta 2MB</p>
                                    </div>
                                    <input type="file" wire:model="profile_photo" id="profile_photo" class="hidden" accept="image/*">
                                </label>
                                @if($profile_photo)
                                    <p class="text-sm text-green-600 mt-2">✓ {{ $profile_photo->getClientOriginalName() }}</p>
                                @endif
                                @error('profile_photo') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role-specific Fields -->
            @if($this->getUserType() === 'admin')
            <!-- Admin Fields -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Información de Empresa
                    </h3>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Empresa/Organización</label>
                        <input type="text" wire:model="company_name" id="company_name" placeholder="Mi Liga Deportiva"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('company_name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Logo de la Marca</label>
                        <div class="flex items-center space-x-6">
                            @if($currentBrandLogo)
                                <div class="relative">
                                    <img src="{{ $this->getStorageUrl($currentBrandLogo) }}" alt="Logo actual" class="h-24 w-24 rounded-lg object-cover border border-gray-200">
                                    <button type="button" wire:click="removeBrandLogo" 
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-lg hover:bg-red-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endif
                            <div class="flex-1">
                                <label for="brand_logo" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <p class="text-sm text-gray-500"><span class="font-medium text-indigo-600">Subir logo</span> o arrastrar</p>
                                        <p class="text-xs text-gray-400">PNG, JPG hasta 2MB</p>
                                    </div>
                                    <input type="file" wire:model="brand_logo" id="brand_logo" class="hidden" accept="image/*">
                                </label>
                                @if($brand_logo)
                                    <p class="text-sm text-green-600 mt-2">✓ {{ $brand_logo->getClientOriginalName() }}</p>
                                @endif
                                @error('brand_logo') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($this->getUserType() === 'coach')
            <!-- Coach Fields -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Información de Entrenador
                    </h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="license_number" class="block text-sm font-medium text-gray-700 mb-1">Número de Licencia</label>
                            <input type="text" wire:model="license_number" id="license_number" placeholder="LIC-2024-001"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('license_number') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="experience_years" class="block text-sm font-medium text-gray-700 mb-1">Años de Experiencia</label>
                            <input type="number" wire:model="experience_years" id="experience_years" min="0" max="50"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('experience_years') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($this->getUserType() === 'referee')
            <!-- Referee Fields -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-yellow-50 to-amber-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        Información de Árbitro
                    </h3>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label for="referee_type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Árbitro</label>
                        <select wire:model="referee_type" id="referee_type"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="main">Árbitro Principal</option>
                            <option value="assistant">Árbitro Asistente</option>
                            <option value="fourth">Cuarto Árbitro</option>
                        </select>
                        @error('referee_type') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            @endif

            @if($this->getUserType() === 'player')
            <!-- Player Fields -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-cyan-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Información de Jugador
                    </h3>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Photo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Foto de Perfil</label>
                        <div class="flex items-center space-x-6">
                            @if($currentPhoto)
                                <div class="relative">
                                    <img src="{{ $this->getStorageUrl($currentPhoto) }}" alt="Foto actual" class="h-24 w-24 rounded-full object-cover border-4 border-gray-200">
                                    <button type="button" wire:click="removePhoto" 
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-lg hover:bg-red-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <div class="h-24 w-24 rounded-full bg-gray-200 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <label for="photo" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-sm text-gray-500"><span class="font-medium text-indigo-600">Subir foto</span> o arrastrar</p>
                                        <p class="text-xs text-gray-400">PNG, JPG hasta 2MB</p>
                                    </div>
                                    <input type="file" wire:model="photo" id="photo" class="hidden" accept="image/*">
                                </label>
                                @if($photo)
                                    <p class="text-sm text-green-600 mt-2">✓ {{ $photo->getClientOriginalName() }}</p>
                                @endif
                                @error('photo') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento</label>
                            <input type="date" wire:model="birth_date" id="birth_date"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('birth_date') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="jersey_number" class="block text-sm font-medium text-gray-700 mb-1">Número de Camiseta</label>
                            <input type="number" wire:model="jersey_number" id="jersey_number" min="1" max="99"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('jersey_number') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Posición</label>
                            <select wire:model="position" id="position"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Seleccionar...</option>
                                <option value="goalkeeper">Portero</option>
                                <option value="defender">Defensa</option>
                                <option value="midfielder">Mediocampista</option>
                                <option value="forward">Delantero</option>
                            </select>
                            @error('position') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Save Button -->
            <div class="flex justify-end">
                <button type="submit" 
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-75 cursor-not-allowed">
                    <svg wire:loading wire:target="updateProfile" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="updateProfile">Guardar Cambios</span>
                    <span wire:loading wire:target="updateProfile">Guardando...</span>
                </button>
            </div>
        </form>
        @endif

        <!-- Security Tab -->
        @if($activeTab === 'security')
        <form wire:submit="updatePassword" class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Cambiar Contraseña</h3>
                    <p class="text-sm text-gray-500 mt-1">Asegúrate de usar una contraseña segura de al menos 8 caracteres.</p>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña Actual</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input type="password" wire:model="current_password" id="current_password" 
                                class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        @error('current_password') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña</label>
                            <input type="password" wire:model="password" id="password" 
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('password') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
                            <input type="password" wire:model="password_confirmation" id="password_confirmation" 
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" 
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-75 cursor-not-allowed">
                    <svg wire:loading wire:target="updatePassword" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="updatePassword">Actualizar Contraseña</span>
                    <span wire:loading wire:target="updatePassword">Actualizando...</span>
                </button>
            </div>
        </form>

        <!-- Session Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-6">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Información de Sesión</h3>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Sesión Actual</p>
                        <p class="text-sm text-gray-500">Último acceso: {{ now()->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="flex items-center text-green-500">
                        <span class="h-2 w-2 bg-green-500 rounded-full mr-2"></span>
                        Activa
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Danger Zone Tab -->
        @if($activeTab === 'danger')
        <div class="bg-white rounded-xl shadow-sm border border-red-200 overflow-hidden">
            <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                <h3 class="text-lg font-semibold text-red-800 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Zona de Peligro
                </h3>
            </div>
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h4 class="text-base font-medium text-gray-900">Eliminar Cuenta</h4>
                        <p class="text-sm text-gray-500 mt-1">
                            Una vez eliminada tu cuenta, todos tus datos serán borrados permanentemente. Esta acción no se puede deshacer.
                        </p>
                    </div>
                    <button type="button" wire:click="confirmDeleteAccount"
                        class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-lg text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        Eliminar Cuenta
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Delete Account Modal -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="cancelDelete"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            ¿Eliminar cuenta permanentemente?
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Esta acción eliminará permanentemente tu cuenta y todos los datos asociados. Esta acción no se puede deshacer.
                            </p>
                        </div>
                        <div class="mt-4">
                            <label for="delete_password" class="block text-sm font-medium text-gray-700">
                                Ingresa tu contraseña para confirmar
                            </label>
                            <input type="password" wire:model="delete_password" id="delete_password"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"
                                placeholder="Tu contraseña actual">
                            @error('delete_password') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                    <button type="button" wire:click="deleteAccount"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto sm:text-sm"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="deleteAccount">Sí, Eliminar Cuenta</span>
                        <span wire:loading wire:target="deleteAccount">Eliminando...</span>
                    </button>
                    <button type="button" wire:click="cancelDelete"
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
