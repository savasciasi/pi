CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (email, password) VALUES
('pinar@pistudiopilates.com', '$2y$12$FyNfSGYn9XVqC.IVVomureQ5UQorHB4y40LSLLv/V7bkmF1Sxh9ye');

CREATE TABLE hero (
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

INSERT INTO hero (title, subtitle, cta_primary_text, cta_secondary_text, cta_primary_link, cta_secondary_link, address, map_embed, background_media) VALUES
('Pi Studio Pilates', 'Vücudunu güçlendir, nefesinle ak.', 'Deneme Dersi Al', 'Ders Programı', '#contact', '#schedule', 'Bağdat Caddesi No:123, İstanbul', '', NULL);

CREATE TABLE about_pilates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO about_pilates (content) VALUES
('Pilates; nefes, kontrol ve core gücünü temel alan kapsamlı bir zihin-beden pratiğidir. Doğru nefes ve hizalanma ile vücudu güçlendirirken zihinsel farkındalığı artırır.');

CREATE TABLE history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO history (content) VALUES
('20. yüzyılın başında Joseph Pilates tarafından geliştirilen kontroloji, modern pilates pratiğinin temelini oluşturur. Pi Studio Pilates bu mirası günümüz bilimiyle buluşturur.');

CREATE TABLE instructor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    bio TEXT,
    highlights TEXT,
    photo VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO instructor (name, bio, highlights, photo) VALUES
('Pınar Sarı Koçak', 'Pınar Sarı Koçak, hareket anatomi bilgisi ve kişiye özel pilates yaklaşımıyla bedeninizi yeniden keşfetmenizi sağlar.', 'Mat Pilates\nReformer Pilates\nRehabilitasyon odaklı programlama\nPrenatal & Postnatal destek', NULL);

CREATE TABLE equipments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(120) NOT NULL,
    description TEXT,
    more_info TEXT,
    img VARCHAR(255),
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO equipments (title, description, more_info, img, sort_order) VALUES
('Mat Pilates', 'Temel pilates prensipleri ve nefes eşliğinde güçlenme.', 'Mat derslerinde core stabilizasyonu, mobilite ve nefes teknikleri üzerinde çalışılır.', NULL, 1),
('Reformer', 'Dinamik yay sistemi ile kişiye özel direnç.', 'Reformer derslerinde kas kuvveti, esneklik ve postür dengesi hedeflenir.', NULL, 2),
('Tower', 'Dikey yay sistemi ile kontrollü direnç.', 'Tower ekipmanı, reformer ve cadillac prensiplerinin birleşimini sunar.', NULL, 3);

CREATE TABLE trainings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    year VARCHAR(40),
    description TEXT,
    img VARCHAR(255),
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO trainings (title, year, description, img, sort_order) VALUES
('Balanced Body Mat Sertifikası', '2016', 'Kapsamlı mat pilates eğitimi ve anatomi modülleri.', NULL, 1),
('Balanced Body Reformer Sertifikası', '2017', 'Dinamik reformer ders planlaması ve progresyonlar.', NULL, 2);

CREATE TABLE plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(120) NOT NULL,
    price VARCHAR(120) NOT NULL,
    description TEXT,
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO plans (title, price, description, sort_order) VALUES
('Deneme Dersi', '₺750', 'Stüdyomuzu ve eğitmenimizi deneyimleyin.', 1),
('Aylık 4 Ders', '₺2.400', 'Haftada bir bireysel veya ikili seans.', 2),
('Aylık 8 Ders', '₺4.400', 'Haftada iki seans ile düzenli gelişim.', 3),
('Kişiye Özel Program', 'Teklif Üzerine', 'Rehabilitasyon ve özel hedefler için tasarlanır.', 4);

CREATE TABLE schedule_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    day_order TINYINT NOT NULL,
    start_time TIME NOT NULL,
    class_type VARCHAR(120) NOT NULL,
    level VARCHAR(120)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO schedule_entries (day_order, start_time, class_type, level) VALUES
(1, '09:00:00', 'Mat Grup', 'Tüm seviyeler'),
(3, '18:30:00', 'Reformer İkili', 'Orta seviye'),
(5, '11:00:00', 'Birebir Reformer', 'Kişiye özel');

CREATE TABLE faq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(255) NOT NULL,
    answer TEXT,
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO faq (question, answer, sort_order) VALUES
('İptal politikası nedir?', 'Seanslar 12 saat öncesine kadar ücretsiz iptal edilebilir.', 1),
('Derslere gelirken ne giymeliyim?', 'Rahat, esnek kıyafetler ve kaymaz çoraplar önerilir.', 2),
('Sağlık beyanı gerekli mi?', 'İlk dersinizde kısa bir sağlık formu doldurmanızı rica ederiz.', 3);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(60),
    preference VARCHAR(120),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE footer_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(120) NOT NULL,
    url VARCHAR(255) NOT NULL,
    target VARCHAR(20) DEFAULT '_self',
    position INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO footer_links (label, url, target, position) VALUES
('Impressum', '#', '_self', 1),
('Datenschutzerklärung', '#', '_self', 2);
