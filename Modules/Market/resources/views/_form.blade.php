@csrf
@isset($listing) @method('PUT') @endisset

<div x-data="listingFormManager()" class="space-y-4">
    <!-- AI Tezkor to'ldirish kartasi -->
    <div class="relative overflow-hidden rounded-2xl border border-amber-500/40 bg-gradient-to-br from-amber-500/15 via-amber-500/5 to-transparent p-4 shadow-sm dark:border-amber-500/30 dark:bg-amber-950/20">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-tr from-amber-500 to-amber-400 text-white shadow-md shadow-amber-500/30">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">AI orqali tezkor to'ldirish</h3>
                        <span class="rounded-full bg-amber-500/20 px-2 py-0.5 text-[10px] font-bold text-amber-700 dark:text-amber-300">BEPUL</span>
                    </div>
                    <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-0.5">
                        Mototsikl rasmini yuklang — AI brend, model, yil va tavsifni avtomatik to'ldirib beradi!
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <label class="relative inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-amber-600 transition active:scale-95 disabled:opacity-50">
                    <input 
                        type="file" 
                        accept="image/*" 
                        class="hidden" 
                        @change="handleAiFileSelect($event)" 
                        :disabled="isAnalyzing"
                    >
                    <template x-if="!isAnalyzing">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Rasm orqali aniqlash</span>
                        </span>
                    </template>
                    <template x-if="isAnalyzing">
                        <span class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>AI tahlil qilmoqda...</span>
                        </span>
                    </template>
                </label>
            </div>
        </div>

        <!-- Muvaffaqiyat xabari -->
        <div x-show="aiSuccessMessage" x-cloak class="mt-3 flex items-start gap-2.5 rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-3 text-xs text-emerald-800 dark:text-emerald-300">
            <svg class="h-4 w-4 shrink-0 mt-0.5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="space-y-0.5">
                <div class="font-bold text-sm" x-text="aiSuccessTitle"></div>
                <div x-text="aiSuccessMessage"></div>
            </div>
        </div>

        <!-- Xatolik xabari -->
        <div x-show="aiErrorMessage" x-cloak class="mt-3 flex items-start gap-2.5 rounded-xl border border-red-500/30 bg-red-500/10 p-3 text-xs text-red-700 dark:text-red-300">
            <svg class="h-4 w-4 shrink-0 mt-0.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div x-text="aiErrorMessage"></div>
        </div>
    </div>

    <div>
        <x-input-label for="brand_id" value="Brend" />
        <select 
            x-ref="brandSelect"
            id="brand_id" 
            name="brand_id" 
            required 
            class="mt-1.5 w-full rounded-xl border-zinc-200 bg-zinc-50 text-sm focus:border-amber-500 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 transition duration-300"
            :class="{'ring-2 ring-amber-500 border-amber-500': highlightedField === 'brand'}"
        >
            <option value="">Tanlang</option>
            @foreach($brands as $brand)
                <option value="{{ $brand->id }}" @selected(old('brand_id', $listing->brand_id ?? null) == $brand->id)>{{ $brand->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('brand_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="motorcycle_id" value="Model (ixtiyoriy)" />
        <select 
            x-ref="motorcycleSelect"
            id="motorcycle_id" 
            name="motorcycle_id" 
            class="mt-1.5 w-full rounded-xl border-zinc-200 bg-zinc-50 text-sm focus:border-amber-500 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 transition duration-300"
            :class="{'ring-2 ring-amber-500 border-amber-500': highlightedField === 'motorcycle'}"
        >
            <option value="">Ro'yxatda yo'q / boshqa</option>
            @foreach($motorcycles as $motorcycle)
                <option value="{{ $motorcycle->id }}" @selected(old('motorcycle_id', $listing->motorcycle_id ?? null) == $motorcycle->id)>{{ $motorcycle->brand->name }} {{ $motorcycle->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('motorcycle_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="custom_title" value="Sarlavha (model ro'yxatda bo'lmasa)" />
        <x-text-input 
            x-ref="customTitleInput"
            id="custom_title" 
            name="custom_title" 
            type="text" 
            class="mt-1.5 transition duration-300" 
            :value="old('custom_title', $listing->custom_title ?? null)" 
            placeholder="Masalan: Honda CB400 1998" 
        />
        <x-input-error :messages="$errors->get('custom_title')" class="mt-2" />
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <x-input-label for="year" value="Ishlab chiqarilgan yili" />
            <x-text-input 
                x-ref="yearInput"
                id="year" 
                name="year" 
                type="number" 
                class="mt-1.5 transition duration-300" 
                :value="old('year', $listing->year ?? null)" 
                required 
            />
            <x-input-error :messages="$errors->get('year')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="mileage_km" value="Probeg (km)" />
            <x-text-input id="mileage_km" name="mileage_km" type="number" class="mt-1.5" :value="old('mileage_km', $listing->mileage_km ?? 0)" required />
            <x-input-error :messages="$errors->get('mileage_km')" class="mt-2" />
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <x-input-label for="price" value="Narx" />
            <x-text-input id="price" name="price" type="number" step="0.01" class="mt-1.5" :value="old('price', $listing->price ?? null)" required />
            <x-input-error :messages="$errors->get('price')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="currency" value="Valyuta" />
            <select id="currency" name="currency" required class="mt-1.5 w-full rounded-xl border-zinc-200 bg-zinc-50 text-sm focus:border-amber-500 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                @foreach(['USD', 'UZS'] as $currency)
                    <option value="{{ $currency }}" @selected(old('currency', $listing->currency ?? 'USD') === $currency)>{{ $currency }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('currency')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label for="condition" value="Texnik holati" />
        <select 
            x-ref="conditionSelect"
            id="condition" 
            name="condition" 
            required 
            class="mt-1.5 w-full rounded-xl border-zinc-200 bg-zinc-50 text-sm focus:border-amber-500 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
        >
            <option value="new" @selected(old('condition', $listing->condition ?? null) === 'new')>Yangi</option>
            <option value="used" @selected(old('condition', $listing->condition ?? null) === 'used')>Ishlatilgan</option>
        </select>
        <x-input-error :messages="$errors->get('condition')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="city_id" value="Shahar" />
        <select id="city_id" name="city_id" required class="mt-1.5 w-full rounded-xl border-zinc-200 bg-zinc-50 text-sm focus:border-amber-500 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
            <option value="">Tanlang</option>
            @foreach($cities as $city)
                <option value="{{ $city->id }}" @selected(old('city_id', $listing->city_id ?? null) == $city->id)>{{ $city->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('city_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="description" value="Tavsif" />
        <textarea 
            x-ref="descriptionInput"
            id="description" 
            name="description" 
            rows="4" 
            required 
            class="mt-1.5 w-full rounded-xl border-zinc-200 bg-zinc-50 text-sm focus:border-amber-500 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 transition duration-300"
        >{{ old('description', $listing->description ?? null) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <!-- Rasmlar yuklash qismi -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <x-input-label for="images" value="Rasmlar" />
            <div class="flex items-center gap-2">
                <button 
                    type="button" 
                    x-show="newFiles.length > 0" 
                    @click="analyzeFirstFile()" 
                    :disabled="isAnalyzing"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-amber-500/40 bg-amber-500/10 px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-amber-500/20 dark:bg-amber-500/20 dark:text-amber-300 transition"
                >
                    <svg class="h-3.5 w-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span x-text="isAnalyzing ? 'Tahlil...' : 'Rasmdan to\'ldirish (AI)'"></span>
                </button>
                <span x-show="newFiles.length > 0" x-text="newFiles.length + ' ta yangi rasm tanlandi'" class="text-xs font-semibold text-amber-600 dark:text-amber-400"></span>
            </div>
        </div>

        <div 
            @dragover.prevent="dragOver = true"
            @dragleave.prevent="dragOver = false"
            @drop.prevent="handleDrop($event)"
            :class="{'border-amber-500 bg-amber-500/10': dragOver, 'border-dashed border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50': !dragOver}"
            class="relative flex flex-col items-center justify-center rounded-2xl border-2 p-6 text-center transition hover:border-amber-500/70"
        >
            <input 
                x-ref="fileInput" 
                id="images" 
                name="images[]" 
                type="file" 
                accept="image/*" 
                multiple 
                @change="handleFiles($event.target.files)"
                class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
            >
            <div class="pointer-events-none flex flex-col items-center space-y-2">
                <div class="rounded-full bg-amber-500/20 p-3 text-amber-500 dark:text-amber-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">
                    Rasmlarni yuklash uchun bosing yoki shu yerga tashlang
                </p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    PNG, JPG, WEBP (Bir nechta rasm tanlashingiz mumkin)
                </p>
            </div>
        </div>

        <template x-if="newFiles.length > 0">
            <div class="space-y-2">
                <div class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Tanlangan rasmlar:</div>
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                    <template x-for="(item, index) in newFiles" :key="index">
                        <div class="group relative aspect-square rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-700 bg-zinc-900 shadow-sm">
                            <img :src="item.url" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" />
                            <button 
                                type="button" 
                                @click="removeFile(index)" 
                                class="absolute top-1.5 right-1.5 flex h-6 w-6 items-center justify-center rounded-full bg-red-600/90 text-white shadow-md hover:bg-red-700 transition"
                                title="O'chirish"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <div class="absolute bottom-1 left-1 rounded bg-black/60 px-1.5 py-0.5 text-[10px] font-medium text-white backdrop-blur" x-text="(index + 1) + '-rasm'"></div>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        @if(isset($listing) && $listing->getMedia('images')->isNotEmpty())
            <div class="mt-3 space-y-2">
                <div class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Joriy yuklangan rasmlar:</div>
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                    @foreach($listing->getMedia('images') as $media)
                        <div class="relative aspect-square rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-700 bg-zinc-900 shadow-sm">
                            <img src="{{ $media->getUrl() }}" class="h-full w-full object-cover" />
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <x-input-error :messages="$errors->get('images')" class="mt-2" />
    </div>

    <x-primary-button class="w-full py-2.5">
        {{ isset($listing) ? 'Saqlash' : "E'lonni joylashtirish" }}
    </x-primary-button>
</div>

<script>
function listingFormManager() {
    return {
        dragOver: false,
        newFiles: [],
        isProcessing: false,
        isAnalyzing: false,
        aiSuccessTitle: '',
        aiSuccessMessage: '',
        aiErrorMessage: '',
        highlightedField: '',

        async handleFiles(fileList) {
            const files = Array.from(fileList);
            this.isProcessing = true;
            for (const file of files) {
                if (file.type && file.type.startsWith('image/')) {
                    const compressed = await this.compress(file, 1600, 1600, 0.82);
                    this.newFiles.push({
                        file: compressed,
                        url: URL.createObjectURL(compressed)
                    });
                }
            }
            this.isProcessing = false;
            this.syncInput();
        },

        async handleAiFileSelect(event) {
            const files = event.target.files;
            if (!files || files.length === 0) return;
            const file = files[0];

            // 1. Rasmlar ro'yxatiga ham qo'shish
            await this.handleFiles([file]);

            // 2. AI tahlilini boshlash
            await this.performAiAnalysis(file);

            // Inputni tozalash
            event.target.value = '';
        },

        async analyzeFirstFile() {
            if (this.newFiles.length === 0) {
                this.aiErrorMessage = "Iltimos, avval mototsikl rasmini yuklang.";
                return;
            }
            await this.performAiAnalysis(this.newFiles[0].file);
        },

        async performAiAnalysis(file) {
            this.isAnalyzing = true;
            this.aiSuccessMessage = '';
            this.aiSuccessTitle = '';
            this.aiErrorMessage = '';

            try {
                const formData = new FormData();
                formData.append('image', file);
                formData.append('_token', '{{ csrf_token() }}');

                const response = await fetch('{{ route('market.ai-identify') }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Mototsikl ma\'lumotlarini aniqlab bo\'lmadi.');
                }

                // 1. Brendni to'ldirish
                if (data.brand_id && this.$refs.brandSelect) {
                    this.$refs.brandSelect.value = data.brand_id;
                    this.$refs.brandSelect.dispatchEvent(new Event('change'));
                }

                // 2. Model yoki Custom Title ni to'ldirish
                if (data.motorcycle_id && this.$refs.motorcycleSelect) {
                    this.$refs.motorcycleSelect.value = data.motorcycle_id;
                    this.$refs.motorcycleSelect.dispatchEvent(new Event('change'));
                } else if (data.custom_title && this.$refs.customTitleInput) {
                    this.$refs.customTitleInput.value = data.custom_title;
                }

                // 3. Yili
                if (data.year && this.$refs.yearInput) {
                    this.$refs.yearInput.value = data.year;
                }

                // 4. Holati
                if (data.condition && this.$refs.conditionSelect) {
                    this.$refs.conditionSelect.value = data.condition;
                }

                // 5. Tavsif (agar hozir bo'sh bo'lsa yoki to'ldirish kerak bo'lsa)
                if (data.description && this.$refs.descriptionInput) {
                    if (!this.$refs.descriptionInput.value || this.$refs.descriptionInput.value.trim() === '') {
                        this.$refs.descriptionInput.value = data.description;
                    }
                }

                // Chiroyli xabar
                const motoTitle = data.motorcycle_name || data.custom_title || data.brand_name || 'Mototsikl';
                this.aiSuccessTitle = `🎉 Aniqlandi: ${motoTitle} (${data.year || ''}-yil)`;
                let details = [];
                if (data.category) details.push(`Toifasi: ${data.category}`);
                if (data.engine_cc) details.push(`Hajmi: ~${data.engine_cc} cc`);
                details.push('Ma\'lumotlar formaga to\'ldirildi. Narx va probegni kiritib, e\'lonni tasdiqlang.');
                this.aiSuccessMessage = details.join(' • ');

                // Animatsiya
                this.highlightedField = 'brand';
                setTimeout(() => { this.highlightedField = ''; }, 3000);

            } catch (err) {
                console.error('AI Recognition Error:', err);
                this.aiErrorMessage = err.message || 'Xatolik yuz berdi. Iltimos, qaytadan urinib ko\'ring.';
            } finally {
                this.isAnalyzing = false;
            }
        },

        compress(file, maxWidth, maxHeight, quality) {
            return new Promise((resolve) => {
                if (file.size <= 250 * 1024) {
                    resolve(file);
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
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

                        canvas.toBlob((blob) => {
                            if (!blob) {
                                resolve(file);
                                return;
                            }
                            const newFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + '.jpg', {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });
                            resolve(newFile);
                        }, 'image/jpeg', quality);
                    };
                    img.onerror = () => resolve(file);
                    img.src = e.target.result;
                };
                reader.onerror = () => resolve(file);
                reader.readAsDataURL(file);
            });
        },

        handleDrop(e) {
            this.dragOver = false;
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                this.handleFiles(e.dataTransfer.files);
            }
        },

        removeFile(index) {
            if (this.newFiles[index]) {
                URL.revokeObjectURL(this.newFiles[index].url);
                this.newFiles.splice(index, 1);
                this.syncInput();
            }
        },

        syncInput() {
            try {
                const dt = new DataTransfer();
                this.newFiles.forEach(item => dt.items.add(item.file));
                this.$refs.fileInput.files = dt.files;
            } catch (err) {
                console.warn('DataTransfer sync error:', err);
            }
        }
    };
}
</script>
