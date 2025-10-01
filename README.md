# Pi Studio Pilates Web Platform

Bu depo, Pi Studio Pilates stüdyosu için hazırlanan çok dilli altyapıya hazır modern web sitesi ve yönetim panelini içerir. Uygulama sade PHP ve MySQL kullanılarak geliştirilmiştir.

## Özellikler

- Hero, ekipmanlar, eğitmen bilgisi, eğitimler, planlar, ders programı, S.S.S. ve iletişim bölümleriyle zengin ön yüz.
- Yönetim paneli sayesinde tüm içerikleri güncelleme, yeni kayıt ekleme ve mesajları görüntüleme.
- Güvenli oturum yönetimi, CSRF koruması ve bcrypt ile şifrelenmiş admin kullanıcı.
- Google Maps gömme, WhatsApp kısa yolu ve SEO odaklı meta alanları.
- Tüm görseller `assets/img/` klasöründe saklanır.

## Kurulum

1. Depoyu sunucuya aktarın ve web kökünü `public/` klasörüne yönlendirin (veya sanal host yapılandırmasıyla).
2. `database/schema.sql` dosyasını phpMyAdmin üzerinden içe aktararak gerekli tabloları oluşturun.
3. `config.php` dosyasında sunucu ortamınıza uygun veritabanı bilgilerini güncelleyin.
4. Admin paneline `https://alanadiniz.com/admin/login.php` adresinden erişebilirsiniz.
   - E-posta: `pinar@pistudiopilates.com`
   - Şifre: `pistudio2025!`
5. Gerektiğinde `.htaccess` veya sunucu yapılandırmasıyla `public` klasörü kök olarak ayarlanabilir.

## Geliştirme

- İçerik düzenlemeleri için yönetim panelinde ilgili bölümlerdeki formları kullanın.
- Yeni görseller yüklediğinizde dosyalar otomatik olarak `assets/img/` altına kaydedilir.
- Çok dillilik için `public/index.php` içinde gerekli metinler yönetim panelinden güncellenebilir. Opsiyonel olarak JSON tabanlı çeviri sistemi eklemek için `config.php` altında ek yapılandırmalar yapılabilir.

## Komutlar

- Yerel geliştirme için PHP yerleşik sunucusunu kullanabilirsiniz: `php -S localhost:8000 -t public`

## Dağıtım

- Üretim derlemesi gerekmez; `public/` klasörünü FTP ile sunucuya gönderin.
- `assets/` klasörü ve `config.php` dahil olmak üzere tüm dosyaların sunucuda bulunduğundan emin olun.
- Veritabanı yapılandırması tamamlandıktan sonra site ve admin paneli kullanıma hazırdır.

## Lisans

Bu proje Pi Studio Pilates için özelleştirilmiştir.
