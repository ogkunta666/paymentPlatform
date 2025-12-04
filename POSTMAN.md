# API ENDPOINTS

## PING

**GET** `http://localhost:8000/api/ping`

**Headers:**
```
Accept: application/json
```

---

## REGISTER

**POST** `http://localhost:8000/api/register`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Body:**
```json
{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

---

## LOGIN

**POST** `http://localhost:8000/api/login`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Body:**
```json
{
    "email": "test@example.com",
    "password": "password123"
}
```

---

## GET USER

**GET** `http://localhost:8000/api/user`

**Headers:**
```
Accept: application/json
Authorization: Bearer YOUR_TOKEN_HERE
```

---

## LOGOUT

**POST** `http://localhost:8000/api/logout`

**Headers:**
```
Accept: application/json
Authorization: Bearer YOUR_TOKEN_HERE
```

---

## CREATE PAYMENT

**POST** `http://localhost:8000/api/payments`

**Headers:**
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer YOUR_TOKEN_HERE
```

**Body:**
```json
{
    "order_id": 1,
    "payment_method": "credit_card",
    "amount": 150.50,
    "paid_at": "2025-12-04 10:45:00"
}
```

---

## GET ALL PAYMENTS

**GET** `http://localhost:8000/api/payments`

**Headers:**
```
Accept: application/json
Authorization: Bearer YOUR_TOKEN_HERE
```

---

## GET ONE PAYMENT

**GET** `http://localhost:8000/api/payments/1`

**Headers:**
```
Accept: application/json
Authorization: Bearer YOUR_TOKEN_HERE
```

---

## UPDATE PAYMENT

**PUT** `http://localhost:8000/api/payments/1`

**Headers:**
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer YOUR_TOKEN_HERE
```

**Body:**
```json
{
    "order_id": 1,
    "payment_method": "bank_transfer",
    "amount": 175.00,
    "paid_at": "2025-12-04 12:00:00"
}
```

---

## UPDATE PAYMENT (PATCH)

**PATCH** `http://localhost:8000/api/payments/1`

**Headers:**
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer YOUR_TOKEN_HERE
```

**Body:**
```json
{
    "payment_method": "stripe",
    "amount": 180.00
}
```

---

## DELETE PAYMENT

**DELETE** `http://localhost:8000/api/payments/1`

**Headers:**
```
Accept: application/json
Authorization: Bearer YOUR_TOKEN_HERE
```
