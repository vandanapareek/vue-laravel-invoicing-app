# Invoicing App

A modern invoicing application with a Laravel backend and a Vue.js frontend. This project supports robust backend validation, clean UI/UX, and maintainable code.

---

## Features
- Create, update, view, and delete invoices
- Draft and pending invoice status workflows
- Mark invoices as paid (secure endpoint)
- Strong backend validation and XSS protection
- Modular, reusable Vue components
- Consistent, modern UI

---

## Prerequisites
- PHP >= 8.1
- Composer
- Node.js (v16+ recommended) & npm
- MySQL or compatible database (or SQLite for local dev)

---

## Getting Started

### 1. Clone the Repository
```sh
git clone <your-repo-url> invoicing-app
cd invoicing-app
```

### 2. Backend Setup (Laravel)
```sh
cd backend
cp .env.example .env
composer install
php artisan key:generate
```

- Configure your database settings in `.env`:
  ```env
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=your_db_name
  DB_USERNAME=your_db_user
  DB_PASSWORD=your_db_password
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

## API Overview
- All invoice-related endpoints are under `/api/invoices`.
- Only `draft` and `pending` statuses can be set via create/update. Use the `/api/invoices/{id}/pay` endpoint to mark as paid.
- All input is sanitized and validated on the backend.

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
  - `php artisan test` — Run backend tests
- **Frontend:**
  - `npm run build` — Build for production

---

## Security & Best Practices
- All user input is sanitized to prevent XSS.
- Only allowed status transitions are permitted.
- Modular, maintainable codebase for both backend and frontend.

---

## License
This project is for educational/demo purposes. Add your license here if needed. 