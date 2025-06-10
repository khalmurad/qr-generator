# QR-kod generatori

Bu oddiy web-ilova bo‘lib, URL bo‘yicha QR-kodlar yaratadi va ularni A4 formatidagi PDF-fayllar sifatida chiqaradi. Ilovada mijoz (client) va server tomonida validatsiya amalga oshiriladi, bu faqat to‘g‘ri URL manzillari qayta ishlanishini ta’minlaydi. Shuningdek, turli formatdagi fayllarni yuklash va umumiy havola olish funksiyalari ham mavjud.

## Loyihaning tavsifi

Ushbu loyiha PHP asosidagi QR-kod generatori bo‘lib, foydalanuvchilarga URL bo‘yicha QR-kodlarni yaratish va ularni PDF-fayl sifatida yuklab olish imkonini beradi. QR-kodlarni yaratishda Endroid QR Code kutubxonasi, PDF-hujjatlarni yaratish va QR-kodlarni foydalanuvchi shriftlari bilan birga joylashtirishda TCPDF ishlatiladi. Ilova oddiy va tushunarli interfeysga ega bo‘lib, ikkita asosiy funksiyani taklif etadi: havola orqali QR-kod yaratish hamda fayl yuklab uning umumiy havolasi olish va uning QR-kodini yaratish.

## Imkoniyatlari

* URL bo‘yicha QR-kodlar yaratish
* Fayllarni yuklash (PNG, JPG, JPEG, PDF, PPT, PPTX, DOC, DOCX) va umumiy havola olish
* QR-kodga sarlavha qo‘shish
* Natijani A4 formatidagi PDF sifatida yuklab olish
* Mijoz va server tomonida validatsiya
* Moslashuvchan dizayn
* PDFga Times New Roman shriftini joylashtirish

## Talablar

* PHP 8.3 yoki undan yuqori versiya
* PHP mbstring kengaytmasi
* Composer
* Veb-server (Apache, Nginx va boshqalar)
* `qrcodes` va `uploads` papkalariga yozish huquqi

## O‘rnatish

1. Ushbu repozitoriyani veb-server katalogingizga klonlang:

   ```bash
   git clone https://github.com/yourusername/qr-generator.git
   ```

2. Loyihaning papkasiga o‘ting:

   ```bash
   cd qr-generator
   ```

3. Composer orqali bog‘liqliklarni o‘rnating (majburiy):

   ```bash
   composer install
   ```

   **Muhim**: ilova bog‘liqliklar o‘rnatilmasa ishlamaydi. Agar `vendor/autoload.php` topilmasa, ushbu buyruqni bajarishingiz lozim.

4. `qrcodes` va `uploads` papkalariga veb-server yozish huquqiga ega ekanligini tekshiring:

   ```bash
   sudo mkdir qrcodes uploads
   sudo chown www-data:www-data qrcodes uploads
   sudo chmod 755 qrcodes uploads
   ```

5. `fonts` papkasi mavjud va kerakli shrift fayllari joylanganini tekshiring:

   ```bash
   ls -la fonts       # Papka mavjudligini tekshirish  
   sudo mkdir -p fonts  # Yo‘q bo‘lsa yaratish  
   ```

6. Ilovani xizmatga qo‘yish uchun veb-serverni sozlang. Repozitoriyada Nginx konfiguratsiyasi namunasi (`nginx.conf`) mavjud, undan shablon sifatida foydalanish mumkin:

   ```bash
   sudo cp nginx.conf /etc/nginx/sites-available/qr-generator.conf
   sudo ln -s /etc/nginx/sites-available/qr-generator.conf /etc/nginx/sites-enabled/
   sudo nginx -t
   sudo nginx -s reload
   ```

## Foydalanish

### QR-kod yaratish uchun

1. Ilovani brauzerda oching.
2. “QR-kod nomi” maydoniga sarlavha kiriting.
3. “Havola” yorlig‘iga o‘ting.
4. “QR-kod havolasi” maydoniga kerakli URLni kiriting.
5. “QR-kod yaratish” tugmasini bosing va PDFni yuklab oling.
6. PDF A4 varaqlari ustida markazlashtirilgan QR-kod bilan avtomatik yuklanadi.

### Fayl yuklash uchun

1. Ilovani brauzerda oching.
2. “QR-kod nomi” maydoniga sarlavha kiriting.
3. “Fayl” yorlig‘iga o‘ting.
4. Yuklash joyini bosing va faylni tanlang (PNG, JPG, JPEG, PDF, PPT, PPTX, DOC, DOCX).
5. “QR-kod yaratish” tugmasini bosing, shunda umumiy havola hosil bo‘ladi.
6. Shu umumiy havola ostida joylashgan tugma orqali QR-kod PDF faylini yuklab oling.

## Testlash

Loyihada muhit sozlamalari to‘g‘ri o‘rnatilganini tekshirish uchun `test.php` skripti mavjud:

```bash
php test.php
```

Skript quyidagilarni tekshiradi:

* PHP versiyasi mosligini
* Zarur papkalar mavjudligini
* Composer bog‘liqliklari o‘rnatilganligini
* QR-kod yaratish funksiyasining ishlashini

O‘rnatishdan so‘ng skriptni ishga tushirib, hamma narsa to‘g‘ri ishlashini tasdiqlang.

## Ishlash prinsipi

### QR-kod yaratish

1. Formaga sarlavha va URLni kiriting.
2. Mijoz tomonidagi JavaScript URL formatini real vaqtda tekshiradi.
3. Forma yuborilganda, JavaScript oddiy yuborishni to‘xtatib, uni yashirin iframe orqali boshqaradi.
4. Server tomonida ma’lumotlar qayta tekshiriladi: majburiy maydonlar va URL formati.
5. endroid/qr-code kutubxonasi yordamida QR-kod yaratiladi va vaqtinchalik PNG-faylga saqlanadi.
6. TCPDF yordamida QR-kod PDF-hujjatga joylashtiriladi, sarlavha Times New Roman shriftida chiqariladi.
7. PDF foydalanuvchiga noyob nom bilan yuklab olish uchun yuboriladi.
8. PDF yaratilgach, vaqtinchalik PNG-fayllar o‘chiriladi.

### Fayl yuklash

1. Foydalanuvchi sarlavha kiriting va faylni tanlang.
2. Forma yuborilganda, fayl serverga uzatiladi.
3. Server fayl turini qo‘llab-quvvatlanadigan formatlarga mosligini tekshiradi.
4. Fayl sana bo‘yicha katalog strukturasi (yil/oy/kun) bo‘yicha saqlanadi.
5. Sarlavha, vaqt meta-ma’lumotlari va asl fayl nomiga asoslanib noyob nom hosil qilinadi.
6. Server yuklangan faylga umumiy havolani qaytaradi.
7. Havola foydalanuvchiga ko‘rsatiladi, nusxalab foydalanish mumkin.

## Litsenziya

Loyiha MIT litsenziyasi ostida tarqatiladi — batafsil ma’lumot LICENSE faylida.

## Minnatdorchilik

* [TCPDF](https://github.com/tecnickcom/TCPDF) — PDF yaratish va shrift o‘rnatish uchun
* [endroid/qr-code](https://github.com/endroid/qr-code) — QR-kod yaratish uchun
