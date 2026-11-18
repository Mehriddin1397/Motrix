@csrf
@isset($listing) @method('PUT') @endisset

<div>
    <x-input-label for="brand_id" value="Brend" />
    <select id="brand_id" name="brand_id" required class="mt-1.5 w-full rounded-xl border-zinc-200 bg-zinc-50 text-sm focus:border-amber-500 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
        <option value="">Tanlang</option>
        @foreach($brands as $brand)
            <option value="{{ $brand->id }}" @selected(old('brand_id', $listing->brand_id ?? null) == $brand->id)>{{ $brand->name }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('brand_id')" class="mt-2" />
</div>

<div>
    <x-input-label for="motorcycle_id" value="Model (ixtiyoriy)" />
    <select id="motorcycle_id" name="motorcycle_id" class="mt-1.5 w-full rounded-xl border-zinc-200 bg-zinc-50 text-sm focus:border-amber-500 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
        <option value="">Ro'yxatda yo'q / boshqa</option>
        @foreach($motorcycles as $motorcycle)
            <option value="{{ $motorcycle->id }}" @selected(old('motorcycle_id', $listing->motorcycle_id ?? null) == $motorcycle->id)>{{ $motorcycle->brand->name }} {{ $motorcycle->name }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('motorcycle_id')" class="mt-2" />
</div>

<div>
    <x-input-label for="custom_title" value="Sarlavha (model ro'yxatda bo'lmasa)" />
    <x-text-input id="custom_title" name="custom_title" type="text" class="mt-1.5" :value="old('custom_title', $listing->custom_title ?? null)" placeholder="Masalan: Honda CB400 1998" />
    <x-input-error :messages="$errors->get('custom_title')" class="mt-2" />
</div>

<div class="grid grid-cols-2 gap-3">
    <div>
        <x-input-label for="year" value="Ishlab chiqarilgan yili" />
        <x-text-input id="year" name="year" type="number" class="mt-1.5" :value="old('year', $listing->year ?? null)" required />
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
    <select id="condition" name="condition" required class="mt-1.5 w-full rounded-xl border-zinc-200 bg-zinc-50 text-sm focus:border-amber-500 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
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
    <textarea id="description" name="description" rows="4" required class="mt-1.5 w-full rounded-xl border-zinc-200 bg-zinc-50 text-sm focus:border-amber-500 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">{{ old('description', $listing->description ?? null) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div x-data="imagePreviewManager()" class="space-y-3">
    <div class="flex items-center justify-between">
        <x-input-label for="images" value="Rasmlar" />
        <span x-show="newFiles.length > 0" x-text="newFiles.length + ' ta yangi rasm tanlandi'" class="text-xs font-semibold text-amber-600 dark:text-amber-400"></span>
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

<script>
function imagePreviewManager() {
    return {
        dragOver: false,
        newFiles: [],
        isProcessing: false,
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
        compress(file, maxWidth, maxHeight, quality) {
            return new Promise((resolve) => {
                if (file.size <= 200 * 1024) {
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

<x-primary-button class="w-full py-2.5">
    {{ isset($listing) ? 'Saqlash' : "E'lonni joylashtirish" }}
</x-primary-button>
