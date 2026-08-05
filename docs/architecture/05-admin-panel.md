# 5. Admin Panel Arxitekturasi (Filament v3)

## 5.1 Nega Filament?

- Laravel-native (Livewire + Alpine + Tailwind) — talab qilingan ekotizimdan chiqmaydi.
- Har bir modul o'z `Filament/Resources/XResource.php` faylini beradi — modulli arxitekturaga mos.
- RelationManager'lar orqali "Bilim daraxti" munosabatlarini (Motorcycle → Specifications, Images, Reviews, Listings) bitta ekranda tahrirlash mumkin.
- spatie/laravel-permission bilan tayyor integratsiya (`FilamentShield` paketi orqali resurs darajasida ruxsatlar avtomatik generatsiya qilinadi).
- Frontend (foydalanuvchi qismi) bunga bog'liq emas — u sof Blade + Alpine bo'lib qoladi.

## 5.2 Rollar va ruxsatlar

| Rol | Kirish huquqi |
|---|---|
| **Super Admin** | Hammasi, shu jumladan Users va Roles |
| **Content Manager** | Brands, Motorcycles, Specifications, Videos, News, Categories |
| **Moderator** | Listings, Community Posts/Comments, Reviews, Reports (faqat tasdiqlash/rad etish) |
| **Support** | Users (faqat ko'rish + ban/unban), Messages monitoring (shikoyat bo'yicha) |
| **Seller / Service Provider** | Admin panelga kirmaydi — o'z kabinetlari `/market/my`, `/services/my` orqali frontendda |

## 5.3 Dashboard vidjetlari

- Umumiy statistika kartalari: Jami mototsikllar, Faol e'lonlar, Yangi foydalanuvchilar (7 kun), Kutilayotgan moderatsiya soni
- "So'nggi 30 kun" grafik: ro'yxatdan o'tishlar, yangi e'lonlar, AI so'rovlar soni
- "Eng ko'p ko'rilgan mototsikllar" reyting jadvali
- "Kutilayotgan moderatsiya" tezkor havolalar (listings, reviews, reports)

## 5.4 Resurslar (modul bo'yicha guruhlangan navigatsiya)

**Katalog** guruhi:
- `BrandResource` — logo, mamlakat, faollik
- `MotorcycleResource` — tab'lar: General / Specifications / Media / Pros & Cons / Engine Details / Related Models
  - RelationManagers: `ReviewsRelationManager` (faqat ko'rish/moderatsiya), `ListingsRelationManager`, `VideosRelationManager`
- `MotorcycleCategoryResource`

**Bozor** guruhi:
- `ListingResource` — filtr: status=pending birinchi ko'rinadi, bulk action "Tasdiqlash/Rad etish"
- `ListingReportResource`

**Ehtiyot qismlar** guruhi:
- `PartCategoryResource` (nested/tree ko'rinish)
- `PartResource` — `motorcycles()` BelongsToMany maydon (moslik tanlash, multi-select)

**Servislar** guruhi:
- `ServiceProviderResource` — `verified` toggle, xarita koordinatasi kiritish maydoni (lat/lng picker)
- `ServiceCategoryResource`

**Video va Kontent** guruhi:
- `VideoResource`, `VideoCategoryResource`
- `NewsResource`, `NewsCategoryResource`

**Hamjamiyat** guruhi:
- `CommunityGroupResource`
- `PostResource` (moderatsiya: hidden qilish, reported filtri)
- `CommentResource`

**Foydalanuvchilar** guruhi (faqat Super Admin/Support):
- `UserResource` — ban/unban, rol biriktirish
- `RoleResource` (FilamentShield)

**AI** guruhi:
- `AiConversationResource` — faqat ko'rish (log), filtr: sana, foydalanuvchi
- Widget: "Eng ko'p tavsiya etilgan modellar" (ai_messages.meta asosida)

**Sozlamalar**:
- `SettingsPage` (Filament custom Page) — sayt nomi, homepage banner boshqaruvi, SEO default qiymatlar

## 5.5 Moderatsiya jarayoni (UX detali)

Har bir moderatsiya talab qiladigan resurs (`ListingResource`, `PostResource`, `MotorcycleReviewResource`) uchun:
- Default table filter: `status = pending`
- Table action: ✅ Tasdiqlash, ❌ Rad etish (modal orqali sabab kiritish → foydalanuvchiga notification ketadi)
- Bulk action: ko'p yozuvni bir vaqtda tasdiqlash

## 5.6 Audit

`spatie/laravel-activitylog` orqali barcha admin CRUD harakatlari avtomatik loglanadi (Filament plugin: `filament-spatie-laravel-activitylog`). `/admin/activity-log` sahifasida kim, qachon, nimani o'zgartirgani ko'rinadi — bu ko'p adminli katta platforma uchun majburiy.

## 5.7 Kelajakdagi kengaytirish

- Har bir yangi modul (`Modules/Insurance`, `Modules/Financing` va h.k.) qo'shilganda, shu modul ichida `Filament/Resources/` papkasi yaratiladi va `FilamentServiceProvider` orqali avtomatik ro'yxatdan o'tadi — asosiy admin kodiga tegmasdan.
- Ko'p tillilikka o'tilganda, Filament resurslar `spatie/laravel-translatable` uchun tayyor plugin (`filament-translatable-fields`) bilan to'g'ridan-to'g'ri ishlaydi.
