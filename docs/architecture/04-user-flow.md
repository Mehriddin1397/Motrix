# 4. Foydalanuvchi Oqimlari (User Flow)

## 4.1 Mehmon (Guest) oqimi

```
Kirish (/) → Ko'rish rejimi:
  ├── Katalog, model sahifalari, video, yangiliklar — TO'LIQ ochiq (SEO uchun ham muhim)
  ├── Bozor e'lonlari — ko'rish ochiq, sotuvchi bilan bog'lanish uchun login talab qilinadi
  ├── AI yordamchi — 2-3 ta bepul savol so'rash mumkin, keyin "Davom etish uchun ro'yxatdan o'ting"
  └── Har qanday "saqlash / like / izoh / e'lon joylash" harakati → Login/Register modal
```

## 4.2 Ro'yxatdan o'tish va onboarding

```
Register (telefon yoki email)
   ↓
SMS/Email tasdiqlash
   ↓
Onboarding (ixtiyoriy, "O'tkazib yuborish" tugmasi bilan):
   - Tajriba darajangiz? (Yangi boshlovchi / O'rta / Tajribali)
   - Bo'yingiz? (AI tavsiyasi uchun)
   - Byudjetingiz?
   - Qaysi turdagi mototsikl qiziqtiradi? (Sport/Cruiser/...)
   ↓
Bosh sahifa (endi "Sizga tavsiya" bo'limi shaxsiylashtirilgan)
```

> Onboarding ma'lumotlari `users` jadvaliga yoziladi va AI yordamchi so'rov kelganda kontekst sifatida avtomatik ishlatiladi (foydalanuvchi qayta yozmasligi uchun).

## 4.3 Mototsikl kashf qilish → sotib olish yo'li (asosiy "knowledge tree" oqimi)

```
Katalog / Qidiruv / Bosh sahifa kartochkasi
   ↓
Model sahifasi (/motorcycles/yamaha/r1)
   ├── Texnik xususiyatlar, tarix, ichki tuzilish
   ├── AI video, dvigatel animatsiyasi
   ├── [Taqqoslash tugmasi] → /compare?ids=...
   ├── "Sotuvdagi variantlar" bo'limi → filtrlangan /market?motorcycle=r1
   ├── "Ehtiyot qismlar" bo'limi → /parts/for/yamaha/r1
   ├── "Servislar" bo'limi (shu brendga ixtisoslashgan) → /services?brand=yamaha
   └── "Foydalanuvchi fikrlari" → sharh yozish (auth)
```

Bu — platformaning markaziy g'oyasi: **bitta model sahifasidan chiqmasdan** foydalanuvchi sotib olish, ehtiyot qism topish, servis topish va boshqalarning fikrini o'qish imkoniyatiga ega bo'ladi.

## 4.4 Sotish (Sell) oqimi

```
"Sotish" tugmasi (bozor sahifasida yoki profil ichida, FAB)
   ↓
Model tanlash: Katalogdan qidirish (autocomplete, motorcycle_id bog'lanadi)
   yoki "Katalogda yo'q" → qo'lda brend/model kiritish
   ↓
Rasm/video yuklash (kamida 3 ta rasm majburiy)
   ↓
Narx, yil, yurgan masofa, holati, joylashuv
   ↓
Tavsif yozish
   ↓
Ko'rib chiqish va yuborish → status: pending
   ↓
Admin/avtomoderatsiya (spam/tasvir tekshiruvi) → status: active
   ↓
E'lon jonli, xaritada va katalogda ko'rinadi
   ↓
Xaridor xabar yozadi → /messages/{conversation}
   ↓
Sotildi → sotuvchi "Sotildi" deb belgilaydi → status: sold (statistika uchun saqlanadi)
```

## 4.5 AI Yordamchi oqimi

```
Foydalanuvchi savol yozadi (matn yoki tayyor shablon: "Menga moto tavsiya qiling")
   ↓
Agar profil to'ldirilgan bo'lsa — bo'yi, byudjeti, tajribasi avtomatik kontekstga qo'shiladi
   ↓
AI backend:
   1. Savolni tahlil qiladi (intent: tavsiya / taqqoslash / texnik savol / moy-servis savoli)
   2. Structured DB'dan (motorcycles, specifications, parts) tegishli yozuvlarni qidiradi (RAG)
   3. Javobni generatsiya qiladi, ICHIDA haqiqiy model/qism/servis sahifalariga link beradi
   ↓
Javob chatda ko'rsatiladi, "Yamaha MT-03" kabi tavsiyalar bosiladigan kartochka sifatida chiqadi
   ↓
ai_messages.meta ga qaysi motorcycle_id tavsiya qilingani yoziladi (keyinchalik "AI eng ko'p nima tavsiya qiladi" analitikasi uchun)
```

## 4.6 Hamjamiyat oqimi

```
/community → Lenta (kuzatilayotgan foydalanuvchilar va guruhlar posti)
   ↓
Guruhga qo'shilish (masalan "Yamaha egalari" guruhi, brand_id bilan bog'liq)
   ↓
Post yozish / Savol berish (motorcycle_id bilan bog'lash ixtiyoriy — "R1 haqida savolim bor")
   ↓
Boshqalar izoh/like qoldiradi → bildirishnoma yuboriladi (real-time, Reverb/Pusher)
```

## 4.7 Sotuvchi/Usta (Service Provider) oqimi

```
Ro'yxatdan o'tish → "Xizmat ko'rsatuvchi sifatida ro'yxatdan o'tish" (rol: service_provider)
   ↓
Profil to'ldirish: kategoriya, manzil (xaritada belgilash), ish vaqti, qaysi brendlar bilan ishlaydi
   ↓
Admin tasdiqlaydi (verified = true) — ishonch belgisi profilida ko'rinadi
   ↓
/services ro'yxatida va tegishli motorcycle sahifalarida ko'rinadi
   ↓
Mijozlar sharh qoldiradi (rating_avg avtomatik yangilanadi)
```

## 4.8 Xolis (moderatsiya) oqimi — admin tomondan

```
Yangi e'lon/post/sharh → status: pending
   ↓
Admin panel "Kutilayotganlar" navbatida ko'rinadi
   ↓
Moderator ko'rib chiqadi → Tasdiqlash / Rad etish (sabab bilan) / Tahrirlash
   ↓
Foydalanuvchiga bildirishnoma (tasdiqlandi/rad etildi)
```
