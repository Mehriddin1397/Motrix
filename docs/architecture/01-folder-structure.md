# 1. Loyiha Papka Tuzilmasi

Motrix **modulli monolit** (modular monolith) arxitekturasida quriladi: `nwidart/laravel-modules` paketi yordamida har bir biznes-domen o'zining Model, Controller, Migration, Route, View va Test qismlariga ega mustaqil modul bo'ladi. Bu kelajakda har qanday modulni alohida xizmatga (microservice) ajratishni ham osonlashtiradi.

```
motrix/
├── app/
│   ├── Console/Commands/
│   ├── Exceptions/
│   ├── Filament/                      # Global Filament resurslar (Users, Roles, Settings)
│   │   ├── Resources/
│   │   ├── Widgets/
│   │   └── Pages/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/V1/                # Mobil/PWA uchun umumiy API kontrollerlar
│   │   ├── Middleware/
│   │   ├── Requests/
│   │   └── Resources/                 # API Resource (JSON transformer) klasslari
│   ├── Models/                        # Faqat umumiy (cross-module) modellar: User, City, Country, Settings
│   ├── Policies/
│   ├── Providers/
│   ├── Services/                      # Umumiy servislar: ImageOptimizer, GeoService, NotificationService
│   ├── Support/                       # Helper klasslar, Enum'lar
│   └── View/Components/               # Umumiy Blade komponentlar (Button, Card, BottomNav)
│
├── Modules/                           # nwidart/laravel-modules — har biri mustaqil domen
│   ├── Brand/
│   ├── Motorcycle/                    # Yadro modul — katalog
│   │   ├── Config/
│   │   ├── Console/
│   │   ├── Database/
│   │   │   ├── Migrations/
│   │   │   ├── Seeders/
│   │   │   └── Factories/
│   │   ├── Entities/                  # Eloquent modellar (Motorcycle, Specification, Review...)
│   │   ├── Filament/Resources/        # MotorcycleResource + RelationManagers
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   ├── Requests/
│   │   │   └── Resources/
│   │   ├── Providers/
│   │   ├── Resources/
│   │   │   ├── views/                 # catalog/index.blade.php, catalog/show.blade.php...
│   │   │   └── lang/uz/
│   │   ├── Routes/
│   │   │   ├── web.php
│   │   │   └── api.php
│   │   └── Tests/
│   ├── Comparison/                    # Taqqoslash moduli
│   ├── Market/                        # Moto bozori (e'lonlar, xabarlashuv)
│   ├── Parts/                         # Ehtiyot qismlar
│   ├── ServiceCenter/                 # Servislar, ustalar, xarita
│   ├── Video/                         # Video platforma
│   ├── Community/                     # Hamjamiyat (post, guruh, savol-javob)
│   ├── Review/                        # Foydalanuvchi fikrlari (motorcycle_reviews)
│   ├── AiAssistant/                   # AI yordamchi (chat, recommendation engine)
│   ├── News/                          # Yangiliklar
│   └── UserProfile/                   # Profil, sozlamalar, follow tizimi
│
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php          # Asosiy mobil-first layout (top bar + bottom nav)
│   │   │   └── admin.blade.php        # (Filament o'zi boshqaradi, kerak bo'lmasligi mumkin)
│   │   ├── components/
│   │   │   ├── bottom-nav.blade.php
│   │   │   ├── top-bar.blade.php
│   │   │   ├── card/                  # motorcycle-card, listing-card, video-card
│   │   │   └── skeleton/              # loading skeleton komponentlar
│   │   └── partials/
│   ├── css/app.css                    # Tailwind entry
│   └── js/
│       ├── app.js                     # Alpine.js init
│       ├── alpine/                    # Alpine komponentlar (x-data moduls)
│       └── pwa/
│           ├── register-sw.js
│           └── push.js
│
├── routes/
│   ├── web.php                        # Faqat umumiy route'lar, qolgani modullarda
│   ├── api.php
│   ├── admin.php                      # Filament panel bootstrap (agar kerak bo'lsa)
│   └── channels.php                   # Broadcast (real-time xabarlar)
│
├── database/
│   ├── migrations/                    # Faqat core jadvallar: users, cities, countries, settings
│   ├── seeders/
│   └── factories/
│
├── public/
│   ├── build/
│   ├── manifest.json                  # PWA manifest
│   ├── sw.js                          # Service worker (Workbox orqali generatsiya)
│   └── icons/                         # PWA ikonkalari (turli o'lchamlar)
│
├── storage/
│   └── app/public/                    # spatie/media-library shu yerga saqlaydi
│
├── tests/
│   ├── Feature/
│   └── Unit/
│
├── config/
│   └── modules.php
│
├── docs/
│   └── architecture/                  # Ushbu hujjatlar
│
├── .env.example
├── composer.json
├── package.json
├── tailwind.config.js
└── vite.config.js
```

## Modul ichidagi standart tuzilma (namuna: `Modules/Parts`)

```
Modules/Parts/
├── Entities/
│   ├── Part.php
│   ├── PartCategory.php
│   └── PartMotorcycleCompatibility.php   # pivot model
├── Http/Controllers/
│   ├── PartController.php                # public: index/show
│   └── SellerPartController.php          # sotuvchi uchun CRUD
├── Filament/Resources/
│   └── PartResource.php
├── Database/Migrations/
│   ├── 2026_01_01_000001_create_part_categories_table.php
│   ├── 2026_01_01_000002_create_parts_table.php
│   └── 2026_01_01_000003_create_part_motorcycle_table.php
├── Resources/views/
│   ├── index.blade.php
│   ├── show.blade.php
│   └── category.blade.php
├── Routes/web.php
└── module.json
```

## Nega modulli monolit?

- **Izolyatsiya**: Har bir jamoa a'zosi (yoki kelajakdagi jamoa) o'z modulida ishlashi mumkin, konflikt kam.
- **Kengaytirish**: Yangi modul (masalan, "Sug'urta" yoki "Kredit") qo'shish — mavjud kodga tegmasdan yangi papka yaratish.
- **Test qilish osonligi**: Har bir modul o'z testlariga ega.
- **Kelajakda mikroservisga o'tish**: Agar `Parts` yoki `Market` alohida xizmat sifatida ajratish kerak bo'lsa, modul chegaralari allaqachon aniq.
