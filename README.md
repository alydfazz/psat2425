# Panduan Lengkap Deploy Aplikasi psat2425 di AWS EC2 + RDS (Private Access + HTTPS)

Aplikasi **psat2425** adalah sistem CRUD siswa berbasis PHP dan MySQL. Panduan ini menjelaskan langkah-langkah lengkap dan otomatis untuk:

- Deploy aplikasi ke EC2
- Koneksi ke RDS 
- Aktivasi HTTPS
- Penggunaan User Data
- Konfigurasi Security Group dan VPC

---

## 1. Membuat 2 Security Group

Masuk ke Consol Home > EC2 > Security Groups > Create security group

### A. Security Group: `SG-ServerWeb` (untuk EC2)

**Inbound Rules:**

| Type  | Port | Destination type           |
|-------|------|----------------------------|
| SSH   | 22   | Anywhere-IPV4 (0.0.0.0/0)  |
| HTTP  | 80   | Anywhere-IPV4 (0.0.0.0/0)  |
| HTTPS | 443  | Anywhere-IPV4 (0.0.0.0/0)  |

**Outbound Rules:**  
- Allow all (default)

---

### B. Security Group: `SG-ServerDB` (untuk RDS)

**Inbound Rules:**

| Type         | Port | Destination type           |
|--------------|------|----------------------------|
| MySQL/Aurora | 3306 | Anywhere-IPV4 (0.0.0.0/0)  |

**Outbound Rules:**  
- Allow all (default)

---

## 2. Membuat Database di Amazon RDS (Private Access)

1. Masuk ke AWS Console > Aurora and RDS > Databases > Create Database
2. Pilih Standar create, **MySQL**, free tier
3. Konfigurasi:
   - DB instance identifier: `alyards`
   - Master Username: `admin`
   - Credentials management: pilih `self managed`
   - Master password: `P4ssw0rd123`
   - Confirm master password: `P4ssw0rd123`
4. Connectivity:
   - Public access: **No**
   - VPC security group (firewall): pilih `Choose existing`
   - Existing VPC security groups: pilih `SG-ServerDB`
5. Klik "Create Database"
6. Setelah selesai, tunngu **endpoint RDS** muncul, contoh:  
   `alyards.c6ag34mtk0rj.us-east-1.rds.amazonaws.com`

---

## 3. Deploy Instance EC2 (Ubuntu 20.04) dengan User Data

### A. Launch EC2

1. Buka EC2 > Instances > Launch Instance
2. Name nya diisi bebas, misalnya  `AlyaServer`
2. Quick Start: **Ubuntu**
3. Instance type: `t2.nano`
4. Key pair: pilih `vockey`
5. Firewall (security groups) pilih `selecting existing security group`, lalu pilih `SG-ServerWeb`
6. Klik **Advanced Details**, masukkan **User Data** di bawah

---

### B. User Data Script (Otomatis Deploy + HTTPS)

```bash
#!/bin/bash
apt update -y
apt install -y apache2 php php-mysql libapache2-mod-php mysql-client openssl

rm -rf /var/www/html/{*,.*}
git clone https://github.com/alydfazz/psat2425.git /var/www/html
chmod -R 777 /var/www/html

echo DB_USER=admin > /var/www/html/.env
echo DB_PASS=P4ssw0rd123  >> /var/www/html/.env
echo DB_NAME=crudsiswa >> /var/www/html/.env
echo DB_HOST=rds11tjkt1.c6ag34mtk0rj.us-east-1.rds.amazonaws.com >> /var/www/html/.env

apt install openssl
a2enmod ssl
a2ensite default-ssl.conf
systemctl reload apache2

````

> Semua proses ini otomatis saat EC2 pertama kali diluncurkan. Tidak perlu SSH ke dalam server.

Untuk susunan file .env sebagai berikut:
```file
DB_USER=....  (isi dengan user RDS) > /var/www/html/.env
DB_PASS=....  (isi dengan password RDS) >> /var/www/html/.env
DB_NAME=....  (isi dengan nama database yang akan dibuat di RDS) >> /var/www/html/.env
DB_HOST=....  (isi dengan Endpoint RDS) >> /var/www/html/.env
````

## 4. Verifikasi Aplikasi

* Akses melalui:
  `https://[Public-IP-EC2]`
* Klik lanjut jika muncul peringatan HTTPS self-signed

---

## 5. Login Aplikasi

* Username: `admin`
* Password: `123`

---

## 6. Troubleshooting (Tanpa SSH)

| Masalah                | Solusi                                                     |
| ---------------------- | ---------------------------------------------------------- |
| Aplikasi tidak tampil  | Ulangi launch instance, pastikan script User Data benar    |
| Tidak bisa konek ke DB | Periksa pengaturan Security Group `SG-ServerDB` dan `.env` |
| HTTPS error            | Pastikan konfigurasi sudah benar (lihat script di atas)    |

---

## 7. Pengumpulan

1. Screenshot halaman **Data Siswa** (`dashboard.php`) setelah mengisi data
2. Sertakan:
   * Link GitHub
3. Upload ke Link Google Form yang sudah disediakan

---

## Selesai