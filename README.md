# Webudi Test Case

API Dokümantasyonu <https://documenter.getpostman.com/view/16638043/2s8YzL36Ek>

---

## Projeyi Çalıştırma

1. Projeyi bilgisayarınıza indirin.
2. Proje ana dizininde ``composer install`` komutunu çalıştırın.
3. `.env.example` dosyasını `.env` olarak kopyalayın. Veritabanı bilgilerini ayarlayın.
4. `php artisan migrate` komutu ile tabloları oluşturun.
5. `php artisan queue:work` komutunu çalıştırın. Bu komut kuyruktaki işleri çalıştırmayı sağlayacak.
6. `php artisan sync:spotify` komutu ile işleri kuyruğa alın.

---

#### Not

Eğer verilerin 30 dakikada bir güncellenmesini istiyorsanız `php artisan schedule:work` komutunu çalıştırın. Bu komut Spotify'dan belirtilen sürelerde senkronizasyon yapılmasını sağlayacak.
