# Motrix — Arxitektura Hujjatlari

Bu papka Motrix platformasining kodga o'tishdan oldingi to'liq arxitektura loyihasini o'z ichiga oladi. Har bir fayl alohida masalaga bag'ishlangan:

1. [01-folder-structure.md](./01-folder-structure.md) — Loyiha papka tuzilmasi
2. [02-database-schema.md](./02-database-schema.md) — Ma'lumotlar bazasi arxitekturasi (jadvallar va bog'lanishlar)
3. [03-sitemap.md](./03-sitemap.md) — Sayt sahifalari xaritasi
4. [04-user-flow.md](./04-user-flow.md) — Foydalanuvchi oqimlari
5. [05-admin-panel.md](./05-admin-panel.md) — Admin panel arxitekturasi

## Tanlangan texnologik stack

| Qaror | Tanlov | Sabab |
|---|---|---|
| Backend | Laravel 11.x (PHP 8.3+) | Talab qilingan |
| Frontend | Blade + Alpine.js + Tailwind CSS | Mobile-first, yengil, PWA'ga mos |
| Admin panel | **Filament v3** | TALL stack, Laravel-native, tez CRUD, RBAC bilan integratsiya, modul asosida resurslar |
| Modul arxitekturasi | **nwidart/laravel-modules** | Har bir biznes-domen (Brand, Motorcycle, Market, Parts, Service...) alohida, mustaqil kengaytiriladigan modul sifatida |
| Autentifikatsiya | Laravel Breeze (Blade variant) + Laravel Sanctum (API/PWA uchun) | Sodda, to'liq nazorat qilinadigan auth |
| Ruxsatlar | spatie/laravel-permission | Rol va huquqlar (admin, moderator, sotuvchi, usta, user) |
| Media (rasm/video) | spatie/laravel-medialibrary | Barcha modullarda (motorcycles, listings, parts, videos) yagona media boshqaruvi |
| Slug | spatie/laravel-sluggable | SEO-friendly URL'lar |
| Audit log | spatie/laravel-activitylog | Admin harakatlari tarixi |
| Qidiruv | Laravel Scout + Meilisearch | Katalog, bozor, ehtiyot qismlar bo'yicha tez qidiruv (kelajakda millionlab yozuvlar uchun) |
| Sitemap/SEO | spatie/laravel-sitemap + spatie/schema-org | SEO talablari |
| Real-time (xabarlar, bildirishnoma) | Laravel Reverb yoki Pusher | Hamjamiyat va bozor xabarlashuvi uchun |
| Til | Faqat o'zbek tili (hozircha) | Kelajakda `spatie/laravel-translatable` bilan kengaytiriladi (schema shunga tayyor qilib qurilgan) |
| PWA | Vite + Workbox (service worker) | "Mobil ilova" tajribasi uchun |

## Muhim arxitektura tamoyili — "Bilim daraxti"

Barcha modullar `motorcycle_id` yoki `brand_id` orqali bir-biriga bog'langan. Bitta mototsikl modeli sahifasi — bu turli modullardan (texnik ma'lumot, video, ehtiyot qism, bozor, servis, sharh) ma'lumotlarni bitta joyga yig'uvchi **agregatsiya nuqtasi**. Bu DB darajasida FK (foreign key) orqali, kod darajasida esa har bir modulning `Motorcycle` modeliga qo'shadigan Eloquent relationship'lari orqali amalga oshiriladi (`HasMany`, `BelongsToMany`) — modullar bir-biriga to'g'ridan-to'g'ri bog'lanmaydi, balki umumiy `Motorcycle` modeli orqali bog'lanadi (loose coupling).

## Keyingi qadam

Ushbu hujjatlar tasdiqlangandan so'ng:
1. `laravel new motrix` bilan loyihani boshlash
2. Kerakli paketlarni o'rnatish (yuqoridagi jadval)
3. `nwidart/laravel-modules` orqali modullarni generatsiya qilish
4. Har bir modul uchun migratsiyalarni yozish (02-database-schema.md asosida)
5. Filament resurslarini yaratish
6. Frontend (Blade + Alpine) sahifalarini sitemap asosida qurish
