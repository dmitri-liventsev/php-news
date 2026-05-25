# Symfony Sample Project | News

This project was completed as part of a test assignment.

## Technical Stack:

#### Backend
 * PHP 8.2
 * Symfony Framework 7.1
 * Doctrine 3.3

#### Frontend
* React 11
* Redux 9
* Redux Toolkit 2.2
* MUI 5

## Requirements

- Docker
- Docker Compose

## Setup Instructions

### 1. Start Docker Compose

From the project root:

```bash
docker compose up -d
```

This builds and starts the containers (`symfony_app`, `symfony_nginx`, `symfony_db`) in detached mode.

> All `bin/console` / `bin/phpunit` commands below run inside the PHP container. Use either `docker compose exec app …` or `docker exec symfony_app …` — both work.

### 2. Run Database Migrations

Create the schema in the dev database:

```bash
docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction
```

### 3. Generate JWT Keypair

The admin panel uses JWT auth. Without keys, login will silently return an empty token.

```bash
docker compose exec app php bin/console lexik:jwt:generate-keypair
```

The passphrase is read from `.env` (`JWT_PASSPHRASE`).

### 4. Load Fixtures

```bash
docker compose exec app php bin/console doctrine:fixtures:load --no-interaction
```

### 5. Create an Admin User

```bash
docker compose exec app php bin/console app:create-user --email="admin@example.com" --password="supersecret"
```

Replace email/password as you like — but note Chrome will refuse to save very weak passwords (e.g. `admin`).

### 6. Open the App

* Client:  http://localhost:8080/
* Admin:   http://localhost:8080/admin

## Running Tests

The test suite runs against a separate `symfony_test` database. First-time setup:

```bash
# Create the test DB and grant access to the symfony user
docker exec symfony_db mysql -uroot -proot -e \
  "CREATE DATABASE IF NOT EXISTS symfony_test; \
   GRANT ALL PRIVILEGES ON symfony_test.* TO 'symfony'@'%'; \
   FLUSH PRIVILEGES;"

# Create the schema in the test DB
docker compose exec app php bin/console doctrine:schema:create --env=test
```

Then run all tests:

```bash
docker compose exec app php bin/phpunit
```

## Additional Commands

### Stop containers
```bash
docker compose down
```

### Stop and wipe DB volume
```bash
docker compose down -v
```

### Follow logs
```bash
docker compose logs -f
```

### Shell into the PHP container
```bash
docker exec -it symfony_app bash
```

### Clear Symfony cache
```bash
docker compose exec app php bin/console cache:clear
```

## Troubleshooting

* **Login returns `{"token":""}`** — JWT keypair not generated. Run step 3.
* **`Access denied for user 'symfony'@'%' to database 'symfony_test'`** — test DB not created yet. See the Running Tests section.
* **`docker compose` not found** — older Docker installs use `docker-compose` (with a hyphen). Both work.
* Ensure Docker is running and you are executing commands from the project root.

## Contact
For any questions or suggestions, feel free to send a letter to Santa Claus.

Happy coding!