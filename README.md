# Kütüphane Yönetim Sistemi API

Bu döküman, Kütüphane Yönetim Sistemi API'sinin kullanımına dair bilgileri içerir. API, kitaplar, yazarlar ve kategorilerle ilgili işlemleri yönetmek için tasarlanmıştır. Tüm istekler için kimlik doğrulama gereklidir.

## Kurulum

### Gereksinimler

- PHP 8.0+
- MySQL/MariaDB
- Composer

### Adım 1: Projeyi İndirin

```bash
git clone https://github.com/burak1994/kutuphane-case.git
cd kutuphane-case
```

### Adım 2: Bağımlılıkları Yükleyin

```bash
composer install
```

### Adım 3: Ortam Değişkenlerini Ayarlayın

`.env` dosyasını oluşturun ve aşağıdaki gibi düzenleyin:

```env
DB_HOST=localhost
DB_NAME=kutuphane-case
DB_USER=root
DB_PASS=
DB_CHARSET=utf8mb4

# API settings
APP_ENV=development # or production
APP_SECRET=opikapbupikapsupikap
APP_ADMIN=burak
APP_PASS=r3Re341!
```

### Adım 4: Veritabanını Oluşturun

- MySQL/MariaDB'de `kutuphane-case` adında bir veritabanı oluşturun.
- `kutuphane-case-sample-database.sql` dosyasını içe aktarın:

```bash
mysql -u root -p kutuphane-case < kutuphane-case-sample-database.sql
```

## 📁 Proje Yapısı

```plaintext
kutuphane-case/
├── controllers/          # API Controller'ları
├── models/               # Veritabanı modelleri
├── helpers/              # Yardımcı sınıflar (Logger, Database vs.)
├── storage/              # Log dosyaları
├── tests/                # PHPUnit testleri (Helpers)
├── vendors/              # Composer dosyaları
├── config/               # Database bağlantı dosyaları
├── .env                  # Ortam değişkenleri
├── composer.json         # Composer konfigürasyonu
└── README.md             # Bu dosya
```

## API Kullanımı

### Base URL

```
http://localhost/kutuphane-case/api
```

### Kimlik Doğrulama

Eğer `.env` dosyasındaki `APP_ENV` değeri `'production'` ise, kimlik doğrulaması için aşağıdaki adımları izleyin:

#### Giriş Yapma (Login)

- **Method:** POST
- **URL:** `/api/login`
- **Gövde:**
  ```json
  {
    "username": "burak",
    "password": "r3Re341!"
  }
  ```
  `username` ve `password` `.env` dosyasından değiştirilebilir.
- **CURL Örneği:**
  ```bash
  curl -X POST http://localhost/kutuphane-case/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"burak","password":"r3Re341!"}'
  ```
- **Yanıt:**
  ```json
  {
    "success": true,
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjoxLCJ1c2VybmFtZSI6ImJ1cmFrIiwiaWF0IjoxNzUwNTE0MzIzLCJleHAiOjE3NTA1MTc5MjN9.TYLMMbyw7awJ-Z8m1s8ccxSwLKHJqtO-EExYs_-yh98"
  }
  ```

#### Sonraki İstekler

Dönen `token` değerini, sonraki isteklerde `Authorization` başlığında `Bearer` olarak ekleyin:

```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjoxLCJ1c2VybmFtZSI6ImJ1cmFrIiwiaWF0IjoxNzUwNTE0MzIzLCJleHAiOjE3NTA1MTc5MjN9.TYLMMbyw7awJ-Z8m1s8ccxSwLKHJqtO-EExYs_-yh98
```

## API Endpoint'leri

### Books Endpoint'leri

#### 1. Tüm Kitapları Listele

- **Method:** GET
- **URL:** `/api/books`
- **Açıklama:** Tüm kitapları sayfalama ile listeler.
- **Parametreler:**
  - `page` (isteğe bağlı): Sayfa numarası (varsayılan: 1)
  - `per_page` (isteğe bağlı): Sayfa başına öğe sayısı (varsayılan: 10)
- **CURL Örneği:**
  ```bash
  curl -X GET http://localhost/kutuphane-case/api/books \
  -H "Authorization: Bearer <token>"
  ```
