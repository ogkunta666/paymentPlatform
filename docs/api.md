# Payment Platform API Dokumentáció

## Áttekintés

Ez a dokumentáció a Payment Platform Laravel API végpontjait írja le. Az API RESTful elveket követ és JSON formátumban kommunikál.

**Base URL:** `http://localhost:8000/api`

---

## Authentikáció

Az API Laravel Sanctum alapú Bearer Token authentikációt használ. A védett végpontokhoz a `Authorization` headerben kell elküldeni a tokent.

### Token megszerzése
1. Regisztráció vagy bejelentkezés
2. A válaszban kapott `access_token` használata
3. Header formátum: `Authorization: Bearer YOUR_TOKEN`

---

## Publikus Végpontok

Ezek a végpontok nem igényelnek authentikációt.

### 1. Ping - Szerver elérhetőség ellenőrzése

**Leírás:** Egyszerű végpont a szerver működésének tesztelésére.

**Endpoint:** `GET /api/ping`

**Headers:**
```
Accept: application/json
```

**Válasz példa:**
```json
{
    "success": true,
    "message": "pong",
    "timestamp": "2025-12-04T12:30:45+00:00",
    "server_time": "2025-12-04 12:30:45"
}
```

---

### 2. Regisztráció

**Leírás:** Új felhasználó létrehozása a rendszerben. A sikeres regisztráció után külön be kell jelentkezni a token megszerzéséhez.

**Endpoint:** `POST /api/register`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Mező magyarázat:**
- `name` (kötelező): A felhasználó neve (max 255 karakter)
- `email` (kötelező): Egyedi email cím, valid email formátum
- `password` (kötelező): Jelszó (minimum 8 karakter)
- `password_confirmation` (kötelező): Jelszó megerősítése (egyeznie kell a password-del)

**Sikeres válasz (201):**
```json
{
    "message": "Registration successful",
    "user": {
        "name": "Test User",
        "email": "test@example.com",
        "updated_at": "2025-12-04T10:30:00.000000Z",
        "created_at": "2025-12-04T10:30:00.000000Z",
        "id": 1
    }
}
```

**Validációs hiba (422):**
```json
{
    "message": "The email has already been taken.",
    "errors": {
        "email": ["The email has already been taken."]
    }
}
```

---

### 3. Bejelentkezés

**Leírás:** Bejelentkezés a rendszerbe. Sikeres authentikáció esetén Bearer tokent ad vissza.

**Endpoint:** `POST /api/login`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
    "email": "kunta@example.com",
    "password": "Super_Secret_Pw2025!"
}
```

**Mező magyarázat:**
- `email` (kötelező): Regisztrált email cím
- `password` (kötelező): A felhasználó jelszava

**Sikeres válasz (200):**
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "Kunta",
        "email": "kunta@example.com",
        "email_verified_at": null,
        "created_at": "2025-12-04T10:30:00.000000Z",
        "updated_at": "2025-12-04T10:30:00.000000Z"
    },
    "access_token": "1|abcdefghijklmnopqrstuvwxyz123456789",
    "token_type": "Bearer"
}
```

**⚠️ FONTOS:** Mentsd el az `access_token` értékét! Ez szükséges a védett végpontok eléréséhez.

**Hibás bejelentkezés (422):**
```json
{
    "message": "The provided credentials are incorrect.",
    "errors": {
        "email": ["The provided credentials are incorrect."]
    }
}
```

---

## Védett Végpontok

Ezek a végpontok Bearer Token authentikációt igényelnek.

### 4. Bejelentkezett Felhasználó Adatai

**Leírás:** A jelenleg bejelentkezett felhasználó adatainak lekérése.

**Endpoint:** `GET /api/user`

**Headers:**
```
Accept: application/json
Authorization: Bearer {{token}}
```

**Válasz (200):**
```json
{
    "id": 1,
    "name": "Kunta",
    "email": "kunta@example.com",
    "email_verified_at": null,
    "created_at": "2025-12-04T10:30:00.000000Z",
    "updated_at": "2025-12-04T10:30:00.000000Z"
}
```

---

### 5. Kijelentkezés

**Leírás:** A jelenlegi token érvénytelenítése. A kijelentkezés után új bejelentkezés szükséges.

**Endpoint:** `POST /api/logout`

**Headers:**
```
Accept: application/json
Authorization: Bearer {{token}}
```

