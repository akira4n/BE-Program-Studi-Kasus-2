# **Event Management API**

Sebuah RESTful API untuk Event Management yang dibuat dengan Laravel, mencakup fitur user autentikasi, manajemen event, dan proses transaksi atau booking.

## 🔧 Requirements

-   PHP >= 8.2
-   MySQL
-   Composer
-   Laravel 12

## ⚙️ Instalasi

1. Clone repository

```bash
git clone https://github.com/akira4n/BE-Program-Studi-Kasus-2.git
cd BE-Program-Studi-Kasus-2
```

2. Install dependency

```bash
composer install
```

3. Buat dan konfigurasi .env

```bash
cp .env.example .env
```

4. Generate app key

```bash
php artisan key:generate
```

5. Run migration

```bash
php artisan migrate
```

6. Start server

```bash
php artisan serve
```

## 🔐 Authentication

Semua endpoint di bawah ini kecuali Register dan Login memerlukan header `Authorization: Bearer {your_token}` yang didapatkan setelah login.

## 🌐 Dokumentasi API

Semua endpoint kecuali **Register** dan **Login** memerlukan header:
`Authorization: Bearer {your_token}`

### Auth

| Method | Endpoint        | Role   | Deskripsi                             |
| :----- | :-------------- | :----- | :------------------------------------ |
| `POST` | `/api/register` | Public | Buat akun baru.                       |
| `POST` | `/api/login`    | Public | Login untuk mendapatkan Bearer Token. |

### Events (Manajemen Acara)

| Method   | Endpoint           | Role             | Deskripsi                               |
| :------- | :----------------- | :--------------- | :-------------------------------------- |
| `GET`    | `/api/events`      | All              | Menampilkan semua daftar event.         |
| `GET`    | `/api/events/{id}` | All              | Menampilkan detail spesifik satu event. |
| `POST`   | `/api/events`      | Admin, Organizer | Membuat event baru.                     |
| `PATCH`  | `/api/events/{id}` | Admin, Organizer | Update data event.                      |
| `DELETE` | `/api/events/{id}` | Admin, Organizer | Menghapus data event.                   |

### Transactions (Pemesanan Tiket)

| Method   | Endpoint                 | Role        | Deskripsi                                                |
| :------- | :----------------------- | :---------- | :------------------------------------------------------- |
| `GET`    | `/api/transactions`      | All         | Melihat riwayat transaksi (Filter otomatis sesuai role). |
| `GET`    | `/api/transactions/{id}` | All         | Melihat detail spesifik satu transaksi.                  |
| `POST`   | `/api/transactions`      | Admin, User | Membeli tiket event.                                     |
| `PATCH`  | `/api/transactions/{id}` | All         | Update status.                                           |
| `DELETE` | `/api/transactions/{id}` | Admin       | Menghapus transaksi.                                     |

### **Auth**

**1. Register**

```http
POST /api/register
```

**Request Body:**

```json
{
    "name": "Syawal",
    "email": "syawal@example.com",
    "password": "password123",
    "role": "admin" // "admin", "organizer", atau "user"
}
```

**Response (201):**

```json
{
    "message": "Akun berhasil dibuat",
    "data": {
        "name": "Syawal",
        "email": "syawal@example.com",
        "role": "admin",
        "updated_at": "2025-12-26T17:37:17.000000Z",
        "created_at": "2025-12-26T17:37:17.000000Z",
        "id": 1
    }
}
```

**2. Login**

```http
POST /api/login
```

**Request Body:**

```json
{
    "email": "syawal@example.com",
    "password": "password123"
}
```

**Response (200):**

```json
{
    "user": {
        "id": 1,
        "name": "Syawal",
        "email": "syawal@example.com",
        "email_verified_at": null,
        "role": "admin",
        "created_at": "2025-12-26T17:37:17.000000Z",
        "updated_at": "2025-12-26T17:37:17.000000Z"
    },
    "token": "your-token"
}
```

### **Events**

**1. Get All Events**

