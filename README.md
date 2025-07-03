# Laravel Skill Test Project – Laravel + Vue 3 + SQLite + Docker

This is a **Laravel skill testing project** developed with a **Laravel backend** and a **Vue 3 frontend**. It is containerized using **Docker** and uses **SQLite** as the database for simplicity and portability.

This project demonstrates full-stack development using Laravel and Vue, and includes a custom middleware that logs all incoming requests and their response times directly into a SQLite database.

## Prerequisites

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/)

## Project Structure

- **Laravel**: Backend API and web routes
- **Vue 3**: Frontend components (in `resources/js`)
- **Vite**: Asset bundler for Vue and JS/CSS
- **SQLite**: Database (file-based)

## Getting Started

### 1. Clone the repository

```sh
repo-ul = https://github.com/veenamani8/Times-world-laravel-test.git
git clone <your-repo-url>
cd myapp
```

### 2. Build and Start the Containers

```sh
docker-compose up --build
```

- Laravel app: [http://localhost:8000](http://localhost:8000)
- Vite dev server: [http://localhost:5173](http://localhost:5173)

### 3. Environment Variables

The Docker setup auto-generates a `.env` file from `.env.example` and sets:
- `DB_CONNECTION=sqlite`
- `DB_DATABASE=/var/www/database/database.sqlite`
- `ENABLE_REQUEST_LOG=true`

You can customize `.env` as needed.

### 4. Database

- SQLite database file is at `database/database.sqlite` (auto-created by Docker).
- Migrations run automatically on container start.

### 5. Frontend Development (Hot Reload)

- The `node` service runs `npm run dev` for Vite hot module reload.
- Edit Vue components in `resources/js/components/` and see changes live.

### 6. Production Build

For production, build assets and serve only with the PHP container:

```sh
docker-compose run --rm node npm run build
```
This generates static assets in `public/build`.

You can then stop the `node` service and serve with only the `app` service.

### 7. Useful Commands

- Rebuild containers: `docker-compose up --build`
- Stop containers: `docker-compose down`
- Run artisan commands: `docker-compose exec app php artisan <command>`
- Run npm commands: `docker-compose exec node npm <command>`

### 8. Preview

- https://drive.google.com/drive/folders/1uItRHE8KRYNhBoIUWCxNUuoT7JKMwkYE?usp=sharing
---

