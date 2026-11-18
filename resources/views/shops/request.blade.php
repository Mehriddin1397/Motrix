<x-layout 
    title="Motrix'da do'kon ochish uchun ariza yuborish"
    description="Motrix platformasida rasmiy moto do'kon yoki ehtiyot qismlar do'koni oching va e'lonlaringizni minglab xaridorlarga yetkazing."
>
    <div class="mx-auto max-w-2xl px-4 py-6 space-y-6">

        {{-- Sarlavha banneri --}}
        <div class="rounded-3xl bg-gradient-to-br from-amber-500 via-amber-600 to-amber-700 p-6 text-white shadow-lg">
            <div class="flex items-center gap-3">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/20 text-3xl backdrop-blur">
                    🏬
                </span>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black">Motrix'da Do'kon ochish</h1>
                    <p class="text-xs text-amber-100 mt-0.5">Rasmiy moto salon yoki ehtiyot qismlar do'koningizni platformada oching</p>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-xs">
                <div class="rounded-2xl bg-black/15 p-2.5 backdrop-blur border border-white/10">
                    <div class="font-bold">✓ Rasmiy maqom</div>
                    <div class="text-[11px] text-amber-100 mt-0.5">Do'koningiz rasmiy belgi (badge) bilan ajralib turadi</div>
                </div>
                <div class="rounded-2xl bg-black/15 p-2.5 backdrop-blur border border-white/10">
                    <div class="font-bold">📍 Navigatsiya va Lokatsiya</div>
                    <div class="text-[11px] text-amber-100 mt-0.5">Xaridorlar do'koningizga to'g'ridan-to'g'ri marshrut chiza oladi</div>
                </div>
                <div class="rounded-2xl bg-black/15 p-2.5 backdrop-blur border border-white/10">
                    <div class="font-bold">📈 Cheksiz savdo</div>
                    <div class="text-[11px] text-amber-100 mt-0.5">Barcha mototexnika va qismlaringiz bir sahifada</div>
                </div>
            </div>
        </div>

        @if(session('status'))
            <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/15 p-4 text-sm font-semibold text-emerald-800 dark:text-emerald-300">
                ✓ {{ session('status') }}
            </div>
        @endif

        @if(isset($existingRequest) && $existingRequest && $existingRequest->isPending())
            <div class="rounded-2xl border border-amber-500/30 bg-amber-500/15 p-4 text-xs font-medium text-amber-900 dark:text-amber-200">
                ⏳ Siz allaqachon "<strong>{{ $existingRequest->shop_name }}</strong>" nomli do'kon ochish uchun ariza yuborgansiz. Arizangiz hozirda admin tomonidan ko'rib chiqilmoqda. Agar ma'lumotlarni o'zgartirmoqchi bo'lsangiz, yangi ariza yuborishingiz mumkin.
            </div>
        @endif

        {{-- Ariza formasi --}}
        <form method="POST" action="{{ route('shop-requests.store') }}" class="space-y-5 rounded-3xl border border-zinc-200 bg-white p-5 sm:p-7 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            @csrf

            <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3">
                <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Do'kon ma'lumotlari</h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Admin ushbu ma'lumotlar asosida do'kon ochadi va sizni biriktiradi.</p>
            </div>

            {{-- Do'kon nomi --}}
            <div>
                <label for="shop_name" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                    Do'kon nomi <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="shop_name" 
                    name="shop_name" 
                    value="{{ old('shop_name', auth()->user()->name . ' Moto Salon') }}" 
                    required 
                    placeholder="Masalan: Samarkand Moto Salon yoki MotoZapchast Toshkent"
                    class="mt-1.5 w-full rounded-xl border border-zinc-300 px-3.5 py-2.5 text-sm text-zinc-900 placeholder-zinc-400 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                >
                @error('shop_name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Do'kon turi --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                    Do'kon turi <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <label class="flex items-center gap-3 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-3 cursor-pointer hover:border-amber-500 transition">
                        <input type="radio" name="shop_type" value="moto" {{ old('shop_type', 'moto') === 'moto' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500">
                        <div>
                            <div class="text-xs font-bold">🏍️ Moto do'kon</div>
                            <div class="text-[11px] text-zinc-400">Mototsikllar savdosi</div>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-3 cursor-pointer hover:border-amber-500 transition">
                        <input type="radio" name="shop_type" value="parts" {{ old('shop_type') === 'parts' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500">
                        <div>
                            <div class="text-xs font-bold">🔩 Ehtiyot qismlar</div>
                            <div class="text-[11px] text-zinc-400">Zapchast va aksessuarlar</div>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-3 cursor-pointer hover:border-amber-500 transition">
                        <input type="radio" name="shop_type" value="both" {{ old('shop_type') === 'both' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500">
                        <div>
                            <div class="text-xs font-bold">⭐ Har ikkalasi</div>
                            <div class="text-[11px] text-zinc-400">Moto va ehtiyot qismlar</div>
                        </div>
                    </label>
                </div>
                @error('shop_type')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Shahar va Telefon --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="city_id" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                        Shahar / Viloyat <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="city_id" 
                        name="city_id" 
                        required
                        class="mt-1.5 w-full rounded-xl border border-zinc-300 px-3.5 py-2.5 text-sm text-zinc-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                    >
                        <option value="">Shaharni tanlang</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('city_id', auth()->user()->city_id) == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('city_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                        Telefon raqami <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone" 
                        value="{{ old('phone', auth()->user()->phone) }}" 
                        required 
                        placeholder="+998 90 123 45 67"
                        class="mt-1.5 w-full rounded-xl border border-zinc-300 px-3.5 py-2.5 text-sm text-zinc-900 placeholder-zinc-400 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                    >
                    @error('phone')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Aniq manzil --}}
            <div>
                <label for="address" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                    Do'kon manzili (ko'cha, mo'ljal)
                </label>
                <input 
                    type="text" 
                    id="address" 
                    name="address" 
                    value="{{ old('address') }}" 
                    placeholder="Masalan: Chilonzor tumani, Bunyodkor shoh ko'chasi 45-uy, Mo'ljal: Novza metro"
                    class="mt-1.5 w-full rounded-xl border border-zinc-300 px-3.5 py-2.5 text-sm text-zinc-900 placeholder-zinc-400 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                >
                @error('address')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Qulay Lokatsiya kiritish komponenti (Xarita, GPS, Havola) --}}
            <div>
                <x-location-picker 
                    latName="lat"
                    lngName="lng"
                    :initialLat="old('lat')"
                    :initialLng="old('lng')"
                />
                @error('lat')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Qo'shimcha xabar / izoh --}}
            <div>
                <label for="message" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                    Adminga xabar / Do'kon haqida qo'shimcha ma'lumot
                </label>
                <textarea 
                    id="message" 
                    name="message" 
                    rows="3" 
                    placeholder="Do'koningiz faoliyati, qaysi brendlar bilan ishlashingiz haqida yozishingiz mumkin..."
                    class="mt-1.5 w-full rounded-xl border border-zinc-300 px-3.5 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                >{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Yuborish tugmasi --}}
            <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                <a href="{{ route('market.index') }}" class="text-xs font-medium text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-300">
                    ← Bekor qilish
                </a>

                <button 
                    type="submit" 
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-6 py-3 text-sm font-bold text-zinc-950 shadow-md transition hover:bg-amber-400 active:scale-95 cursor-pointer"
                >
                    <span>Arizani adminga yuborish</span>
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>
        </form>

    </div>
</x-layout>