```http
GET /api/events
```

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "title": "Konser Musik",
            "capacity": 150,
            "stock": 150,
            "start_event": "2024-12-31 00:00:00",
            "price": "75000.00",
            "created_at": "2025-12-26T17:37:57.000000Z",
            "updated_at": "2025-12-26T17:37:57.000000Z"
        }
    ]
}
```

**2. Create Event**

```http
POST /api/events
```

**Request Body:**

```json
{
    "title": "Konser Musik",
    "capacity": 150,
    "start_event": "2024-12-31", //format: Y-m-d
    "price": 75000
}
```

**Response (201):**

```json
{
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "title": "Konser Musik",
            "capacity": 150,
            "stock": 150,
            "start_event": "2024-12-31 00:00:00",
            "price": "75000.00",
            "created_at": "2025-12-26T17:37:57.000000Z",
            "updated_at": "2025-12-26T17:37:57.000000Z"
        }
    ]
}
```

**3. Update Event**

```http
PATCH /api/events/{id}
```

**Request Body:**

```json
{
    "title": "Konser Musik Tahun Baru",
    "price": 100000,
    "start_event": "2025-12-31"
    //"capacity" : "",
}
```

**Response (200):**

```json
{
    "message": "Data event berhasil diperbarui",
    "data": {
        "id": 1,
        "user_id": 1,
        "title": "Konser Musik Tahun Baru",
        "capacity": 150,
        "stock": 150,
        "start_event": "2025-12-31",
        "price": 100000,
        "created_at": "2025-12-26T17:37:57.000000Z",
        "updated_at": "2025-12-26T18:15:02.000000Z"
    }
}
```

**4. Delete Event**

```http
DELETE /api/events/{id}
```

**Response (200):**

```json
{
    "message": "Data event berhasi dihapus"
}
```

### **Transactions**

**1. Get All Transactions**

```http
GET /api/transactions
```

**Response (200):**

```json
{
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "event_id": 1,
            "quantity": 3,
            "total_price": "300000.00",
            "status": "paid",
            "created_at": "2025-12-26T18:16:02.000000Z",
            "updated_at": "2025-12-26T18:16:02.000000Z",
            "user": {
                "id": 1,
                "name": "Syawal",
                "email": "syawal@example.com",
                "email_verified_at": null,
                "role": "admin",
                "created_at": "2025-12-26T17:37:17.000000Z",
                "updated_at": "2025-12-26T17:37:17.000000Z"
            },
            "event": {
                "id": 1,
                "user_id": 1,
                "title": "Konser Musik Tahun Baru",
                "capacity": 150,
                "stock": 147,
                "start_event": "2025-12-31 00:00:00",
                "price": "100000.00",
                "created_at": "2025-12-26T17:37:57.000000Z",
                "updated_at": "2025-12-26T18:16:02.000000Z"
            }
        }
    ]
}
```

**2. Create Transaction**

```http
POST /api/transactions
```

**Request Body:**

```json
{
    "event_id": 1,
    "quantity": 3
}
```

**Response (201):**

```json
{
    "message": "Transaksi berhasil dilakukan",
    "data": {
        "user_id": 1,
        "event_id": 1,
        "quantity": 3,
        "total_price": 300000,
        "status": "paid",
        "updated_at": "2025-12-26T18:16:02.000000Z",
        "created_at": "2025-12-26T18:16:02.000000Z",
        "id": 1,
        "event": {
            "id": 1,
            "user_id": 1,
            "title": "Konser Musik Tahun Baru",
            "capacity": 150,
            "stock": 147,
            "start_event": "2025-12-31 00:00:00",
            "price": "100000.00",
            "created_at": "2025-12-26T17:37:57.000000Z",
            "updated_at": "2025-12-26T18:16:02.000000Z"
        }
    }
}
```

**3. Update Transaction Status**

```http
PATCH /api/transactions/{id}
```

**Request Body:**

```json
{
    "status": "cancelled" // "waiting", "paid", "cancelled", or "expired"
}
```

**Response (200):**

```json
{
    "message": "Status transaksi berhasil diperbarui",
    "data": {
        "id": 1,
        "user_id": 1,
        "event_id": 1,
        "quantity": 3,
        "total_price": "300000.00",
        "status": "cancelled",
        "created_at": "2025-12-26T18:16:02.000000Z",
        "updated_at": "2025-12-26T18:44:33.000000Z",
        "event": {
            "id": 1,
            "user_id": 1,
            "title": "Konser Musik Tahun Baru",
            "capacity": 150,
            "stock": 150,
            "start_event": "2025-12-31 00:00:00",
            "price": "100000.00",
            "created_at": "2025-12-26T17:37:57.000000Z",
            "updated_at": "2025-12-26T18:44:33.000000Z"
        }
    }
}
```

**4. Delete Transaction**

```http
DELETE /api/transaction/{id}
```

**Response (200):**

```json
{
    "message": "Transaksi berhasil dihapus"
}
```

### Error Responses

**Forbidden Error (403):**

```json
{
    "message": "Forbidden"
}
```

**Auth Error (401):**

```json
{
    "message": "Unauthenticated."
}
```
