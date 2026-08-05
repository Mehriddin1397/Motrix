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

<div>
    <x-input-label for="images" value="Rasmlar" />
    <input id="images" name="images[]" type="file" accept="image/*" multiple class="mt-1.5 block w-full text-sm text-zinc-500 file:mr-3 file:rounded-full file:border-0 file:bg-amber-500 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-zinc-900 dark:text-zinc-400">
    <x-input-error :messages="$errors->get('images')" class="mt-2" />
</div>

<x-primary-button class="w-full py-2.5">
    {{ isset($listing) ? 'Saqlash' : "E'lonni joylashtirish" }}
</x-primary-button>
