@props([
    'latName' => 'lat',
    'lngName' => 'lng',
    'initialLat' => null,
    'initialLng' => null,
    'mapId' => 'location-picker-map-' . uniqid(),
])

@php
    $defaultLat = filled($initialLat) ? (float)$initialLat : 41.311081;
    $defaultLng = filled($initialLng) ? (float)$initialLng : 69.240562;
    $hasInitial = filled($initialLat) && filled($initialLng) && (float)$initialLat != 0 && (float)$initialLng != 0;
@endphp

<div 
    x-data="locationPicker({
        mapId: '{{ $mapId }}',
        initialLat: {{ $defaultLat }},
        initialLng: {{ $defaultLng }},
        hasInitial: {{ $hasInitial ? 'true' : 'false' }}
    })" 
    x-init="initMap()"
    class="space-y-3 rounded-2xl border border-zinc-200 bg-zinc-50/50 p-4 dark:border-zinc-800 dark:bg-zinc-900/50"
>
    {{-- Sarlavha va Harakat tugmalari --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5">
        <div>
            <label class="block text-xs font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider">
                📍 Do'kon / Xizmat joylashuvi (Lokatsiya)
            </label>
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">
                Xaritadan tanlang, GPS orqali aniqlang yoki havola kiriting.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            {{-- GPS Tugmasi --}}
            <button 
                type="button" 
                @click="detectCurrentLocation()" 
                :disabled="locating"
                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-amber-500 px-3.5 py-2 text-xs font-bold text-zinc-950 shadow-sm transition hover:bg-amber-400 active:scale-95 disabled:opacity-50"
            >
                <svg x-show="!locating" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 18a8 8 0 110-16 8 8 0 010 16zm0-11a3 3 0 100 6 3 3 0 000-6z"/>
                </svg>
                <svg x-show="locating" class="h-4 w-4 animate-spin text-zinc-950" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="locating ? 'Aniqlanmoqda...' : '📍 Joriy joylashuvimni aniqlash'"></span>
            </button>

            {{-- Telefonda ochish (QR-kod) --}}
            <button 
                type="button" 
                @click="$dispatch('open-phone-share')"
                class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 active:scale-95 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700"
                title="Saytni telefonda ochib aniq GPS orqali belgilash"
            >
                <svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span>📱 Telefonda ochish</span>
            </button>
        </div>
    </div>

    {{-- Xabarnoma / Status banneri --}}
    <div 
        x-show="statusBanner.text" 
        x-cloak 
        class="rounded-xl p-3 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 transition"
        :class="{
            'bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800': statusBanner.type === 'success',
            'bg-amber-50 text-amber-900 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-200 dark:border-amber-800': statusBanner.type === 'warning' || statusBanner.type === 'info',
            'bg-red-50 text-red-800 border border-red-200 dark:bg-red-950/40 dark:text-red-300 dark:border-red-800': statusBanner.type === 'error'
        }"
    >
        <div class="flex items-start gap-2">
            <span x-text="statusBanner.icon" class="text-base leading-none shrink-0"></span>
            <span x-html="statusBanner.text" class="leading-relaxed"></span>
        </div>
        <div class="flex flex-wrap items-center gap-1.5 shrink-0 pt-1 sm:pt-0">
            <button 
                x-show="showPermissionHelp" 
                type="button" 
                @click="openPermissionModal = true"
                class="rounded-lg bg-amber-500/20 px-2.5 py-1 text-[11px] font-bold text-amber-950 dark:text-amber-200 hover:bg-amber-500/30 transition"
            >
                ❓ Ruxsat berish yo'riqnomasi
            </button>
            <button 
                type="button" 
                @click="$dispatch('open-phone-share')"
                class="rounded-lg bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 px-2.5 py-1 text-[11px] font-bold shadow-sm transition hover:opacity-90"
            >
                📱 Telefonda ochish
            </button>
            <button 
                type="button" 
                @click="statusBanner.text = ''"
                class="rounded-lg p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition"
                title="Yopish"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Xarita havolasini kiritish (Tezkor kiritish) --}}
    <div class="flex items-center gap-2">
        <div class="relative flex-1">
            <input 
                type="text" 
                x-model="pastedLink"
                @paste="setTimeout(() => parsePastedLink(), 100)"
                placeholder="Yandex / Google Maps havolasi yoki koordinata (masalan: 41.311, 69.240)..." 
                class="w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-xs text-zinc-800 placeholder-zinc-400 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200"
            >
        </div>
        <button 
            type="button" 
            @click="parsePastedLink()" 
            class="shrink-0 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition"
        >
            Qo'llash
        </button>
    </div>

    {{-- Interaktiv Xarita --}}
    <div class="relative overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-inner">
        <div id="{{ $mapId }}" class="h-64 sm:h-72 w-full z-0"></div>

        <div class="absolute bottom-2 left-2 z-10 rounded-lg bg-black/70 px-2.5 py-1 text-[11px] font-medium text-white backdrop-blur">
            💡 Xaritani bosib yoki belgini surib aniq joyni belgilang
        </div>
    </div>

    {{-- Koordinatalar ko'rsatkichi va Yashirin inputlar --}}
    <div class="flex flex-wrap items-center justify-between gap-2 pt-1 text-xs">
        <div class="flex items-center gap-2">
            <span class="font-medium text-zinc-500 dark:text-zinc-400">Koordinatalar:</span>
            <span x-show="currentLat && currentLng" class="font-mono font-semibold text-amber-600 dark:text-amber-400">
                <span x-text="Number(currentLat).toFixed(6)"></span>, <span x-text="Number(currentLng).toFixed(6)"></span>
            </span>
            <span x-show="!currentLat || !currentLng" class="text-zinc-400 italic">
                Hali belgilanmagan
            </span>
        </div>

        <div class="flex items-center gap-2">
            <button 
                type="button"
                x-show="currentLat && currentLng"
                @click="resetLocation()"
                class="text-[11px] text-zinc-400 hover:text-red-500 transition"
            >
                Tozalash
            </button>
        </div>
    </div>

    {{-- Form inputlari --}}
    <input type="hidden" name="{{ $latName }}" :value="currentLat || ''">
    <input type="hidden" name="{{ $lngName }}" :value="currentLng || ''">

    {{-- Brauzerda GPS ruxsatini yoqish bo'yicha yordamchi modal --}}
    <div 
        x-cloak 
        x-show="openPermissionModal" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;"
    >
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="openPermissionModal = false"></div>
        <div class="relative w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl dark:bg-zinc-900 ring-1 ring-zinc-200 dark:ring-zinc-800">
            <button 
                type="button" 
                @click="openPermissionModal = false"
                class="absolute right-4 top-4 rounded-full p-2 text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-amber-500/15 text-amber-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100">
                        GPS ruxsatini yoqish yo'riqnomasi
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        Brauzerda lokatsiyaga ruxsat berish bosqichlari
                    </p>
                </div>
            </div>

            <div class="mt-4 space-y-3 text-xs text-zinc-600 dark:text-zinc-300">
                <div class="flex items-start gap-2.5 rounded-xl bg-zinc-100 p-3 dark:bg-zinc-800">
                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-500 font-bold text-zinc-950 text-[11px]">1</span>
                    <p>Brauzeringizning manzil satrida (sayt URL manzili chapida) <strong>🔒 Qulfcha</strong> yoki <strong>⚙️ Sozlamalar</strong> belgisini bosing.</p>
                </div>
                <div class="flex items-start gap-2.5 rounded-xl bg-zinc-100 p-3 dark:bg-zinc-800">
                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-500 font-bold text-zinc-950 text-[11px]">2</span>
                    <p><strong>"Geolokatsiya" (Location / Местоположение)</strong> bandini topib, uni <strong>"Ruxsat berish" (Allow / Разрешить)</strong> holatiga o'tkazing.</p>
                </div>
                <div class="flex items-start gap-2.5 rounded-xl bg-zinc-100 p-3 dark:bg-zinc-800">
                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-500 font-bold text-zinc-950 text-[11px]">3</span>
                    <p>Sahifani yangilang (<code>F5</code>) va qaytadan <strong>"Joriy joylashuvimni aniqlash"</strong> tugmasini bosing.</p>
                </div>
            </div>

            <div class="mt-4 rounded-xl border border-amber-500/20 bg-amber-500/10 p-3 text-xs text-amber-800 dark:text-amber-300">
                <div class="font-bold">💡 Nima uchun ruxsat so'ramay bloklandi?</div>
                <p class="mt-1">
                    Zamonaviy brauzerlar (Chrome, Safari, Edge) xavfsizlik qoidalariga ko'ra faqatgina <strong>HTTPS</strong> protokoli orqali GPS ma'lumotlarini uzatadi.
                    Agar sayt HTTP orqali ochilgan bo'lsa yoki kompyuterda GPS chipi bo'lmasa, eng oson yo'li — <strong>saytni telefoningizda ochish</strong> (chunki smartfonlarda GPS har doim aniq ishlaydi).
                </p>
            </div>

            <div class="mt-5 flex items-center justify-end gap-2">
                <button 
                    type="button" 
                    @click="openPermissionModal = false; $dispatch('open-phone-share')"
                    class="rounded-xl bg-amber-500 px-4 py-2 text-xs font-bold text-zinc-950 shadow transition hover:bg-amber-400"
                >
                    📱 Telefonda ochish (QR-kod)
                </button>
                <button 
                    type="button" 
                    @click="openPermissionModal = false"
                    class="rounded-xl border border-zinc-200 dark:border-zinc-700 px-4 py-2 text-xs font-semibold text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
                >
                    Tushundim
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Leaflet kutubxonasini yuklash --}}
@once
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        function locationPicker(config) {
            return {
                mapId: config.mapId,
                currentLat: config.hasInitial ? config.initialLat : null,
                currentLng: config.hasInitial ? config.initialLng : null,
                locating: false,
                pastedLink: '',
                map: null,
                marker: null,
                openPermissionModal: false,
                showPermissionHelp: false,
                statusBanner: {
                    type: '',
                    icon: '',
                    text: ''
                },

                initMap() {
                    const self = this;
                    const startLat = self.currentLat || config.initialLat || 41.311081;
                    const startLng = self.currentLng || config.initialLng || 69.240562;
                    const zoomLevel = self.currentLat ? 15 : 12;

                    // Leaflet tayyor bo'lishini kutish
                    const checkLeaflet = setInterval(() => {
                        if (typeof L !== 'undefined') {
                            clearInterval(checkLeaflet);
                            self.setupLeaflet(startLat, startLng, zoomLevel);
                        }
                    }, 50);
                },

                setupLeaflet(startLat, startLng, zoomLevel) {
                    const self = this;
                    const mapElement = document.getElementById(self.mapId);
                    if (!mapElement) return;

                    self.map = L.map(self.mapId, {
                        zoomControl: true,
                        scrollWheelZoom: false
                    }).setView([startLat, startLng], zoomLevel);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(self.map);

                    // Agar boshlang'ich koordinatalar bo'lsa marker qo'yish
                    if (self.currentLat && self.currentLng) {
                        self.setMarker(self.currentLat, self.currentLng, false);
                    }

                    // Xaritani bosganda marker qo'yish
                    self.map.on('click', function(e) {
                        self.setMarker(e.latlng.lat, e.latlng.lng, true);
                        self.statusBanner = {
                            type: 'success',
                            icon: '📍',
                            text: 'Joylashuv belgilandi. Kerak bo\'lsa belgini surib aniqlashtiring.'
                        };
                        self.showPermissionHelp = false;
                    });

                    // Scroll muammosini oldini olish uchun
                    setTimeout(() => {
                        self.map.invalidateSize();
                    }, 400);
                },

                setMarker(lat, lng, center = true) {
                    const self = this;
                    self.currentLat = lat;
                    self.currentLng = lng;

                    if (self.marker) {
                        self.marker.setLatLng([lat, lng]);
                    } else {
                        self.marker = L.marker([lat, lng], { draggable: true }).addTo(self.map);
                        self.marker.on('dragend', function(event) {
                            const position = event.target.getLatLng();
                            self.currentLat = position.lat;
                            self.currentLng = position.lng;
                            self.statusBanner = {
                                type: 'success',
                                icon: '📍',
                                text: 'Joylashuv yangilandi.'
                            };
                        });
                    }

                    if (center && self.map) {
                        self.map.setView([lat, lng], Math.max(self.map.getZoom(), 15));
                    }
                },

                detectCurrentLocation() {
                    const self = this;
                    self.locating = true;
                    self.showPermissionHelp = false;
                    self.statusBanner = { type: '', icon: '', text: '' };

                    // 1. Agar brauzerda umuman geolocation bo'lmasa
                    if (!navigator.geolocation) {
                        self.locating = false;
                        self.fallbackToIpLocation("Qurilmangizda GPS funksiyasi topilmadi.");
                        return;
                    }

                    // 2. Agar context xavfsiz bo'lmasa (HTTP) va localhost bo'lmasa
                    const isSecure = window.isSecureContext || location.hostname === 'localhost' || location.hostname === '127.0.0.1';

                    // GPS orqali sinab ko'ramiz
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            self.locating = false;
                            self.setMarker(position.coords.latitude, position.coords.longitude, true);
                            self.statusBanner = {
                                type: 'success',
                                icon: '🎯',
                                text: 'Joriy aniq GPS joylashuvingiz muvaffaqiyatli aniqlandi!'
                            };
                            self.showPermissionHelp = false;
                        },
                        (error) => {
                            // Agar aniqlab bo'lmasa yoki ruxsat berilmagan bo'lsa
                            self.locating = false;
                            self.showPermissionHelp = true;

                            let reason = "Geolakatsiyaga ruxsat berilmadi yoki xavfsizlik cheklovi mavjud.";
                            if (error.code === 1) {
                                reason = !isSecure 
                                    ? "Sayt HTTPS orqali ochilmaganligi sababli brauzer GPS ni chekladi." 
                                    : "Brauzerda lokatsiyaga ruxsat berilmagan.";
                            } else if (error.code === 2) {
                                reason = "Qurilmangizda GPS signali topilmadi.";
                            } else if (error.code === 3) {
                                reason = "Joylashuvni aniqlash vaqti tugadi.";
                            }

                            // Darhol IP orqali taxminiy manzilni o'rnatamiz
                            self.fallbackToIpLocation(reason);
                        },
                        { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
                    );
                },

                fallbackToIpLocation(reasonText) {
                    const self = this;
                    // Backend orqali taxminiy manzil olish
                    fetch('/api/location/approximate')
                        .then(res => res.json())
                        .then(data => {
                            if (data && data.lat && data.lng) {
                                self.setMarker(data.lat, data.lng, true);
                                self.statusBanner = {
                                    type: 'warning',
                                    icon: '⚠️',
                                    text: `<strong>${reasonText}</strong><br>IP orqali taxminiy (${data.city || 'shahar'}) joylashuvi belgilandi. Xaritani bosib aniq do'koningiz joyiga suring yoki telefoningizda oching.`
                                };
                            } else {
                                self.statusBanner = {
                                    type: 'warning',
                                    icon: '⚠️',
                                    text: `<strong>${reasonText}</strong> Iltimos xaritani bosib do'koningiz manzilini belgilang.`
                                };
                            }
                        })
                        .catch(() => {
                            // Agar API ishlamasa, default Toshkent markazini ko'rsatish
                            if (!self.currentLat) {
                                self.setMarker(41.311081, 69.240562, true);
                            }
                            self.statusBanner = {
                                type: 'warning',
                                icon: '⚠️',
                                text: `<strong>${reasonText}</strong> Iltimos xaritani bosib do'koningiz manzilini belgilang.`
                            };
                        });
                },

                parsePastedLink() {
                    const self = this;
                    const text = self.pastedLink.trim();
                    if (!text) return;

                    // 1. Oddiy koordinatalar formati: "41.311081, 69.240562" yoki "41.311081 69.240562"
                    const coordRegex = /(-?\d+\.\d+)[\s,]+(-?\d+\.\d+)/;
                    const matchCoord = text.match(coordRegex);
                    if (matchCoord) {
                        const lat = parseFloat(matchCoord[1]);
                        const lng = parseFloat(matchCoord[2]);
                        if (!isNaN(lat) && !isNaN(lng)) {
                            self.setMarker(lat, lng, true);
                            self.pastedLink = '';
                            self.statusBanner = {
                                type: 'success',
                                icon: '✓',
                                text: 'Koordinatalar kiritildi va xaritada belgilandi.'
                            };
                            return;
                        }
                    }

                    // 2. Yandex Maps: ll=69.240,41.311 yoki rtext=...~41.311,69.240
                    const yandexLl = text.match(/[?&]ll=(-?\d+\.\d+)%2C(-?\d+\.\d+)/i) || text.match(/[?&]ll=(-?\d+\.\d+),(-?\d+\.\d+)/i);
                    if (yandexLl) {
                        // Yandex ll parametri: [lng, lat]
                        const lng = parseFloat(yandexLl[1]);
                        const lat = parseFloat(yandexLl[2]);
                        self.setMarker(lat, lng, true);
                        self.pastedLink = '';
                        self.statusBanner = {
                            type: 'success',
                            icon: '✓',
                            text: 'Yandex Maps havolasidan lokatsiya aniqlandi.'
                        };
                        return;
                    }

                    // 3. Google Maps: @41.311,69.240 or q=41.311,69.240 or destination=41.311,69.240
                    const googleMatch = text.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/) || text.match(/[?&](?:q|destination)=(-?\d+\.\d+),(-?\d+\.\d+)/);
                    if (googleMatch) {
                        const lat = parseFloat(googleMatch[1]);
                        const lng = parseFloat(googleMatch[2]);
                        self.setMarker(lat, lng, true);
                        self.pastedLink = '';
                        self.statusBanner = {
                            type: 'success',
                            icon: '✓',
                            text: 'Google Maps havolasidan lokatsiya aniqlandi.'
                        };
                        return;
                    }

                    self.statusBanner = {
                        type: 'error',
                        icon: '✕',
                        text: 'Havoladan koordinata topilmadi. Koordinatani "41.311, 69.240" formatida kiriting yoki to\'g\'ridan-to\'g\'ri xaritani bosing.'
                    };
                },

                resetLocation() {
                    const self = this;
                    self.currentLat = null;
                    self.currentLng = null;
                    self.statusBanner = { type: '', icon: '', text: '' };
                    self.showPermissionHelp = false;
                    if (self.marker) {
                        self.map.removeLayer(self.marker);
                        self.marker = null;
                    }
                }
            };
        }
    </script>
@endonce
