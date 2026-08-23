# ANHS Voting System — Setup Guide

## Requirements

Make sure the following are installed on your laptop before proceeding:

| Software | Version | Download |
|---|---|---|
| XAMPP | Latest | https://www.apachefriends.org |
| Node.js | LTS (v20+) | https://nodejs.org |
| Composer | Latest | https://getcomposer.org |
| Git | Latest | https://git-scm.com |

---

## Step 1 — Clone the Repository

Open a terminal inside `C:\xampp\htdocs\` and run:

```bash
git clone https://github.com/JoshuaAdante/anhs-voting-system.git
cd anhs-voting-system
```

---

## Step 2 — Install PHP Dependencies

```bash
composer install
```

---

## Step 3 — Setup Environment File

```bash
copy .env.example .env
php artisan key:generate
```

Then open `.env` and update the database section:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=anhs_voting_system
DB_USERNAME=root
DB_PASSWORD=
```

> Leave `DB_PASSWORD` blank if your XAMPP MySQL has no password set.

---

## Step 4 — Create the Database

1. Open **XAMPP Control Panel** and start **Apache** and **MySQL**
2. Go to `http://localhost/phpmyadmin`
3. Create a new database named: `anhs_voting_system`

---

## Step 5 — Run Migrations

```bash
php artisan migrate
```

---

## Step 6 — Install Node Dependencies & Build Assets

```bash
npm install
npm run build
```

---

## Step 7 — Run the Application

Visit in your browser:
```
http://localhost/anhs-voting-system/public
```

Or use Laravel's built-in server:
```bash
php artisan serve
```
Then visit: `http://127.0.0.1:8000`

---

## Development Mode (Hot Reload)

Run these two commands in **separate terminals**:

```bash
php artisan serve
```

```bash
npm run dev
```

---

## Notes

- Do **not** share or commit your `.env` file — it contains sensitive configuration.
- The `.env` file is already excluded from Git via `.gitignore`.