**Válasz (200):**
```json
{
    "message": "Logout successful"
}
```

---

## Payment CRUD Műveletek

Mind a payment műveletek Bearer Token authentikációt igényelnek.

### 6. Új Payment Létrehozása

**Leírás:** Új fizetés rögzítése egy megrendeléshez.

**Endpoint:** `POST /api/payments`

**Headers:**
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {{token}}
```

**Request Body:**
```json
{
    "order_id": 1,
    "payment_method": "credit_card",
    "amount": 150.50,
    "paid_at": "2025-12-04 10:45:00"
}
```

**Mező magyarázat:**
- `order_id` (kötelező): Létező megrendelés azonosítója (foreign key az orders táblához)
- `payment_method` (kötelező): Fizetési mód (pl.: credit_card, paypal, bank_transfer, cash, stripe)
- `amount` (kötelező): Fizetett összeg (pozitív szám, 2 tizedesjegy)
- `paid_at` (opcionális): Fizetés időpontja (datetime formátum vagy null)

**Sikeres válasz (201):**
```json
{
    "success": true,
    "message": "Payment created successfully",
    "data": {
        "id": 1,
        "order_id": 1,
        "payment_method": "credit_card",
        "amount": "150.50",
        "paid_at": "2025-12-04T10:45:00.000000Z",
        "created_at": "2025-12-04T11:00:00.000000Z",
        "order": {
            "id": 1,
            "user_id": 1,
            "total_amount": "150.50",
            "status": "pending",
            "created_at": "2025-12-04T09:00:00.000000Z",
            "updated_at": "2025-12-04T09:00:00.000000Z"
        }
    }
}
```

**Validációs hiba (422):**
```json
{
    "success": false,
    "errors": {
        "order_id": ["The selected order id is invalid."],
        "amount": ["The amount must be at least 0."]
    }
}
```

---

### 7. Összes Payment Lekérése

**Leírás:** Az összes payment rekord listázása a kapcsolódó order adatokkal együtt.

**Endpoint:** `GET /api/payments`

**Headers:**
```
Accept: application/json
Authorization: Bearer {{token}}
```

**Válasz (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "order_id": 1,
            "payment_method": "credit_card",
            "amount": "150.50",
            "paid_at": "2025-12-04T10:45:00.000000Z",
            "created_at": "2025-12-04T11:00:00.000000Z",
            "order": {
                "id": 1,
                "user_id": 1,
                "total_amount": "150.50",
                "status": "pending"
            }
        },
        {
            "id": 2,
            "order_id": 2,
            "payment_method": "paypal",
            "amount": "99.99",
            "paid_at": null,
            "created_at": "2025-12-04T11:15:00.000000Z",
            "order": {
                "id": 2,
                "user_id": 1,
                "total_amount": "99.99",
                "status": "processing"
            }
        }
    ]
}
```

---

### 8. Egy Payment Lekérése

**Leírás:** Egy konkrét payment részletes adatainak lekérése ID alapján.

**Endpoint:** `GET /api/payments/{id}`

**Példa:** `GET /api/payments/1`

**Headers:**
```
Accept: application/json
Authorization: Bearer {{token}}
```

**Sikeres válasz (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "order_id": 1,
        "payment_method": "credit_card",
        "amount": "150.50",
        "paid_at": "2025-12-04T10:45:00.000000Z",
        "created_at": "2025-12-04T11:00:00.000000Z",
        "order": {
            "id": 1,
            "user_id": 1,
            "total_amount": "150.50",
            "status": "pending",
            "created_at": "2025-12-04T09:00:00.000000Z",
            "updated_at": "2025-12-04T09:00:00.000000Z"
        }
    }
}
```

**Nem található (404):**
```json
{
    "success": false,
    "message": "Payment not found"
}
```

---

### 9. Payment Frissítése (PUT)

**Leírás:** Egy payment összes adatának teljes felülírása. Minden mezőt meg kell adni.

**Endpoint:** `PUT /api/payments/{id}`

**Példa:** `PUT /api/payments/1`

**Headers:**
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {{token}}
```

**Request Body:**
```json
{
    "order_id": 1,
    "payment_method": "bank_transfer",
    "amount": 175.00,
    "paid_at": "2025-12-04 12:00:00"
}
```

**Mező magyarázat:**
- PUT esetén minden mezőt kötelező megadni
- Az adatok teljesen felülírják a meglévő payment rekordot

