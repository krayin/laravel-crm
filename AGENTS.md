# AGENTS.md

> **Tujuan dokumen**: Panduan operasional untuk _coding agent_ yang mengerjakan Tugas Akhir: **Rancang Bangun Sistem Informasi Analytical CRM untuk Pemesanan Custom Engineering (PT Famindo Teknik Karya Utama)** berbasis **Krayin (Laravel CRM)** dengan metode **Market Basket Analysis (Apriori)** — _upgrade‑safe_ tanpa memodifikasi core Krayin.

---

## 1) Gambaran Proyek
- **Goal utama**: menambah modul **Analytical CRM** pada Krayin untuk:
  - Mengambil data transaksi pemesanan custom engineering.
  - Menjalankan **Apriori** untuk menemukan _association rules_ (X ⇒ Y) beserta **support**, **confidence**, **lift**.
  - Menyajikan **UI admin** untuk analisis & ekspor.
  - Memberi **rekomendasi item** di alur Lead/Quote/Order (cross-sell/upsell).
- **Platform**: Windows 11, **Laragon** + PHPStorm (lokal). Docker tersedia sebagai alternatif.
- **Prinsip**: **Jangan ubah folder `vendor/…` (core Krayin)**. Seluruh kerja dilakukan via **package/module kustom** + event/listener/override view & config.

---

## 2) Stack & Versi yang Disarankan
- **Krayin**: `v2.1.5` (stabil).
- **PHP**: 8.1/8.2 (Laragon default 8.3 juga OK jika dependensi terpenuhi). Pastikan ekstensi aktif: `zip`, `intl`, `gd`, `fileinfo`, `exif`.
- **Composer**: ≥ 2.5 (Laragon Full sudah include; atau install global).
- **Node**: ≥ 16.16 LTS (untuk asset build jika diperlukan).
- **DB**: MySQL ≥ 8.0 / MariaDB ≥ 10.3.

---

## 3) Setup Lingkungan Lokal
### 3.1 Laragon
1. Pastikan **Composer** tersedia (Terminal Laragon → `composer -V`).
2. Aktifkan ekstensi (Laragon → PHP → Extensions): **zip**, **intl**, **gd**, **fileinfo**, **exif**.
3. Restart All.

### 3.2 Membuat Proyek (dua pola)
- **Pola A – Clone yang sudah ada**
  - Jalankan: `composer install -o`
  - Jika pertama kali: `php artisan optimize:clear` dan `php artisan storage:link`
- **Pola B – Buat proyek baru dari template**
  ```bash
  composer create-project krayin/laravel-crm krayin-app
  cd krayin-app
  php artisan krayin-crm:install
  ```

### 3.3 `.env` minimal (dev)
```env
APP_NAME="Krayin CRM"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000   # atau http://krayin.test jika pakai virtual host Laragon
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=en
APP_CURRENCY=IDR

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel-crm
DB_USERNAME=root
DB_PASSWORD=            # kosong default Laragon kecuali diubah

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Email dev (pilih salah satu)
MAIL_MAILER=log         # paling simpel; tidak kirim email sungguhan
# atau MailHog (kalau dijalankan)
# MAIL_MAILER=smtp
# MAIL_HOST=127.0.0.1
# MAIL_PORT=1025
# MAIL_USERNAME=null
# MAIL_PASSWORD=null
# MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=laravel@krayincrm.com
MAIL_FROM_NAME="${APP_NAME}"

FILESYSTEM_DISK=public
```
Lalu:
```bash
php artisan storage:link
php artisan optimize:clear
```

### 3.4 Menjalankan Aplikasi
```bash
php artisan serve   # buka http://localhost:8000
```

> **Catatan**: Jika memakai DB di Docker, sesuaikan `DB_PORT` (mis. 3307) dan kredensial.

---

## 4) Arsitektur _Upgrade‑Safe_
- **Jangan** modifikasi `vendor/` (core Krayin & dependensi).
- Tambahan fitur dibungkus di **package kustom**: `packages/Famindo/AnalyticalCRM`.
- Gunakan **event/listener**, **service container binding**, **override view** (publish bila perlu), **policy/ACL**.