- **Yanıt:**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 3,
        "title": "Memleketimden İnsan Manzaraları",
        "isbn": "9789750803772",
        "author_id": 1,
        "category_id": 2,
        "publication_year": "1957",
        "page_count": 258,
        "is_available": 1,
        "created_at": "2025-06-19 11:41:34"
      }
    ],
    "message": "book fetched successfully",
    "pagination": {
      "current_page": 1,
      "total_pages": 5,
      "per_page": 10,
      "total_items": 50
    }
  }
  ```

#### 2. Belirli Bir Kitabı Getir

- **Method:** GET
- **URL:** `/api/books/{id}`
- **Açıklama:** ID'ye göre belirli bir kitabın detaylarını getirir.
- **CURL Örneği:**
  ```bash
  curl -X GET http://localhost/kutuphane-case/api/books/3 \
  -H "Authorization: Bearer <token>"
  ```
- **Yanıt:**
  ```json
  {
    "success": true,
    "data": {
      "id": 3,
      "title": "Memleketimden İnsan Manzaraları",
      "isbn": "9789750803772",
      "author_id": 1,
      "category_id": 2,
      "publication_year": "1957",
      "page_count": 258,
      "is_available": 1,
      "created_at": "2025-06-19 11:41:34"
    },
    "message": "book fetched successfully"
  }
  ```

#### 3. Yeni Kitap Ekle

- **Method:** POST
- **URL:** `/api/books`
- **Açıklama:** Yeni bir kitap ekler.
- **Gövde:**
  ```json
  {
    "title": "Yeni Kitap",
    "isbn": "9781234567890",
    "author_id": 3,
    "category_id": 3,
    "publication_year": 2022,
    "page_count": 200
  }
  ```
- **Validasyon Kuralları:**
  - `isbn`: 13 haneli geçerli bir ISBN formatı olmalı.
  - `title`, `author_id`, `category_id`: Boş olamaz.
- **CURL Örneği:**
  ```bash
  curl -X POST http://localhost/kutuphane-case/api/books \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"title":"Yeni Kitap","isbn":"9781234567890","author_id":3,"category_id":3,"publication_year":2022,"page_count":200}'
  ```
- **Yanıt:**
  ```json
  {
    "success": true,
    "message": "book added"
  }
  ```

#### 4. Kitap Güncelle

- **Method:** PUT
- **URL:** `/api/books/{id}`
- **Açıklama:** Belirli bir kitabı günceller.
- **Gövde:**
  ```json
  {
    "title": "Güncellenmiş Kitap",
    "isbn": "9781234567890",
    "author_id": 3,
    "category_id": 3,
    "publication_year": 2022,
    "page_count": 200
  }
  ```
- **Validasyon Kuralları:**
  - `isbn`: 13 haneli geçerli bir ISBN formatı olmalı.
  - `title`, `author_id`, `category_id`: Boş olamaz.
- **CURL Örneği:**
  ```bash
  curl -X PUT http://localhost/kutuphane-case/api/books/1 \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"title":"Güncellenmiş Kitap","isbn":"9781234567890","author_id":1,"category_id":1,"publication_year":2022,"page_count":200}'
  ```
- **Yanıt:**
  ```json
  {
    "success": true,
    "message": "Book Updated"
  }
  ```

#### 5. Kitap Sil

- **Method:** DELETE
- **URL:** `/api/books/{id}`
- **Açıklama:** Belirli bir kitabı siler.
- **CURL Örneği:**
  ```bash
  curl -X DELETE http://localhost/kutuphane-case/api/books/1 \
  -H "Authorization: Bearer <token>"
  ```
- **Yanıt:**
  ```json
  {
    "success": true,
    "message": "book deleted"
  }
  ```

#### 6. Kitap Ara

- **Method:** GET
- **URL:** `/api/books/search?q={query}`
- **Açıklama:** Kitapları başlık veya ISBN'e göre arar.
- **Parametreler:**
  - `q`: Arama terimi (başlık veya ISBN)
- **CURL Örneği:**
  ```bash
  curl -X GET http://localhost/kutuphane-case/api/books/search?q=9781234567890 \
  -H "Authorization: Bearer <token>"
  ```
- **Yanıt:**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 66,
        "title": "Yeni Kitap",
        "isbn": "9781234567890",
        "author_id": 3,
        "category_id": 3,
        "publication_year": "2022",
        "page_count": 200,
        "is_available": 1,
        "created_at": "2025-06-22 12:11:25"
      }
    ],
    "message": "success",
    "pagination": {
      "current_page": 1,
      "total_pages": 1,
      "per_page": 10,
      "total_items": 1
    }
  }
  ```

