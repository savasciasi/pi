# Pi Studio Pilates Web Platform

Bu depo, Pi Studio Pilates stüdyosu için hazırlanan çok dilli altyapıya hazır modern web sitesi ve yönetim panelini içerir. Uygulama sade PHP ve MySQL kullanılarak geliştirilmiştir.

## Özellikler

- Hero, ekipmanlar, eğitmen bilgisi, eğitimler, planlar, ders programı, S.S.S. ve iletişim bölümleriyle zengin ön yüz.
- Yönetim paneli sayesinde tüm içerikleri güncelleme, yeni kayıt ekleme ve mesajları görüntüleme.
- Güvenli oturum yönetimi, CSRF koruması ve bcrypt ile şifrelenmiş admin kullanıcı.
- Google Maps gömme, WhatsApp kısa yolu ve SEO odaklı meta alanları.
- Tüm görsel yolları kodda referans olarak bulunur ancak depoda herhangi bir görsel dosyası yer almaz.

> Bu repoda görseller yoktur, FTP ile `assets/img/` klasörüne kendiniz yükleyeceksiniz. Admin panelinden yüklenenler `assets/img/uploads/` içine kaydedilir. Yüklenmeyen resimler otomatik olarak placeholder ile gösterilir.

## Kurulum

1. Tüm projeyi XAMPP veya paylaşımlı hosting ortamınızda `htdocs/pistudio` klasörünün içine kopyalayın.
2. `pistudio.sql` dosyasını phpMyAdmin üzerinden içe aktararak veritabanı tablolarını ve örnek içerikleri oluşturun.
3. `config/config.php` dosyasındaki veritabanı bağlantı ayarlarını (sunucu, kullanıcı adı, şifre) ortamınıza göre güncelleyin.
4. `assets/img/` klasörüne gerekli görselleri manuel olarak yükleyin. **Bu repoda görseller yoktur, FTP ile `assets/img/` klasörüne kendiniz yükleyeceksiniz. Admin panelinden yüklenenler `assets/img/uploads/` içine kaydedilir. Yüklenmeyen resimler otomatik olarak placeholder ile gösterilir.** Kullanacağınız dosya adları kodda ilgili bileşenler içerisinde referans olarak yer almaktadır.
5. Admin paneline `https://alanadiniz.com/admin/login.php` adresinden giriş yapın.
   - E-posta: `pinar@pistudiopilates.com`
   - Şifre: `pistudio2025!`
6. İhtiyaç halinde FTP veya sunucu yapılandırmasıyla web kökünü `public/` klasörüne yönlendirin.

## Geliştirme

- İçerik düzenlemeleri için yönetim panelinde ilgili bölümlerdeki formları kullanın.
- Yeni görseller yüklediğinizde dosyalar otomatik olarak `assets/img/uploads/` altına kaydedilir.
- Çok dillilik için `public/index.php` içinde gerekli metinler yönetim panelinden güncellenebilir. Opsiyonel olarak JSON tabanlı çeviri sistemi eklemek için `config/config.php` altında ek yapılandırmalar yapılabilir.

## Komutlar

- Yerel geliştirme için PHP yerleşik sunucusunu kullanabilirsiniz: `php -S localhost:8000 -t public`

## Dağıtım

- Üretim derlemesi gerekmez; `public/` klasörünü FTP ile sunucuya gönderin.
- `assets/` klasörü ve `config/config.php` dahil olmak üzere tüm dosyaların sunucuda bulunduğundan emin olun.
- Veritabanı yapılandırması tamamlandıktan sonra site ve admin paneli kullanıma hazırdır.

## Lisans

Bu proje Pi Studio Pilates için özelleştirilmiştir.
