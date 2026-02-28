# 🎫 EPA Prodaja Karata

Veb aplikacija za prodaju ulaznica za događaje razvijena kao seminarski rad na Fakultetu organizacionih nauka, Univerzitet u Beogradu.

## 👥 Tim

- Ana Obradović (2022/0358)
- Petar Nikolić (2022/0110)
- Elza Osmani (2022/0409)

**Mentor:** Aleksandar Joksimović

## 🚀 Tehnologije

### Backend
- Laravel 12
- PHP 8.4
- MySQL 8.0
- Redis (Queue sistem)

### Frontend
- React 18
- Inertia.js
- Tailwind CSS
- Vite

### DevOps
- Docker & Docker Compose
- Git & GitHub

## ✨ Funkcionalnosti

- 🔐 Autentifikacija (Register/Login/Logout)
- 👥 Role-based pristup (User, Moderator, Admin)
- 🎭 CRUD operacije za događaje
- 🎫 Tri tipa ulaznica (Standard, Premium, VIP)
- 🛒 Kupovina ulaznica sa Redis Queue sistemom (FIFO)
- 🔍 Pretraga i filtriranje događaja
- 📊 Admin statistika prodaje
- 📱 Responzivan dizajn

## 📦 Instalacija

### Preduslov
- Docker Desktop
- Git

### Pokretanje projekta

1. **Kloniraj repozitorijum:**

git clone https://github.com/elab-development/internet-tehnologije-2025-prodajaulaznica_2022_0110.git
cd internet-tehnologije-2025-prodajaulaznica_2022_0110

2. **Pokreni Docker containere:**
docker-compose up -d --build

3. **Pokreni migracije i seedere:**
docker exec ticket-app php artisan migrate --seed

4. **Otvori aplikaciju:**
http://localhost:8000

## 🐳 Docker Servisi

Projekat koristi 4 Docker servisa:

- **ticket-app** - Laravel backend (port 8000)
- **ticket-vite** - Vite development server (port 5173)
- **ticket-mysql** - MySQL baza (port 3307)
- **ticket-redis** - Redis queue (port 6380)

## 🔧 Korisne komande

### Docker

# Pokreni sve servise
docker-compose up -d

# Zaustavi sve servise
docker-compose down

# Vidi logove
docker logs -f ticket-app

# Pristupi Laravel containeru
docker exec -it ticket-app bash

### Laravel
# Migracije
docker exec ticket-app php artisan migrate

# Seederi
docker exec ticket-app php artisan db:seed

# Clear cache
docker exec ticket-app php artisan config:clear
docker exec ticket-app php artisan cache:clear

# Queue worker
docker exec ticket-app php artisan queue:work

## 📊 Baza podataka

**Modeli:**
- User (name, surname, username, email, role)
- Category (name, description)
- Event (title, description, location, dates, status)
- TicketType (name, price, capacity, sold)
- Order (user_id, event_id, total_amount, status)
- Ticket (unique_code, price, purchased_at, status)

## 🔒 Bezbednost

Aplikacija je zaštićena od:
- **CSRF** - Laravel middleware
- **SQL Injection** - Eloquent ORM
- **XSS** - React automatic escaping
- **CORS** - Konfigurisan CORS policy

## 📝 Licenca

Projekat je kreiran u edukativne svrhe za kurs Internet Tehnologije.

## 📧 Kontakt

Za pitanja kontaktirajte članove tima preko GitHub-a.
