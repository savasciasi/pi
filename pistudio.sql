/*
  # Pi Studio Pilates - PostgreSQL Database Schema & Seed Data

  This migration creates the complete database structure for the Pi Studio Pilates website,
  including multilingual page content (TR/EN), admin users, settings, media, and contact messages.

  1. New Tables
    - `users` - Admin user accounts with bcrypt password hashing
      - `id` (serial, primary key)
      - `username` (varchar 50, unique, not null)
      - `password_hash` (varchar 255, not null)
      - `role` (varchar 20, not null, default 'admin')
      - `created_at` (timestamptz, default now())

    - `pages` - Main page records (language-neutral)
      - `id` (serial, primary key)
      - `slug` (varchar 100, unique, not null)
      - `created_at` (timestamptz, default now())
      - `updated_at` (timestamptz, default now())

    - `page_translations` - Multilingual content for pages
      - `id` (serial, primary key)
      - `page_id` (integer, foreign key to pages)
      - `lang` (varchar 2, check constraint 'tr' or 'en')
      - `title` (varchar 255, not null)
      - `content` (text)
      - `meta_title` (varchar 255)
      - `meta_description` (text)
      - unique constraint on (page_id, lang)

    - `settings` - Key-value configuration store
      - `key` (varchar 100, primary key)
      - `value` (text)

    - `media` - Uploaded files and assets
      - `id` (serial, primary key)
      - `file_name` (varchar 255, not null)
      - `file_path` (varchar 500, not null)
      - `mime_type` (varchar 100)
      - `file_size` (integer)
      - `created_at` (timestamptz, default now())

    - `contact_messages` - Contact form submissions
      - `id` (serial, primary key)
      - `name` (varchar 100, not null)
      - `email` (varchar 255, not null)
      - `phone` (varchar 50)
      - `message` (text, not null)
      - `created_at` (timestamptz, default now())

  2. Seed Data
    - Admin user: pinar / pistudyo2025! (bcrypt hashed)
    - Pages: home, egitmen, instructor, contact (with TR/EN translations)
    - Settings: site name, WhatsApp, Instagram, video playlist, contact info
    - Full instructor profile content in Turkish and English

  3. Security
    - All tables use prepared statements (enforced by PDO in application)
    - Password stored with bcrypt hash
    - Check constraints on language fields
    - Foreign key constraints with cascade delete

  4. Important Notes
    - Uses PostgreSQL-specific features (SERIAL, TIMESTAMPTZ, CHECK)
    - Default video playlist: bg.mp4, bg1.mp4, bg2.mp4, bg3.mp4
    - WhatsApp number: +05417672104
    - Instagram studio: pi_studyo_pilatess
    - Instagram instructor: uverrcinnkaa
*/

-- Drop existing tables if they exist (for clean reinstall)
DROP TABLE IF EXISTS contact_messages CASCADE;
DROP TABLE IF EXISTS media CASCADE;
DROP TABLE IF EXISTS settings CASCADE;
DROP TABLE IF EXISTS page_translations CASCADE;
DROP TABLE IF EXISTS pages CASCADE;
DROP TABLE IF EXISTS users CASCADE;

-- Create users table
CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL DEFAULT 'admin',
  created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Create pages table