### 4.1 Struktur Package Kustom
```
packages/
  Famindo/AnalyticalCRM/
    src/
      Providers/ServiceProvider.php
      Http/Controllers/
      Models/
      Services/
      Console/
    routes/admin.php
    database/migrations/
    Resources/views/
    composer.json (opsional, jika dijadikan package standalone)
```
- Daftarkan `ServiceProvider` di app (config/komposer autoload PSR‑4).
- Tambahkan **menu Admin**: “Analytics → Market Basket (Apriori)”.
- Tambahkan **permissions/ACL** untuk modul ini.

---

## 5) Desain Data “Custom Engineering Order”
**Tabel inti (baru)**:
- `engineering_orders` (id, customer_id, organization_id, order_date, status, notes, timestamps)
- `engineering_order_items` (id, order_id, product_id?, item_code?, qty, unit_price, timestamps)

> Catatan: jika alur order sudah ada di modul lain, buat **ETL** yang menormalkan data menjadi transaksi per order.

---

## 6) Pipeline Apriori
### 6.1 ETL → Transaksi
- Query `engineering_order_items` → kelompokkan per `order_id` → array transaksi `[[itemA,itemB], [itemC,itemD,…], …]`.
- Sediakan filter periode & segmen (customer/org/industri/nilai order).

### 6.2 Algoritma
- Tambah dependensi (di root proyek):
  ```bash
  composer require php-ai/php-ml
  ```
- Contoh pemakaian ringkas:
  ```php
  use Phpml\Association\Apriori;

  $assoc = new Apriori($minSupport, $minConfidence);
  $assoc->train($transactions, []);
  $rules = $assoc->getRules();
  ```

### 6.3 Persistensi & Metadata
- Simpan ke tabel `apriori_rules` dengan kolom: `lhs` (json), `rhs` (json), `support`, `confidence`, `lift`, `period_start`, `period_end`, `params_json`, `created_by`.
- (Opsional) simpan juga `apriori_transactions` untuk audit/debug.

### 6.4 Scheduler & CLI
- Console command: `analytics:apriori` (param: `--from`, `--to`, `--support`, `--confidence`, `--segment`?).
- Tambah jadwal di `app/Console/Kernel.php` (harian/mingguan).

### 6.5 UI Admin
- Halaman **parameter** (periode, min support/confidence, filter segmen).
- Tabel hasil (frequent itemsets & rules) + metrik **support/confidence/lift**.
- Aksi: **Simpan rekomendasi**, **Export CSV**.

### 6.6 Integrasi ke Alur CRM
- **Widget rekomendasi** di halaman Lead/Quote/Order: ketika item dipilih → tampilkan saran item tambahan dari rules (X⇒Y).
- Tombol “Tambahkan” untuk push item ke quote/order.
- Logging **acceptance rate** rekomendasi.

---

## 7) Alur Kerja (Roadmap Tugas)
1. **Fondasi**: project jalan, ekstensi PHP aktif, `.env` beres.
2. **Data**: rancang & migrasi `engineering_orders`/`items`; seed data uji.
3. **Package**: siapkan `Famindo/AnalyticalCRM` (provider, routes, menu, ACL).
4. **ETL & Apriori**: service ETL → training → simpan `apriori_rules`.
5. **CLI & Scheduler**: command + jadwal rutin.
6. **UI Admin**: parameter, tabel hasil, ekspor.
7. **Integrasi UI CRM**: widget rekomendasi di Lead/Quote/Order.
8. **Testing & Evaluasi**: unit test ETL, validasi metrik, uji performa dasar.
9. **Dokumentasi**: arsitektur, ERD, flow, eksperimen & hasil, batasan & saran.
10. **Packaging/Deploy** (opsional): env prod, backup, docker-compose.

---

## 8) Perintah & Cheat‑Sheet
```bash
# dependency & util
composer install -o
composer dump-autoload
php artisan optimize:clear
php artisan storage:link

# migrasi & seeding
php artisan migrate --seed

# membuat artefak
php artisan make:migration create_engineering_orders
php artisan make:model EngineeringOrder -m
php artisan make:controller Admin/AprioriController --invokable
php artisan make:command AnalyticsApriori

# server dev
php artisan serve

# analitik (contoh CLI)
php artisan analytics:apriori --from=2025-01-01 --to=2025-06-30 --support=0.05 --confidence=0.6
```

---

## 9) Standar Kode & Guardrails
- **Jangan** modif `vendor/`.
- Business logic di `Services/` (mudah di‑unit test), controller tipis.
- Validasi parameter (periode, support/confidence), _empty dataset_ harus ditangani elegan.
- Pastikan query ETL **efisien** (indexing pada `order_id`, `product_id`, `order_date`).
- Data sensitif: hindari log isi _credentials_; gunakan `.env` & config.

