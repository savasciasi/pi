SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

REPLACE INTO users (id, email, password, role) VALUES
(1, 'pinar@pistudiopilates.com', '$2y$12$FyNfSGYn9XVqC.IVVomureQ5UQorHB4y40LSLLv/V7bkmF1Sxh9ye', 'admin');

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    site_name VARCHAR(150) NOT NULL,
    logo VARCHAR(255) DEFAULT NULL,
    language VARCHAR(10) NOT NULL DEFAULT 'tr',
    contact_email VARCHAR(255) DEFAULT NULL,
    contact_phone VARCHAR(120) DEFAULT NULL,
    whatsapp_number VARCHAR(120) DEFAULT NULL,
    instagram_url VARCHAR(255) DEFAULT NULL,
    address VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

REPLACE INTO settings (id, site_name, logo, language, contact_email, contact_phone, whatsapp_number, instagram_url, address) VALUES
(1, 'Pi Studio Pilates', NULL, 'tr', 'info@pistudiopilates.com', '+90 530 111 22 33', '+90 530 111 22 33', 'https://www.instagram.com/pistudiopilates', 'Bağdat Caddesi No:123, İstanbul');

CREATE TABLE IF NOT EXISTS hero (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subtitle TEXT,
    cta_primary_text VARCHAR(120),
    cta_secondary_text VARCHAR(120),
    cta_primary_link VARCHAR(255),
    cta_secondary_link VARCHAR(255),
    background_media VARCHAR(255),
    address VARCHAR(255),
    map_embed TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

REPLACE INTO hero (id, title, subtitle, cta_primary_text, cta_secondary_text, cta_primary_link, cta_secondary_link, address, map_embed, background_media) VALUES
(1, 'Pi Studio Pilates', 'Vücudunu güçlendir, nefesinle ak.', 'Deneme Dersi Al', 'Ders Programı', '#contact', '#schedule', 'Bağdat Caddesi No:123, İstanbul', '', 'assets/img/hero-bg.jpg');

CREATE TABLE IF NOT EXISTS about_pilates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

REPLACE INTO about_pilates (id, content) VALUES
(1, 'Pilates; nefes, kontrol ve core gücünü temel alan kapsamlı bir zihin-beden pratiğidir. Doğru nefes ve hizalanma ile vücudu güçlendirirken zihinsel farkındalığı artırır.');

CREATE TABLE IF NOT EXISTS history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

REPLACE INTO history (id, content) VALUES
(1, '20. yüzyılın başında Joseph Pilates tarafından geliştirilen kontroloji, modern pilates pratiğinin temelini oluşturur. Pi Studio Pilates bu mirası günümüz bilimiyle buluşturur.');

CREATE TABLE IF NOT EXISTS instructor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    bio TEXT,
    highlights TEXT,
    photo VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

REPLACE INTO instructor (id, name, bio, highlights, photo) VALUES
(1, 'Pınar Sarı Koçak', 'Pınar Sarı Koçak, hareket anatomi bilgisi ve kişiye özel pilates yaklaşımıyla bedeninizi yeniden keşfetmenizi sağlar.', 'Mat Pilates\nReformer Pilates\nRehabilitasyon odaklı programlama\nPrenatal & Postnatal destek', 'assets/img/pinar.jpg');

CREATE TABLE IF NOT EXISTS equipments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(120) NOT NULL,
    description TEXT,
    more_info TEXT,
    img VARCHAR(255),
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

TRUNCATE TABLE equipments;

INSERT INTO equipments (title, description, more_info, img, sort_order) VALUES
('Mat Pilates', 'Temel pilates prensipleri ve nefes eşliğinde güçlenme.', 'Mat derslerinde core stabilizasyonu, mobilite ve nefes teknikleri üzerinde çalışılır.', 'assets/img/equip-mat.jpg', 1),
('Reformer', 'Dinamik yay sistemi ile kişiye özel direnç.', 'Reformer derslerinde kas kuvveti, esneklik ve postür dengesi hedeflenir.', 'assets/img/equip-reformer.jpg', 2),
('Tower', 'Dikey yay sistemi ile kontrollü direnç.', 'Tower ekipmanı, reformer ve cadillac prensiplerinin birleşimini sunar.', 'assets/img/equip-tower.jpg', 3),
('Cadillac', 'Çok yönlü egzersiz seçenekleri sunar.', 'Cadillac ile kuvvet, esneklik ve koordinasyon birlikte geliştirilir.', 'assets/img/equip-cadillac.jpg', 4),
('Chair', 'Denge ve güç odaklı kompakt ekipman.', 'Chair egzersizleri özellikle core ve alt beden stabilitesini artırır.', 'assets/img/equip-chair.jpg', 5),
('Barrel', 'Omurga mobilitesi ve esneklik için ideal.', 'Barrel dersleri postürü iyileştirir ve omurga esnekliğini destekler.', 'assets/img/equip-barrel.jpg', 6);

CREATE TABLE IF NOT EXISTS trainings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    year VARCHAR(40),
    description TEXT,
    img VARCHAR(255),
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

TRUNCATE TABLE trainings;

INSERT INTO trainings (title, year, description, img, sort_order) VALUES
('Balanced Body Mat Sertifikası', '2016', 'Kapsamlı mat pilates eğitimi ve anatomi modülleri.', NULL, 1),
('Balanced Body Reformer Sertifikası', '2017', 'Dinamik reformer ders planlaması ve progresyonlar.', NULL, 2),
('Hamilelik ve Doğum Sonrası Pilates Uzmanlığı', '2018', 'Prenatal ve postnatal süreçler için güvenli hareket programları.', NULL, 3);

CREATE TABLE IF NOT EXISTS plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(120) NOT NULL,
    price VARCHAR(120) NOT NULL,
    description TEXT,
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

TRUNCATE TABLE plans;

INSERT INTO plans (title, price, description, sort_order) VALUES
('Deneme Dersi', '₺750', 'Stüdyomuzu ve eğitmenimizi deneyimleyin.', 1),
('Aylık 4 Ders', '₺2.400', 'Haftada bir bireysel veya ikili seans.', 2),
('Aylık 8 Ders', '₺4.400', 'Haftada iki seans ile düzenli gelişim.', 3),
('Kişiye Özel Program', 'Teklif Üzerine', 'Rehabilitasyon ve özel hedefler için tasarlanır.', 4);

CREATE TABLE IF NOT EXISTS schedule_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    day_order TINYINT NOT NULL,
    start_time TIME NOT NULL,
    class_type VARCHAR(120) NOT NULL,
    level VARCHAR(120)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

TRUNCATE TABLE schedule_entries;

INSERT INTO schedule_entries (day_order, start_time, class_type, level) VALUES
(1, '09:00:00', 'Mat Grup', 'Tüm seviyeler'),
(3, '18:30:00', 'Reformer İkili', 'Orta seviye'),
(5, '11:00:00', 'Birebir Reformer', 'Kişiye özel');

CREATE TABLE IF NOT EXISTS faq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(255) NOT NULL,
    answer TEXT,
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

TRUNCATE TABLE faq;

INSERT INTO faq (question, answer, sort_order) VALUES
('İptal politikası nedir?', 'Seanslar 12 saat öncesine kadar ücretsiz iptal edilebilir.', 1),
('Derslere gelirken ne giymeliyim?', 'Rahat, esnek kıyafetler ve kaymaz çoraplar önerilir.', 2),
('Sağlık beyanı gerekli mi?', 'İlk dersinizde kısa bir sağlık formu doldurmanızı rica ederiz.', 3);

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(60),
    preference VARCHAR(120),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS footer_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(120) NOT NULL,
    url VARCHAR(255) NOT NULL,
    target VARCHAR(20) DEFAULT '_self',
    position INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

TRUNCATE TABLE footer_links;

INSERT INTO footer_links (label, url, target, position) VALUES
('Impressum', '#', '_self', 1),
('Datenschutzerklärung', '#', '_self', 2);

SET FOREIGN_KEY_CHECKS = 1;
