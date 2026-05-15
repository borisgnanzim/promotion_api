# API Documentation

## Overview

Cette documentation couvre les endpoints disponibles dans l’API du backend.
L’API utilise Laravel Sanctum pour l’authentification des routes protégées.

Base URL : `/api`

---

## Authentication

### 1. Login standard

- `POST /api/auth/login`
- Contenu JSON :
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```
- Réponse succès :
```json
{
  "success": true,
  "data": {
    "user": { ... },
    "token": "...",
    "role": "client"
  },
  "message": "User logged in successfully"
}
```

### 2. Login admin

- `POST /api/auth/admin/login`
- Contenu JSON :
```json
{
  "email": "admin@example.com",
  "password": "password123"
}
```
- Réponse succès :
```json
{
  "success": true,
  "data": {
    "token": "..."
  },
  "message": "Login successful"
}
```

### 3. Request OTP

- `POST /api/auth/otp/request`
- Permet de demander un code OTP via email ou SMS.
- Contenu JSON (email) :
```json
{
  "email": "user@example.com"
}
```
- Contenu JSON (téléphone) :
```json
{
  "phone_number": "+33123456789"
}
```
- Réponse succès :
```json
{
  "success": true,
  "data": {
    "contact": "user@example.com"
  },
  "message": "Code OTP envoyé."
}
```

### 4. Verify OTP

- `POST /api/auth/otp/verify`
- Vérifie le code OTP et retourne un token d’authentification.
- Contenu JSON (email) :
```json
{
  "email": "user@example.com",
  "code": "123456"
}
```
- Contenu JSON (téléphone) :
```json
{
  "phone_number": "+33123456789",
  "code": "123456"
}
```
- Réponse succès :
```json
{
  "success": true,
  "data": {
    "user": { ... },
    "token": "...",
    "role": "client"
  },
  "message": "Connexion par OTP réussie"
}
```

### 5. Logout

- `POST /api/auth/logout`
- Nécessite une authentification Sanctum.
- Définit `auth_token` dans le cookie ou en header Bearer.
- Réponse succès :
```json
{
  "success": true,
  "data": "",
  "message": "Logged out successfully"
}
```

### 6. Profile

- `GET /api/auth/profile`
- Requiert une authentification.
- Réponse succès :
```json
{
  "success": true,
  "data": { ... }
}
```

---

## Admin routes

Ces routes sont protégées par `auth:sanctum` et l’ability `admin`.

### Categories

- `GET /api/admin/categories`
- `POST /api/admin/categories`
- `GET /api/admin/categories/{ref}`
- `PUT /api/admin/categories/{ref}`
- `DELETE /api/admin/categories/{ref}`

### Images

- `GET /api/admin/images`
- `POST /api/admin/images`
- `GET /api/admin/images/{ref}`
- `DELETE /api/admin/images/{ref}`

### Users

- `GET /api/admin/users`
- `POST /api/admin/users`
- `GET /api/admin/users/{ref}`
- `PUT /api/admin/users/{ref}`
- `DELETE /api/admin/users/{ref}`

### Promotions

- `GET /api/admin/promotions`
- `POST /api/admin/promotions`
- `GET /api/admin/promotions/{ref}`
- `PUT /api/admin/promotions/{ref}`
- `DELETE /api/admin/promotions/{ref}`

### Stores

- `GET /api/admin/stores`
- `POST /api/admin/stores`
- `GET /api/admin/stores/{ref}`
- `PUT /api/admin/stores/{ref}`
- `DELETE /api/admin/stores/{ref}`

---

## Authentication headers

Pour les routes protégées :

- `Authorization: Bearer {token}`

ou via cookie `auth_token` si l’application front-end le supporte.

---

## Notes importantes

- Pour utiliser l’OTP par SMS, le champ `phone_number` doit être renseigné sur l’utilisateur.
- Assure-toi que les variables d’environnement Mail et Vonage sont configurées si tu veux envoyer des emails et SMS :
  - `MAIL_MAILER`
  - `MAIL_HOST`
  - `MAIL_PORT`
  - `MAIL_USERNAME`
  - `MAIL_PASSWORD`
  - `VONAGE_KEY`
  - `VONAGE_SECRET`
  - `VONAGE_SMS_FROM`

---

## Convention de réponse

L’API renvoie généralement :

```json
{
  "success": true,
  "data": { ... },
  "message": "..."
}
```

Les erreurs utilisent un statut HTTP approprié et un message explicite.
