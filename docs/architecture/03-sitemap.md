# 3. Sayt Sahifalari Xaritasi (Sitemap)

Mobile-first: har bir sahifa avvalo telefon ekrani uchun, keyin desktop uchun moslashadi (responsive, lekin "mobile app" tuyg'usi ustuvor).

## 3.1 Ochiq (Public) qism

```
/                                   Bosh sahifa
/motorcycles                        Katalog (barcha brendlar, filtr)
/motorcycles/{brand}                Brend sahifasi (masalan /motorcycles/yamaha)
/motorcycles/{brand}/{model}        Model sahifasi — "Bilim daraxti" markazi
/compare                            Taqqoslash (query: ?ids=1,5,9)
/compare/{token}                    Ulashiladigan taqqoslash linki

/market                             Bozor — e'lonlar ro'yxati (filtr: brend, narx, yil, shahar)
/market/{id}                        E'lon tafsiloti
/market/create                      E'lon joylash (auth)
/market/my                          Mening e'lonlarim (auth)
/market/saved                       Saqlangan e'lonlar (auth)
/messages                           Xabarlar (suhbatlar ro'yxati, auth)
/messages/{conversation}            Suhbat sahifasi (auth)

/parts                              Ehtiyot qismlar katalogi
/parts/{category}                   Kategoriya bo'yicha (masalan /parts/dvigatel-qismlari)
/parts/{id}                         Qism tafsiloti
/parts/for/{brand}/{model}          Aniq modelga mos qismlar (motorcycle sahifasidan link)

/services                           Servislar ro'yxati
/services/map                       Xaritada ko'rish
/services/{id}                      Servis/usta tafsiloti

/videos                             Video platforma bosh sahifasi
/videos/{category}                  Kategoriya bo'yicha
/videos/{id}                        Video ko'rish sahifasi

/community                          Hamjamiyat lentasi
/community/groups                   Guruhlar ro'yxati
/community/groups/{slug}            Guruh sahifasi
/community/posts/{id}               Post/savol tafsiloti
/community/create                   Post/savol yozish (auth)

/news                                Yangiliklar ro'yxati
/news/{slug}                         Yangilik tafsiloti

/ai-assistant                        AI yordamchi chat interfeysi

/profile/{username}                  Foydalanuvchi ochiq profili
/settings/profile                    Profil sozlamalari (auth)
/settings/account                    Akkaunt sozlamalari (auth)
/settings/appearance                 Dark/Light mode
/notifications                       Bildirishnomalar (auth)

/auth/login
/auth/register
/auth/forgot-password
/auth/verify
```

## 3.2 Mobil bosh ekran tuzilmasi

**Yuqori panel (top bar)** — barcha asosiy sahifalarda umumiy:
```
[ ☰/Logo ]        [ 🔍 Qidiruv ]        [ 👤 Profil/Avatar ]
```

**Pastki navigatsiya (bottom nav)** — 5 ta asosiy bo'lim, doim ko'rinadi:
```
🏠 Bosh sahifa | 🏍️ Mototsikllar | 🛒 Bozor | 🎬 Video | 👤 Profil
```

> `Parts`, `Services`, `Community`, `AI Assistant` — bosh sahifadagi kartochkalar va "Mototsikllar"/"Profil" ichidagi tez havolalar orqali ochiladi (5 tadan ortiq bottom-nav tugmasi mobil UX'ni buzadi — Instagram/Airbnb standarti: 5 ta band).

## 3.3 Bosh sahifa bo'limlari tartibi (yuqoridan pastga)

1. Top bar (logo, qidiruv, profil)
2. Hero / banner karusel (aksiyalar, yangi modellar)
3. Tezkor kategoriyalar (Sport, Cruiser, Naked... — gorizontal scroll chip'lar)
4. "Mashhur mototsikllar" — gorizontal scroll kartochkalar
5. "Yangi qo'shilgan modellar"
6. "AI videolar" — gorizontal scroll
7. "Sotuvdagi e'lonlar" (bozordan tanlangan/eng yangi)
8. "Sizga tavsiya" (AI/algoritm asosida, agar login bo'lsa profil ma'lumotiga qarab)
9. "Yangiliklar" — 2-3 ta karta
10. Suzuvchi AI yordamchi tugmasi (floating action button, doim ekranda)
11. Bottom nav

## 3.4 Admin panel xaritasi

Batafsil: [05-admin-panel.md](./05-admin-panel.md)

```
/admin                              Dashboard
/admin/brands
/admin/motorcycles
/admin/motorcycles/{id}/edit        (tabs: General, Specs, Media, Pros/Cons, Related)
/admin/categories
/admin/listings                     (moderatsiya navbat bilan)
/admin/parts
/admin/service-providers
/admin/videos
/admin/community/groups
/admin/community/posts               (moderatsiya)
/admin/reviews                       (moderatsiya)
/admin/news
/admin/users
/admin/roles
/admin/reports                       (shikoyatlar)
/admin/settings
```