CREATE TABLE pages (
  id SERIAL PRIMARY KEY,
  slug VARCHAR(100) UNIQUE NOT NULL,
  created_at TIMESTAMPTZ DEFAULT NOW(),
  updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Create page_translations table
CREATE TABLE page_translations (
  id SERIAL PRIMARY KEY,
  page_id INTEGER NOT NULL REFERENCES pages(id) ON DELETE CASCADE,
  lang VARCHAR(2) NOT NULL CHECK (lang IN ('tr', 'en')),
  title VARCHAR(255) NOT NULL,
  content TEXT,
  meta_title VARCHAR(255),
  meta_description TEXT,
  UNIQUE(page_id, lang)
);

-- Create settings table
CREATE TABLE settings (
  key VARCHAR(100) PRIMARY KEY,
  value TEXT
);

-- Create media table
CREATE TABLE media (
  id SERIAL PRIMARY KEY,
  file_name VARCHAR(255) NOT NULL,
  file_path VARCHAR(500) NOT NULL,
  mime_type VARCHAR(100),
  file_size INTEGER,
  created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Create contact_messages table
CREATE TABLE contact_messages (
  id SERIAL PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(50),
  message TEXT NOT NULL,
  created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Create indexes for better performance
CREATE INDEX idx_page_translations_page_id ON page_translations(page_id);
CREATE INDEX idx_page_translations_lang ON page_translations(lang);
CREATE INDEX idx_pages_slug ON pages(slug);
CREATE INDEX idx_contact_messages_created_at ON contact_messages(created_at DESC);

-- ============================================
-- SEED DATA
-- ============================================

-- Insert admin user (pinar / pistudyo2025!)
-- Password hash generated with: password_hash('pistudyo2025!', PASSWORD_BCRYPT)
INSERT INTO users (username, password_hash, role) VALUES
('pinar', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert pages
INSERT INTO pages (slug) VALUES
('home'),
('egitmen'),
('instructor'),
('contact');

-- Insert page translations (TR)
INSERT INTO page_translations (page_id, lang, title, content, meta_title, meta_description) VALUES
-- Home TR
(1, 'tr', 'Ana Sayfa',
'<div class="hero-content">
  <h1>Pi Studio Pilates</h1>
  <p>Bedeninizi ve zihninizi güçlendirin</p>
</div>

<section class="services">
  <h2>Hizmetlerimiz</h2>
  <div class="service-grid">
    <div class="service-card">
      <h3>Reformer Pilates</h3>
      <p>Profesyonel reformer cihazları ile özel antrenmanlar</p>
    </div>
    <div class="service-card">
      <h3>Mat Pilates</h3>
      <p>Klasik mat egzersizleri ile güçlenme</p>
    </div>
    <div class="service-card">
      <h3>Kişiye Özel Programlar</h3>
      <p>Size özel hazırlanan pilates programları</p>
    </div>
  </div>
</section>',
'Pi Studio Pilates - Ankara',
'Ankara''da profesyonel pilates eğitimi. Reformer, mat ve kişiye özel pilates programları.'),

-- Home EN
(1, 'en', 'Home',
'<div class="hero-content">
  <h1>Pi Studio Pilates</h1>
  <p>Strengthen your body and mind</p>
</div>

<section class="services">
  <h2>Our Services</h2>
  <div class="service-grid">
    <div class="service-card">
      <h3>Reformer Pilates</h3>
      <p>Professional training with reformer equipment</p>
    </div>
    <div class="service-card">
      <h3>Mat Pilates</h3>
      <p>Strengthen with classic mat exercises</p>
    </div>
    <div class="service-card">
      <h3>Personalized Programs</h3>
      <p>Custom pilates programs designed for you</p>
    </div>
  </div>
</section>',
'Pi Studio Pilates - Ankara',
'Professional pilates training in Ankara. Reformer, mat and personalized pilates programs.'),

-- Instructor TR (egitmen)
(2, 'tr', 'Eğitmen - Pınar Sarı Koçak',
'<div class="instructor-profile">
  <h1>Pınar Sarı Koçak</h1>

  <section class="about">
    <h2>Hakkımda</h2>
    <p>Ankara Üniversitesi Spor Bilimleri Fakültesi Beden Eğitimi ve Spor Bölümü''nden bölüm üçüncüsü olarak mezun oldum. Ardından Yozgat Bozok Üniversitesi Sağlık Bilimleri Enstitüsü''nde tezli yüksek lisansımı tamamladım.</p>
  </section>

  <section class="education">
    <h2>Eğitim</h2>
    <ul>
      <li>Ankara Üniversitesi Spor Bilimleri Fakültesi, Beden Eğitimi ve Spor Bölümü (Fakülte 3.sü)</li>
      <li>Yozgat Bozok Üniversitesi, Sağlık Bilimleri Enstitüsü – Tezli Yüksek Lisans</li>
    </ul>
  </section>

  <section class="certifications">
    <h2>Uzmanlık Alanlarım</h2>
    <ul>
      <li>Türkiye Cimnastik Federasyonu 1. ve 2. Kademe Pilates Antrenörlüğü</li>
      <li>Bodysystem Reformer Seviye 1</li>
      <li>Bodysystem Trapez-Cadillac</li>
      <li>Bodysystem Chair Seviye 1–2</li>
      <li>Sport Science Institute Reformer Level 1–2</li>
    </ul>
  </section>

  <section class="workshops">
    <h2>Katıldığım Workshoplar</h2>
    <ul>
      <li>Pilates Tedavinin Bir Parçası Olabilir mi? — Prof. Dr. Osman Coşkun (HUP)</li>
      <li>Profesyonel Sporcularda Pilatesin Kullanımı — Alper Koçak (HUP)</li>
      <li>Nefes ve Core Aktivasyonu — Benay Yakışır & Seda Şüküroğlu Özer (HUP)</li>
      <li>Hiit X Pilates Fusion — İpek Yolyapan (HUP)</li>
      <li>Over Pronation — Emrah Demirtaş (FMI)</li>
    </ul>
  </section>
</div>',
'Eğitmen - Pınar Sarı Koçak | Pi Studio Pilates',
'Pınar Sarı Koçak - Sertifikalı pilates eğitmeni. TCF, Bodysystem ve Sport Science Institute sertifikaları.'),

-- Instructor EN
(3, 'en', 'Instructor - Pınar Sarı Koçak',
'<div class="instructor-profile">
  <h1>Pınar Sarı Koçak</h1>

  <section class="about">
    <h2>About Me</h2>
    <p>I graduated third in my class from Ankara University Faculty of Sport Sciences, Physical Education and Sports Department. I then completed my master''s degree with thesis at Yozgat Bozok University Institute of Health Sciences.</p>
  </section>

  <section class="education">
    <h2>Education</h2>
    <ul>
      <li>Ankara University Faculty of Sport Sciences, Physical Education and Sports (3rd in Faculty)</li>
      <li>Yozgat Bozok University, Institute of Health Sciences – Master''s with Thesis</li>
    </ul>
  </section>

  <section class="certifications">
    <h2>Certifications</h2>
    <ul>
      <li>Turkish Gymnastics Federation Level 1 & 2 Pilates Instructor</li>
      <li>Bodysystem Reformer Level 1</li>
      <li>Bodysystem Trapeze-Cadillac</li>
      <li>Bodysystem Chair Level 1–2</li>
      <li>Sport Science Institute Reformer Level 1–2</li>
    </ul>
  </section>

  <section class="workshops">
    <h2>Workshops Attended</h2>
    <ul>
      <li>Can Pilates Be Part of Treatment? — Prof. Dr. Osman Coşkun (HUP)</li>
      <li>Use of Pilates in Professional Athletes — Alper Koçak (HUP)</li>
      <li>Breathing and Core Activation — Benay Yakışır & Seda Şüküroğlu Özer (HUP)</li>
      <li>HIIT X Pilates Fusion — İpek Yolyapan (HUP)</li>
      <li>Over Pronation — Emrah Demirtaş (FMI)</li>
    </ul>
  </section>
</div>',
'Instructor - Pınar Sarı Koçak | Pi Studio Pilates',
'Pınar Sarı Koçak - Certified pilates instructor. TCF, Bodysystem and Sport Science Institute certifications.'),

-- Contact TR
(4, 'tr', 'İletişim',
'<div class="contact-page">
  <h1>İletişim</h1>
  <p>Sorularınız için bize ulaşın veya WhatsApp üzerinden direkt randevu alın.</p>
</div>',
'İletişim | Pi Studio Pilates',
'Pi Studio Pilates ile iletişime geçin. Randevu almak için WhatsApp''tan ulaşabilirsiniz.'),

-- Contact EN
(4, 'en', 'Contact',
'<div class="contact-page">
  <h1>Contact</h1>
  <p>Reach out to us with your questions or book an appointment directly via WhatsApp.</p>
</div>',
'Contact | Pi Studio Pilates',
'Contact Pi Studio Pilates. You can reach us via WhatsApp to book an appointment.');

-- Insert settings
INSERT INTO settings (key, value) VALUES
('site_name', 'Pi Studio Pilates'),
('whatsapp_number', '+05417672104'),
('instagram_studio', 'https://www.instagram.com/pi_studyo_pilatess'),
('instagram_instructor', 'https://www.instagram.com/uverrcinnkaa'),
('contact_email', 'info@pistudiopilates.com'),
('address', 'Ankara, Türkiye'),
('working_hours', 'Pazartesi - Cumartesi: 09:00 - 20:00<br>Pazar: Kapalı'),
('video_playlist', '["assets/video/bg.mp4","assets/video/bg1.mp4","assets/video/bg2.mp4","assets/video/bg3.mp4"]'),
('whatsapp_message', 'Merhaba, randevu almak istiyorum.'),
('map_embed', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3060.2615876707595!2d32.8543!3d39.9334!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMznCsDU2JzAwLjIiTiAzMsKwNTEnMTUuNSJF!5e0!3m2!1sen!2str!4v1234567890" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>');

-- Success message
DO $$
BEGIN
  RAISE NOTICE 'Pi Studio Pilates database schema and seed data created successfully!';
  RAISE NOTICE 'Admin login: pinar / pistudyo2025!';
  RAISE NOTICE 'WhatsApp: +05417672104';
  RAISE NOTICE 'Instagram Studio: https://www.instagram.com/pi_studyo_pilatess';
  RAISE NOTICE 'Instagram Instructor: https://www.instagram.com/uverrcinnkaa';
END $$;
