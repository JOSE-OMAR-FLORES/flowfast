<div class="min-h-screen bg-gradient-to-b from-[#071226] to-[#071b2a] flex items-center justify-center p-8">
    <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 gap-7 items-center">
        
        {{-- Columna izquierda: Información de Invitación --}}
        <section class="hidden md:block bg-[#0b1220] bg-opacity-95 rounded-[14px] shadow-2xl border border-white/5 p-8 h-full">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-cyan-400 to-blue-300 flex items-center justify-center text-[#042027] font-bold text-xl">FF</div>
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-white">FlowFast</h1>
                    <p class="text-sm text-slate-400 mt-1">Sistema de Gestión Deportiva</p>
                </div>
            </div>

            @if(!$error)
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-cyan-400 to-blue-500 rounded-full border border-cyan-400 shadow mb-4">
                    <span class="text-lg">{{ $roleIcon }}</span>
                    <span class="text-sm font-semibold text-[#042027]">{{ $roleLabel }}</span>
                </div>

                <p class="text-slate-400 text-sm mb-4">Has sido invitado para unirte a la plataforma FlowFast. Completa el registro para acceder a todas las funcionalidades.</p>

                @if($league || $team)
                    <div class="bg-white/5 border border-cyan-400 rounded-xl p-4 space-y-3 mb-4">
                        <p class="text-xs font-semibold text-cyan-400 uppercase tracking-wide">Detalles de la invitación</p>
                        @if($league)
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-white border border-cyan-400 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-cyan-400">Liga</p>
                                    <p class="text-base font-semibold text-white">{{ $league->name }}</p>
                                </div>
                            </div>
                        @endif
                        @if($team)
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-white border border-cyan-400 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-cyan-400">Equipo</p>
                                    <p class="text-base font-semibold text-white">{{ $team->name }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="h-1 bg-gradient-to-r from-transparent via-white/10 to-transparent rounded my-5"></div>
                <div class="text-sm text-slate-400">
                    <strong class="text-white">Beneficios:</strong>
                    <ul class="list-disc ml-5 mt-2">
                        <li>Gestión completa de ligas y equipos</li>
                        <li>Control financiero y reportes</li>
                        <li>Estadísticas en tiempo real</li>
                    </ul>
                </div>
            @else
                <p class="text-slate-400 text-sm mb-4">Lo sentimos, la invitación no es válida o ha expirado.</p>
            @endif
        </section>

        {{-- Columna derecha: Formulario --}}
        <div class="w-full">
            @if($error)
                <div class="bg-[#0b1220] bg-opacity-95 rounded-2xl shadow-2xl border border-red-400 p-10 text-center">
                    <div class="w-20 h-20 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-5">
                        <svg class="w-10 h-10 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-2">Invitación no válida</h2>
                    <p class="text-slate-400 text-sm mb-8 leading-relaxed">{{ $error }}</p>
                    <a href="/" class="inline-block px-6 py-3 bg-gradient-to-r from-cyan-400 to-blue-500 hover:from-blue-500 hover:to-blue-700 text-[#042027] text-sm font-semibold rounded-xl shadow-md transition-all">
                        Ir al inicio
                    </a>
                </div>
            @else
                <div class="bg-[#0b1220] bg-opacity-95 rounded-2xl shadow-2xl overflow-hidden border border-white/5">
                    <div class="px-8 py-8 text-center">
                        <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-cyan-400 to-blue-300 flex items-center justify-center text-[#042027] font-bold text-2xl mx-auto mb-4">FF</div>
                        <h2 class="text-2xl font-bold text-white mb-2">Aceptar Invitación</h2>
                        <p class="text-slate-400 text-sm mb-4">Completa tu registro para unirte</p>
                    </div>

                    <div class="p-8 pt-0">
                        <form wire:submit="accept" class="space-y-5">
                            <div>
                                <label for="name" class="block text-sm font-semibold text-cyan-400 mb-2">Nombre de usuario</label>
                                <input id="name" type="text" wire:model="name" required autofocus class="w-full px-4 py-2.5 border border-cyan-400 rounded-lg text-sm bg-white/5 text-white focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 transition-all placeholder:text-slate-400" placeholder="Nombre de usuario">
                                @error('name')
                                    <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="first_name" class="block text-sm font-semibold text-cyan-400 mb-2">Nombre</label>
                                    <input id="first_name" type="text" wire:model="first_name" required class="w-full px-4 py-2.5 border border-cyan-400 rounded-lg text-sm bg-white/5 text-white focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 transition-all placeholder:text-slate-400" placeholder="Tu nombre">
                                    @error('first_name')
                                        <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="last_name" class="block text-sm font-semibold text-cyan-400 mb-2">Apellido</label>
                                    <input id="last_name" type="text" wire:model="last_name" required class="w-full px-4 py-2.5 border border-cyan-400 rounded-lg text-sm bg-white/5 text-white focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 transition-all placeholder:text-slate-400" placeholder="Tu apellido">
                                    @error('last_name')
                                        <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-semibold text-cyan-400 mb-2">Teléfono (opcional)</label>
                                <input id="phone" type="tel" wire:model="phone" class="w-full px-4 py-2.5 border border-cyan-400 rounded-lg text-sm bg-white/5 text-white focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 transition-all placeholder:text-slate-400" placeholder="+52 123 456 7890">
                                @error('phone')
                                    <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-semibold text-cyan-400 mb-2">Correo electrónico</label>
                                <input id="email" type="email" wire:model="email" required class="w-full px-4 py-2.5 border border-cyan-400 rounded-lg text-sm bg-white/5 text-white focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 transition-all placeholder:text-slate-400" placeholder="tu@email.com">
                                @error('email')
                                    <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-semibold text-cyan-400 mb-2">Contraseña</label>
                                <input id="password" type="password" wire:model="password" required class="w-full px-4 py-2.5 border border-cyan-400 rounded-lg text-sm bg-white/5 text-white focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 transition-all placeholder:text-slate-400" placeholder="Mínimo 8 caracteres">
                                @error('password')
                                    <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-semibold text-cyan-400 mb-2">Confirmar contraseña</label>
                                <input id="password_confirmation" type="password" wire:model="passwordConfirmation" required class="w-full px-4 py-2.5 border border-cyan-400 rounded-lg text-sm bg-white/5 text-white focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 transition-all placeholder:text-slate-400" placeholder="Repite tu contraseña">
                            </div>

                            <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-cyan-400 to-blue-500 hover:from-blue-500 hover:to-blue-700 text-[#042027] text-base font-bold rounded-lg shadow-md border border-cyan-400 transition-all">
                                Crear cuenta y unirme
                            </button>
                        </form>

                        <div class="mt-6 text-center">
                            <p class="text-sm text-cyan-400">
                                Ya tienes una cuenta? 
                                <a href="{{ route('login') }}" class="font-bold underline hover:text-white">Inicia sesión</a>
                            </p>
                        </div>

                        <p class="text-center text-xs text-cyan-400 mt-4 leading-relaxed">
                            Al registrarte, aceptas nuestros 
                            <a href="#" class="underline font-semibold hover:text-white">términos y condiciones</a>
                        </p>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
