# Postman API Testing - Példák

## Base URL
```
http://localhost:8000/api
```

---

## 1. REGISZTRÁCIÓ (POST /api/register)

**URL:** `http://localhost:8000/api/register`  
**Method:** `POST`  
**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Body (raw JSON):**
```json
{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Válasz példa (201):**
```json
{
    "message": "Registration successful",
    "user": {
        "name": "Test User",
        "email": "test@example.com",
        "updated_at": "2025-12-04T10:30:00.000000Z",
        "created_at": "2025-12-04T10:30:00.000000Z",
        "id": 1
    },
    "access_token": "1|abcdefghijklmnopqrstuvwxyz123456789",
    "token_type": "Bearer"
}
```

---

## 2. BEJELENTKEZÉS (POST /api/login)

**URL:** `http://localhost:8000/api/login`  
**Method:** `POST`  
**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Body (raw JSON):**
```json
{
    "email": "test@example.com",
    "password": "password123"
}
```

**Válasz példa (200):**
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "Test User",
        "email": "test@example.com",
        "email_verified_at": null,
        "created_at": "2025-12-04T10:30:00.000000Z",
        "updated_at": "2025-12-04T10:30:00.000000Z"
    },
    "access_token": "2|xyz987654321abcdefghijklmnop",
    "token_type": "Bearer"
}
```

**⚠️ FONTOS:** Másold ki az `access_token` értékét! Ez kell az alábbi kérésekhez!

---

## 3. PAYMENT LÉTREHOZÁSA (POST /api/payments)

**URL:** `http://localhost:8000/api/payments`  
**Method:** `POST`  
**Headers:**
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer 2|xyz987654321abcdefghijklmnop
```

**Body (raw JSON):**
```json
{
    "order_id": 1,
    "payment_method": "credit_card",
    "amount": 150.50,
    "paid_at": "2025-12-04 10:45:00"
}
```

**Másik példa:**
```json
{
    "order_id": 2,
    "payment_method": "paypal",
    "amount": 99.99,
    "paid_at": null
}
```

**Válasz példa (201):**
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

---

## 4. ÖSSZES PAYMENT LEKÉRÉSE (GET /api/payments)

**URL:** `http://localhost:8000/api/payments`  
**Method:** `GET`  
**Headers:**
```
Accept: application/json
Authorization: Bearer 2|xyz987654321abcdefghijklmnop
```

**Body:** Nincs

**Válasz példa (200):**
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
                "status": "pending"
            }
        }
    ]
}
```

---

## 5. EGY PAYMENT LEKÉRÉSE (GET /api/payments/{id})

**URL:** `http://localhost:8000/api/payments/1`  
**Method:** `GET`  
**Headers:**
```
Accept: application/json
Authorization: Bearer 2|xyz987654321abcdefghijklmnop
```

**Body:** Nincs

**Válasz példa (200):**
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

**Hiba példa (404):**
```json
{
    "success": false,
    "message": "Payment not found"
}
```

---

## 6. PAYMENT FRISSÍTÉSE (PUT /api/payments/{id})

**URL:** `http://localhost:8000/api/payments/1`  
**Method:** `PUT` vagy `PATCH`  
**Headers:**
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer 2|xyz987654321abcdefghijklmnop
```

**Body (raw JSON) - Teljes frissítés (PUT):**
```json
{
    "order_id": 1,
    "payment_method": "bank_transfer",
    "amount": 175.00,
    "paid_at": "2025-12-04 12:00:00"
}
```

**Body (raw JSON) - Részleges frissítés (PATCH):**
```json
{
    "payment_method": "stripe",
    "amount": 180.00
}
```

**Válasz példa (200):**
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

## 7. PAYMENT TÖRLÉSE (DELETE /api/payments/{id})

**URL:** `http://localhost:8000/api/payments/1`  
**Method:** `DELETE`  
**Headers:**
```
Accept: application/json
Authorization: Bearer 2|xyz987654321abcdefghijklmnop
```

**Body:** Nincs

**Válasz példa (200):**
```json
{
    "success": true,
    "message": "Payment deleted successfully"
}
```

**Hiba példa (404):**
```json
{
    "success": false,
    "message": "Payment not found"
}
```

---

## 8. KIJELENTKEZÉS (POST /api/logout)

**URL:** `http://localhost:8000/api/logout`  
**Method:** `POST`  
**Headers:**
```
Accept: application/json
Authorization: Bearer 2|xyz987654321abcdefghijklmnop
```

**Body:** Nincs

**Válasz példa (200):**
```json
{
    "message": "Logout successful"
}
```

---

## 9. BEJELENTKEZETT USER ADATAI (GET /api/user)

**URL:** `http://localhost:8000/api/user`  
**Method:** `GET`  
**Headers:**
```
Accept: application/json
Authorization: Bearer 2|xyz987654321abcdefghijklmnop
```

**Body:** Nincs

**Válasz példa (200):**
```json
{
    "id": 1,
    "name": "Test User",
    "email": "test@example.com",
    "email_verified_at": null,
    "created_at": "2025-12-04T10:30:00.000000Z",
    "updated_at": "2025-12-04T10:30:00.000000Z"
}
```

---

## POSTMAN BEÁLLÍTÁSOK

### Authorization Header beállítása:
1. A kérés **Headers** fülén add hozzá:
   - **Key:** `Authorization`
   - **Value:** `Bearer IDE_A_TOKENED`

VAGY

2. Az **Authorization** fülön válaszd a **Type: Bearer Token** opciót
   - Illeszd be a token-t a mezőbe

### Environment Variables (opcionális):
Postman-ben létrehozhatsz változókat:
- `{{base_url}}` = `http://localhost:8000/api`
- `{{token}}` = A bejelentkezés után kapott token

Így használhatod: `{{base_url}}/payments`

---

## TESZTELÉSI SORREND

1. **Regisztráció** → Szerezz token-t
2. **Bejelentkezés** → Frissítsd a token-t ha kell
3. **Payment létrehozása** → Először kell order!
4. **Összes payment lekérése** → Ellenőrzés
5. **Egy payment lekérése** → Használd a létrehozott ID-t
6. **Payment frissítése** → Módosítsd az adatokat
7. **Payment törlése** → Töröld a létrehozott rekordot
8. **Kijelentkezés** → Invalidálja a token-t

---

## HIBAKEZELÉS

### 401 Unauthorized
```json
{
    "message": "Unauthenticated."
}
```
→ Hiányzik vagy hibás a Bearer token

### 422 Validation Error
```json
{
    "success": false,
    "errors": {
        "email": ["The email has already been taken."],
        "password": ["The password field confirmation does not match."]
    }
}
```
→ Validációs hiba

### 404 Not Found
```json
{
    "success": false,
    "message": "Payment not found"
}
```
→ A keresett resource nem létezik