### Authors Endpoint'leri

#### 1. Tüm Yazarları Listele

- **Method:** GET
- **URL:** `/api/authors`
- **Açıklama:** Tüm yazarları listeler.
- **CURL Örneği:**
  ```bash
  curl -X GET http://localhost/kutuphane-case/api/authors \
  -H "Authorization: Bearer <token>"
  ```
- **Yanıt:**
  ```json
  {
      "success": true,
      "data": [
          {
            "id": 1,
            "name": "Nazım Hikmet",
            "email": "nhikmet@dogankitap.com",
            "created_at": "2025-06-19 11:16:42"
          },
          ...
      ],
       "message": "success",
    "pagination": {
        "current_page": 1,
        "total_pages": 1,
        "per_page": 10,
        "total_items": 4
    }
  }
  ```

#### 2. Yeni Yazar Ekle

- **Method:** POST
- **URL:** `/api/authors`
- **Açıklama:** Yeni bir yazar ekler.
- **Gövde:**
  ```json
  {
    "name": "Yeni Yazar",
    "email": "yeni.yazar@example.com"
  }
  ```
- **Validasyon Kuralları:**
  - `email`: Geçerli bir e-posta formatı olmalı.
  - `name`: Boş olamaz.
- **CURL Örneği:**
  ```bash
  curl -X POST http://localhost/kutuphane-case/api/authors \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"name":"Yeni Yazar","email":"yeni.yazar@example.com"}'
  ```
- **Yanıt:**
  ```json
  {
    "success": true,
    "message": "Author Added"
  }
  ```

#### 3. Yazarın Kitaplarını Getir

- **Method:** GET
- **URL:** `/api/authors/{id}/books`
- **Açıklama:** Belirli bir yazarın kitaplarını listeler.
- **CURL Örneği:**
  ```bash
  curl -X GET http://localhost/kutuphane-case/api/authors/4/books \
  -H "Authorization: Bearer <token>"
  ```
- **Yanıt:**
  ```json
  {
      "success": true,
      "data": [
          {
            "id": 7,
            "title": "Değirmen",
            "isbn": "978-975-08-0660-3",
            "author_id": 4,
            "category_id": 4,
            "publication_year": "1947",
            "page_count": 240,
            "is_available": 1,
            "created_at": "2025-06-19 11:41:34"
        },
        {
            "id": 8,
            "title": "Yeni Dünya",
            "isbn": "978-975-363-0661-1",
            "author_id": 4,
            "category_id": 4,
            "publication_year": "1953",
            "page_count": 106,
            "is_available": 1,
            "created_at": "2025-06-19 11:41:34"
        }
          ...
      ],
       "message": "success",
       "pagination": {
        "current_page": 1,
        "total_pages": 1,
        "per_page": 10,
        "total_items": 2
    }
  }
  ```

### Categories Endpoint'leri

#### 1. Tüm Kategorileri Listele

- **Method:** GET
- **URL:** `/api/categories`
- **Açıklama:** Tüm kategorileri listeler.
- **CURL Örneği:**
  ```bash
  curl -X GET http://localhost/kutuphane-case/api/categories \
  -H "Authorization: Bearer <token>"
  ```
- **Yanıt:**
  ```json
  {
      "success": true,
      "data": [
         {
            "id": 4,
            "name": "Hikaye",
            "description": "Bu kategoride yazarlara ait hikayeleri görebilirsiniz",
            "created_at": "2025-06-19 11:30:59"
        }
          ...
      ],
      "message": "success",
    "pagination": {
        "current_page": 1,
        "total_pages": 1,
        "per_page": 10,
        "total_items": 3
    }
  }
  ```

#### 2. Yeni Kategori Ekle

- **Method:** POST
- **URL:** `/api/categories`
- **Açıklama:** Yeni bir kategori ekler.
- **Gövde:**
  ```json
  {
    "name": "Yeni Kategori",
    "description": "Kategori Açıklaması"
  }
  ```