### 9.1 Seeding Entitas Core Krayin (EAV + LogsActivity)
- Banyak entitas core (persons, organizations, warehouses, products, dst.) memakai EAV (`attributes`/`attribute_values`) dan trait `LogsActivity` untuk timeline.
- Agar data hasil seeding muncul dengan benar di UI (form/lookup) dan menulis timeline “Created/Updated”, ikuti pola dua langkah berikut:
  1) Simpan kolom inti via Eloquent Model entitas terkait (contoh: `Webkul\Warehouse\Models\Warehouse::create([...])` atau update lalu `save()`). Ini akan memicu event `created/updated` dari `LogsActivity` sehingga timeline mencatat “Created/Updated”.
  2) Simpan nilai EAV via `Webkul\Attribute\Repositories\AttributeValueRepository->save([...])` dengan payload minimal:
     - `entity_type` = kode entitas (mis. `persons`, `organizations`, `warehouses`).
     - `entity_id` = ID record yang baru dibuat/diperbarui.
     - Pasang nilai atribut yang dipakai UI (mis. persons: `name`, `emails`, `contact_numbers`, `job_title`, `user_id`, `organization_id`; organizations: `name`, `address`, `user_id`; warehouses: `name`, `description`, `contact_name`, `contact_emails`, `contact_numbers`, `contact_address`).
- Hindari `DB::table(...)->insert` langsung untuk entitas EAV, karena UI membaca dari `attribute_values` dan timeline tidak akan menulis “Created”.
- Jika memilih lewat Repository core (mis. `WarehouseRepository->create($data)`), jangan sisipkan `entity_type` ke payload model (akan error kolom tidak ada). Simpan EAV terpisah memakai `AttributeValueRepository->save()` seperti di langkah (2).
- Persons/Organizations: set juga `user_id` (owner) pada record inti dan EAV agar lookup/ACL bekerja mulus di UI.

### 9.2 Catatan Khusus (Short‑Term Project)
- Untuk tugas ini bersifat jangka pendek/eksperimental dan tidak ditujukan ke produksi, maka diperbolehkan melakukan perubahan pada kode di `packages/Webkul/**` bila diperlukan untuk mempercepat implementasi/bugfix.
- Tetap dilarang mengubah kode di `vendor/**` karena:
  - File di `vendor/` dikelola Composer dan tidak masuk kendali versi kita (rawan ter‑overwrite),
  - Tidak aman untuk upgrade/deploy.
- Usahakan perubahan di `packages/Webkul/**` tetap minimal, terdokumentasi, dan mudah di‑rollback. Jika nantinya dibutuhkan upgrade‑safe, pindahkan ke package kustom (override routes/controller/datagrid/binding) sesuai pola di dokumen ini.

---

## 10) Testing & Evaluasi
- **Unit test**: ETL builder (jumlah transaksi, konsistensi item), perhitungan metrik.
- **Sanity check**: sample rules ditinjau domain expert.
- **Metrik**: distribusi support/confidence/lift; di integrasi UI ukur **acceptance rate** rekomendasi.

---

## 11) Deliverables
- Kode modul `packages/Famindo/AnalyticalCRM` + migration & seeder.
- Console command & scheduler aktif.
- UI Admin Analytics + ekspor CSV.
- Widget rekomendasi di Lead/Quote/Order.
- Dokumentasi: ERD, arsitektur, flow ETL/Apriori, panduan instal, hasil uji & analisis.

---

## 12) Known Issues & Tips
- Jika Composer mengeluh `ext-zip` → aktifkan `zip` di Laragon (PHP → Extensions). Terminal baru setelah perubahan.
- `.env` untuk email di dev: gunakan `MAIL_MAILER=log` atau MailHog; jangan pakai host `mailhog` kecuali via docker-compose.
- App URL harus sesuai cara run (artisan serve vs virtual host Laragon).

---

## 13) Kontak & Eskalasi
- **Teknis**: masalah dependency/ekstensi PHP/Composer.
- **Data**: kebutuhan kolom tambahan pada `engineering_orders/items`.
- **UX**: kebutuhan tampilan & metrik di UI admin.

> Selesai. Ikuti urutan di §7 sebagai _sprint plan_. Jika ada perubahan requirement, update dokumen ini terlebih dahulu sebelum implementasi.