**Sikeres válasz (200):**
```json
{
    "success": true,
    "message": "Payment updated successfully",
    "data": {
        "id": 1,
        "order_id": 1,
        "payment_method": "bank_transfer",
        "amount": "175.00",
        "paid_at": "2025-12-04T12:00:00.000000Z",
        "created_at": "2025-12-04T11:00:00.000000Z",
        "order": {
            "id": 1,
            "user_id": 1,
            "total_amount": "150.50",
            "status": "pending"
        }
    }
}
```

---

### 10. Payment Frissítése (PATCH)

**Leírás:** Egy payment részleges módosítása. Csak a megadott mezők frissülnek.

**Endpoint:** `PATCH /api/payments/{id}`

**Példa:** `PATCH /api/payments/1`

**Headers:**
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {{token}}
```

**Request Body:**
```json
{
    "payment_method": "stripe",
    "amount": 180.00
}
```

**Mező magyarázat:**
- PATCH esetén csak a módosítani kívánt mezőket kell megadni
- A többi mező változatlan marad

**Sikeres válasz (200):**
```json
{
    "success": true,
    "message": "Payment updated successfully",
    "data": {
        "id": 1,
        "order_id": 1,
        "payment_method": "stripe",
        "amount": "180.00",
        "paid_at": "2025-12-04T12:00:00.000000Z",
        "created_at": "2025-12-04T11:00:00.000000Z",
        "order": {
            "id": 1,
            "user_id": 1,
            "total_amount": "150.50",
            "status": "pending"
        }
    }
}
```

---

### 11. Payment Törlése

**Leírás:** Egy payment végleges törlése az adatbázisból ID alapján.

**Endpoint:** `DELETE /api/payments/{id}`

**Példa:** `DELETE /api/payments/1`

**Headers:**
```
Accept: application/json
Authorization: Bearer {{token}}
```

**Sikeres válasz (200):**
```json
{
    "success": true,
    "message": "Payment deleted successfully"
}
```

**Nem található (404):**
```json
{
    "success": false,
    "message": "Payment not found"
}
```

---

## Hibakezelés

### HTTP Státuszkódok

- **200 OK** - Sikeres kérés
- **201 Created** - Sikeres létrehozás
- **401 Unauthorized** - Hiányzó vagy érvénytelen token
- **404 Not Found** - A keresett erőforrás nem található
- **422 Unprocessable Entity** - Validációs hiba

### Authentikációs Hiba (401)

**Oka:** Hiányzó, érvénytelen vagy lejárt Bearer token.

```json
{
    "message": "Unauthenticated."
}
```

**Megoldás:**
1. Jelentkezz be újra
2. Frissítsd a tokent az Authorization headerben
3. Ellenőrizd, hogy a token formátum helyes: `Bearer YOUR_TOKEN`

### Validációs Hiba (422)

**Oka:** A request body nem felel meg a validációs szabályoknak.

```json
{
    "success": false,
    "errors": {
        "email": ["The email has already been taken."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

**Megoldás:** Javítsd a megadott mezőket az errors objektumban jelzett hibák alapján.

---

## Adatbázis Struktúra

### Users tábla
- `id` - Egyedi azonosító
- `name` - Felhasználó neve
- `email` - Email cím (egyedi)
- `password` - Hash-elt jelszó
- `created_at`, `updated_at` - Időbélyegek

### Orders tábla
- `id` - Egyedi azonosító
- `user_id` - Foreign key a users táblához
- `total_amount` - Teljes összeg (decimal)
- `status` - Státusz (pending, processing, completed, cancelled)
- `created_at`, `updated_at` - Időbélyegek

### Payments tábla
- `id` - Egyedi azonosító
- `order_id` - Foreign key az orders táblához
- `payment_method` - Fizetési mód
- `amount` - Fizetett összeg (decimal)
- `paid_at` - Fizetés időpontja (nullable)
- `created_at` - Létrehozás időpontja (nincs updated_at)

---

## Teszt Felhasználó

Az adatbázis előre feltöltve egy teszt felhasználóval:

**Email:** `kunta@example.com`  
**Jelszó:** `Super_Secret_Pw2025!`

További 10 fake felhasználó magyar nevekkel és adatokkal, mindegyikhez tartozó orders és payments rekordokkal.

---

