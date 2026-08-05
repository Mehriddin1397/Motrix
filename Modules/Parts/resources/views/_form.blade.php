@csrf
@isset($part) @method('PUT') @endisset

<div>
    <x-input-label for="name" value="Mahsulot nomi" />
    <x-text-input id="name" name="name" type="text" class="mt-1.5" :value="old('name', $part->name ?? null)" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div>
    <x-input-label for="category_id" value="Kategoriya" />
    <select id="category_id" name="category_id" required class="mt-1.5 w-full rounded-xl border-zinc-200 bg-zinc-50 text-sm focus:border-amber-500 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
        <option value="">Tanlang</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $part->category_id ?? null) == $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
</div>

<div class="grid grid-cols-2 gap-3">
    <div>
        <x-input-label for="part_type" value="Turi" />
        <select id="part_type" name="part_type" required class="mt-1.5 w-full rounded-xl border-zinc-200 bg-zinc-50 text-sm focus:border-amber-500 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
            <option value="oem" @selected(old('part_type', $part->part_type ?? null) === 'oem')>OEM (original)</option>
            <option value="aftermarket" @selected(old('part_type', $part->part_type ?? null) === 'aftermarket')>Aftermarket</option>
        </select>
        <x-input-error :messages="$errors->get('part_type')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="condition" value="Holati" />
        <select id="condition" name="condition" required class="mt-1.5 w-full rounded-xl border-zinc-200 bg-zinc-50 text-sm focus:border-amber-500 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
            <option value="new" @selected(old('condition', $part->condition ?? null) === 'new')>Yangi</option>
            <option value="used" @selected(old('condition', $part->condition ?? null) === 'used')>Ishlatilgan</option>
        </select>
        <x-input-error :messages="$errors->get('condition')" class="mt-2" />
    </div>
</div>

<div>
    <x-input-label for="part_number" value="Ehtiyot qism raqami (ixtiyoriy)" />
    <x-text-input id="part_number" name="part_number" type="text" class="mt-1.5" :value="old('part_number', $part->part_number ?? null)" />
    <x-input-error :messages="$errors->get('part_number')" class="mt-2" />
</div>

<div class="grid grid-cols-2 gap-3">
    <div>
        <x-input-label for="price" value="Narx" />
        <x-text-input id="price" name="price" type="number" step="0.01" class="mt-1.5" :value="old('price', $part->price ?? null)" required />
        <x-input-error :messages="$errors->get('price')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="stock_qty" value="Ombordagi soni" />
        <x-text-input id="stock_qty" name="stock_qty" type="number" class="mt-1.5" :value="old('stock_qty', $part->stock_qty ?? 0)" required />
        <x-input-error :messages="$errors->get('stock_qty')" class="mt-2" />
    </div>
</div>

<div>
    <x-input-label for="description" value="Tavsif" />
    <textarea id="description" name="description" rows="4" required class="mt-1.5 w-full rounded-xl border-zinc-200 bg-zinc-50 text-sm focus:border-amber-500 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">{{ old('description', $part->description ?? null) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div>
    <x-input-label for="images" value="Rasmlar" />
    <input id="images" name="images[]" type="file" accept="image/*" multiple class="mt-1.5 block w-full text-sm text-zinc-500 file:mr-3 file:rounded-full file:border-0 file:bg-amber-500 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-zinc-900 dark:text-zinc-400">
    <x-input-error :messages="$errors->get('images')" class="mt-2" />
</div>

<x-primary-button class="w-full py-2.5">
    {{ isset($part) ? 'Saqlash' : 'Mahsulotni joylashtirish' }}
</x-primary-button>
