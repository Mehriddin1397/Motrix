<section>
    <header>
        <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-50">
            Profil ma'lumotlarini tahrirlash
        </h2>

        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
            Hisobingizning asosiy ma'lumotlari, telefon raqami va ijtimoiy tarmoq havolalarini yangilang.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Avatar yuklash --}}
        <div x-data="avatarPicker('{{ $user->avatar_url }}')" class="flex flex-col sm:flex-row items-center sm:items-start gap-4 p-4 rounded-xl bg-zinc-50 border border-zinc-200/80 dark:bg-zinc-800/40 dark:border-zinc-800">
            <div class="relative shrink-0">
                <div class="h-20 w-20 overflow-hidden rounded-full ring-4 ring-amber-500/20 shadow-md bg-zinc-200 dark:bg-zinc-700">
                    <template x-if="preview">
                        <img 
                            :src="preview" 
                            alt="{{ $user->name }}" 
                            referrerpolicy="no-referrer"
                            class="h-full w-full object-cover"
                            @error="preview = null"
                        >
                    </template>
                    <template x-if="!preview">
                        <span class="flex h-full w-full items-center justify-center bg-gradient-to-br from-amber-400 to-amber-600 text-2xl font-black text-white">
                            {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                        </span>
                    </template>
                </div>
                
                {{-- Rasm yuklash kamera belgisi --}}
                <label for="avatar" class="absolute -bottom-1 -right-1 flex h-7 w-7 cursor-pointer items-center justify-center rounded-full bg-amber-500 text-zinc-950 shadow-md hover:bg-amber-400 transition" title="Rasm yuklash">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </label>
                <input 
                    id="avatar" 
                    name="avatar" 
                    type="file" 
                    accept="image/png,image/jpeg,image/webp" 
                    @change="onFileChange($event)" 
                    class="hidden"
                >
            </div>

            <div class="flex-1 text-center sm:text-left space-y-1.5">
                <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Profil rasmi (Avatar)</div>
                <div class="text-xs text-zinc-500 dark:text-zinc-400">PNG, JPG yoki WEBP formatida (avtomatik moslashtiriladi)</div>
                
                <div x-show="isProcessing" class="inline-flex items-center gap-1.5 text-xs text-amber-600 font-medium">
                    <svg class="h-3.5 w-3.5 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>Rasm tayyorlanmoqda...</span>
                </div>

                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 pt-1">
                    <label for="avatar" class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg bg-amber-500 px-3 py-1.5 text-xs font-semibold text-zinc-950 hover:bg-amber-400 shadow-sm transition">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Rasm tanlash
                    </label>
                    <template x-if="preview">
                        <button type="button" @click="clearAvatar()" class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-white px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-900/60 dark:bg-zinc-800 dark:text-red-400 dark:hover:bg-red-950/30 transition">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            O'chirish
                        </button>
                    </template>
                </div>
                <input type="hidden" name="remove_avatar" :value="removeAvatar ? '1' : '0'">
                <x-input-error class="mt-1" :messages="$errors->get('avatar')" />
            </div>
        </div>

        <script>
        function avatarPicker(initialUrl) {
            return {
                preview: initialUrl || null,
                removeAvatar: false,
                isProcessing: false,
                async onFileChange(e) {
                    const file = e.target.files[0];
                    if (!file || !file.type || !file.type.startsWith('image/')) {
                        return;
                    }
                    this.removeAvatar = false;
                    this.isProcessing = true;

                    try {
                        const compressed = await this.compress(file, 800, 800, 0.85);
                        this.preview = URL.createObjectURL(compressed);

                        // DataTransfer orqali input faylini siqilgan fayl bilan almashtirish
                        try {
                            const dt = new DataTransfer();
                            dt.items.add(compressed);
                            e.target.files = dt.files;
                        } catch (dtError) {
                            console.warn('DataTransfer unavailable:', dtError);
                        }
                    } catch (err) {
                        console.error('Siqish xatosi:', err);
                        this.preview = URL.createObjectURL(file);
                    } finally {
                        this.isProcessing = false;
                    }
                },
                compress(file, maxWidth, maxHeight, quality) {
                    return new Promise((resolve) => {
                        // Agar fayl allaqachon juda kichik bo'lsa (< 150KB)
                        if (file.size <= 150 * 1024) {
                            resolve(file);
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = (re) => {
                            const img = new Image();
                            img.onload = () => {
                                let width = img.width;
                                let height = img.height;

                                if (width > maxWidth || height > maxHeight) {
                                    if (width > height) {
                                        height = Math.round((height * maxWidth) / width);
                                        width = maxWidth;
                                    } else {
                                        width = Math.round((width * maxHeight) / height);
                                        height = maxHeight;
                                    }
                                }

                                const canvas = document.createElement('canvas');
                                canvas.width = width;
                                canvas.height = height;
                                const ctx = canvas.getContext('2d');
                                ctx.drawImage(img, 0, 0, width, height);

                                const outputType = file.type === 'image/png' ? 'image/png' : 'image/jpeg';
                                canvas.toBlob((blob) => {
                                    if (!blob) {
                                        resolve(file);
                                        return;
                                    }
                                    const ext = outputType === 'image/png' ? '.png' : '.jpg';
                                    const baseName = file.name ? file.name.replace(/\.[^/.]+$/, "") : 'avatar';
                                    const newFile = new File([blob], baseName + ext, {
                                        type: outputType,
                                        lastModified: Date.now()
                                    });
                                    resolve(newFile);
                                }, outputType, quality);
                            };
                            img.onerror = () => resolve(file);
                            img.src = re.target.result;
                        };
                        reader.onerror = () => resolve(file);
                        reader.readAsDataURL(file);
                    });
                },
                clearAvatar() {
                    this.preview = null;
                    this.removeAvatar = true;
                    const input = document.getElementById('avatar');
                    if (input) input.value = '';
                }
            };
        }
        </script>

        <div>
            <x-input-label for="name" value="Ism" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full text-zinc-900 dark:text-zinc-100" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="phone" value="Telefon raqami" />
                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full text-zinc-900 dark:text-zinc-100" :value="old('phone', $user->phone)" placeholder="+998 90 123 45 67" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>

            <div>
                <x-input-label for="city_id" value="Shahar / Viloyat" />
                <select id="city_id" name="city_id" class="mt-1 block w-full rounded-xl border-zinc-200 bg-zinc-50 text-sm text-zinc-900 focus:border-amber-500 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="" class="bg-white text-zinc-900 dark:bg-zinc-800 dark:text-zinc-100">Tanlanmagan</option>
                    @foreach($cities ?? [] as $city)
                        <option value="{{ $city->id }}" class="bg-white text-zinc-900 dark:bg-zinc-800 dark:text-zinc-100" @selected(old('city_id', $user->city_id) == $city->id)>
                            {{ $city->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('city_id')" />
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="telegram" value="Telegram (@username yoki havola)" />
                <x-text-input id="telegram" name="telegram" type="text" class="mt-1 block w-full text-zinc-900 dark:text-zinc-100" :value="old('telegram', $user->telegram)" placeholder="@username" />
                <x-input-error class="mt-2" :messages="$errors->get('telegram')" />
            </div>

            <div>
                <x-input-label for="instagram" value="Instagram (@username yoki havola)" />
                <x-text-input id="instagram" name="instagram" type="text" class="mt-1 block w-full text-zinc-900 dark:text-zinc-100" :value="old('instagram', $user->instagram)" placeholder="@username" />
                <x-input-error class="mt-2" :messages="$errors->get('instagram')" />
            </div>
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full text-zinc-900 dark:text-zinc-100" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-zinc-600 dark:text-zinc-300">
                        Email manzilingiz tasdiqlanmagan.

                        <button form="send-verification" class="rounded-md text-sm text-amber-600 underline hover:text-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:text-amber-400">
                            Tasdiqlash xatini qayta yuborish uchun bosing.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-emerald-600 dark:text-emerald-400">
                            Email manzilingizga yangi tasdiqlash havolasi yuborildi.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <x-primary-button class="bg-amber-500 text-zinc-950 font-bold hover:bg-amber-400 shadow-sm">
                Ma'lumotlarni saqlash
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm font-semibold text-emerald-600 dark:text-emerald-400"
                >✓ Muvaffaqiyatli saqlandi.</p>
            @endif
        </div>
    </form>
</section>
