# Invoicing App

Invoicing application with a Laravel backend and a Vue.js frontend.

- **Postman Collection:** All API endpoints are documented in the [Invoice_CRUD.postman_collection.json](./Invoice_CRUD.postman_collection.json) file located at the project root.

- **Demo:** [Watch demo video of the project running locally](https://app.screencastify.com/watch/RZss7Ny40z7ukXhTKwXl)
---

## Features
- Create, update, view, and delete invoices.
- Draft and pending invoice status workflows.
- Mark invoices as paid.
- Strong backend validation and XSS protection.
- Modular, reusable Vue components.

---

## Prerequisites
- PHP >= 8.1
- Composer
- Node.js (v20+ required) & npm (v10+ required)
- MySQL or compatible database (or SQLite for local dev)

> This project is tested with Node.js v20.x and npm v10.x. Lower versions may not work due to modern dependencies.

---

## Getting Started

### 1. Clone the Repository
```sh
git clone <repo-url> <folder-name>
cd <folder-name>
cd app
```

### 2. Backend Setup (Laravel)
```sh
cd backend
cp .env.example .env
composer install
php artisan key:generate
```

- Set your database settings in `.env`:
  ```env
  DB_CONNECTION=sqlite
  DB_DATABASE=database/database.sqlite
  
  ```
- If the file `database/database.sqlite` does not exist, create it:
  ```sh
  touch database/database.sqlite
  ```

- Run migrations:
```sh
php artisan migrate
```

- (Optional) Seed the database:
```sh
php artisan db:seed
```

- Start the backend server:
```sh
php artisan serve
```

The backend API will be available at `http://localhost:8000` by default.

---

### 3. Frontend Setup (Vue.js)
```sh
cd ../frontend
npm install
```

- Copy `.env.example` to `.env`:
  ```sh
  cp .env.example .env
  ```

- Set your API base URL in `.env`:
  ```env
  VITE_API_BASE_URL=http://localhost:8000
  ```

- Start the frontend dev server:
```sh
npm run dev
```

The frontend will be available at the URL shown in your terminal (typically `http://localhost:5173`).

---


## Project Structure
```
invoicing-app/
  backend/    # Laravel API
  frontend/   # Vue.js SPA
```

---

## Useful Commands
- **Backend:**
  - `php artisan migrate:fresh --seed` — Reset and reseed the database
- **Frontend:**
  - `npm run build` — Build for production

---