- **Validasyon Kuralları:**
  - `name`: Boş olamaz.
  - `email`: Doğru formatta olmalı.
- **CURL Örneği:**
  ```bash
  curl -X POST http://localhost/kutuphane-case/api/categories \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"name":"Yeni Kategori"}'
  ```
- **Yanıt:**
  ```json
  {
    "success": true,
    "message": "Category Added"

  }
  ```

## Özel Gereksinimler

### Validasyon

- **ISBN Formatı:** Kitap eklerken veya güncellerken ISBN, 13 haneli geçerli bir formatta olmalıdır (ör. `9781234567890`).
- **E-posta Formatı:** Yazar eklerken e-posta adresi geçerli bir formatta olmalıdır (ör. `yazar@example.com`).
- **Boş Alanlar:** Tüm zorunlu alanlar (`title`, `isbn`, `author_id`, `category_id`, `name`, `email`) boş olamaz.

## Genel Yanıt Formatı

Tüm başarılı istekler aşağıdaki formatta yanıt döner:

```json
{
    "success": true,
    "data": [...],
    "message": "success",
    "pagination": {
        "current_page": 1,
        "total_pages": 5,
        "per_page": 10,
        "total_items": 50
    }
}
```

Hata durumunda:

```json
{
  "success": false,
  "errors": "error detail",
}
```

## Loglama

Proje, hataları ve bilgi mesajlarını izlemek için bir loglama mekanizması kullanır. Loglama işlemleri `helpers/LoggerHelpers.php` sınıfı tarafından yönetilir.

### Loglama Mekanizması

Log Dosyaları: Loglar, `storage/` dizininde saklanır ve üç tür log dosyası oluşturulur:

- \*\* `info.log`: Bilgi mesajları (örneğin, başarılı işlemler).

- \*\* `warning.log`: Uyarı mesajları (örneğin, beklenmeyen ancak kritik olmayan durumlar).

- \*\* `error.log`: Hata mesajları (örneğin, JWT doğrulama hataları veya veritabanı hataları).

Log Formatı: Her log kaydı, `[YYYY-MM-DD HH:MM:SS]` mesaj formatında yazılır. Örneğin:

```bash
[2025-06-22 02:07:45] validateToken@JwtHelpers Token expired: Token is expired
```

Kullanım:

- \*\* Bilgi Logu: `LoggerHelpers::info('Mesaj');`

Örnek: Başarılı bir kullanıcı girişi loglanabilir.

- \*\* Uyarı Logu: `LoggerHelpers::warning('Mesaj');`

Örnek: Geçersiz bir giriş denemesi.

- \*\* Hata Logu: `LoggerHelpers::error('Mesaj');`

Örnek: JWT token doğrulama hatası (JwtHelpers::validateToken tarafından kullanılır).

Dosya Yeri: Log dosyaları otomatik olarak `storage/` dizininde oluşturulur.

## Testler

PHPUnit testlerini çalıştırmak için:

```bash
vendor/bin/phpunit tests
```

## Gelecek Planlar ve İyileştirmeler

Projenin daha güvenli, ölçeklenebilir ve güvenilir olması için aşağıdaki özelliklerin eklenmesi planlanmaktadır:

1. Rate Limiting

Amaç: API'nin kötüye kullanımını önlemek ve performansı korumak için kullanıcı veya IP bazlı istek sınırlama uygulanması.
Planlanan Özellikler:
Varsayılan sınır: Saatte 100 istek (kullanıcı veya IP başına).
.env dosyasında yapılandırılabilir sınırlar (RATE_LIMIT_MAX ve RATE_LIMIT_PERIOD).

Hata Yanıtı: Sınır aşılırsa, 429 Too Many Requests yanıtı dönecek:{
"success": false,
"message": "Rate limit aşıldı. Lütfen bir süre sonra tekrar deneyin."
}

2. Daha Geniş Test Kapsamı

Amaç: API'nin güvenilirliğini artırmak için daha kapsamlı testler yazılması.
Planlanan Özellikler:
Tüm endpoint'ler için birim (unit) ve entegrasyon testleri eklenmesi.

Hedef: Şu anki testler yalnızca yardımcı sınıfları (helpers) kapsıyor. Tüm controller ve model katmanlarını kapsayan testler yazılacak.
