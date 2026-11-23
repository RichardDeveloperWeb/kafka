# Los Cuentos de Kafka

Aplicación web de una librería con catálogo de libros, revistas, audiolibros y material didáctico. Incluye carrito, checkout con envío, área de cliente, reservas de preventas con anticipo y saldo simulado, formulario de contacto y panel administrativo con roles y permisos.

La interfaz está en español. El pago de preventas es demostrativo: no se conecta a ninguna pasarela real.

## Stack

- Laravel 13, PHP 8.5 y PostgreSQL 18.
- Inertia 3, Vue 3 y Tailwind 4.
- Fortify para autenticación y Spatie Laravel Permission para roles.
- Laravel Sail y Docker Compose para el entorno local.

## Requisitos

- Docker y Docker Compose, o Docker Desktop.
- Git.
- Puertos libres para la web y la base de datos. Por defecto se usan `80` y `5432`; si el puerto 80 está ocupado, define `APP_PORT=8000` en `.env`.

## Instalación con Docker

1. Clona el proyecto y entra en la carpeta:

```bash
git clone https://github.com/RichardDeveloperWeb/kafka.git
cd kafka
```

2. Crea el archivo de entorno:

```bash
cp .env.example .env
```

3. Edita `.env` para que la app use el servicio PostgreSQL de Docker:

```dotenv
APP_URL=http://localhost
APP_PORT=80

DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=kafka
DB_USERNAME=root
DB_PASSWORD=password
```

Si cambias `APP_PORT` a `8000`, cambia también `APP_URL` a `http://localhost:8000`.

4. En Linux/macOS, exporta tu usuario para evitar problemas de permisos con archivos creados por Docker:

```bash
export WWWUSER=$(id -u)
export WWWGROUP=$(id -g)
```

5. Instala las dependencias de PHP usando solo Docker:

```bash
docker run --rm -u "$(id -u):$(id -g)" -v "$PWD:/app" -w /app composer:2 composer install --no-interaction
```

6. Construye y levanta los contenedores:

```bash
docker compose up -d --build
```

7. Instala dependencias de JavaScript, genera la clave, migra y siembra la base de datos:

```bash
docker compose exec -T laravel.test composer setup
```

8. Abre la aplicación en `http://localhost` o en el puerto que hayas configurado.

## Usuarios de ejemplo

Todos usan la contraseña `password` y ya están verificados.

| Correo                   | Rol                 | Datos de ejemplo              |
| ------------------------ | ------------------- | ----------------------------- |
| `superadmin@example.com` | Super administrador | Acceso total                  |
| `admin@example.com`      | Administrador       | Panel administrativo          |
| `cliente@example.com`    | Cliente             | Pedidos y mensajes de ejemplo |
| `cliente2@example.com`   | Cliente             | Pedidos y mensajes de ejemplo |
| `test@example.com`       | Cliente             | Cliente básico                |

## Uso diario

Para iniciar el servidor, la cola, los logs y Vite:

```bash
docker compose exec laravel.test composer dev
```

Para reiniciar la base de datos con datos de ejemplo:

```bash
docker compose exec -T laravel.test php artisan migrate:fresh --seed --force
```

Para ejecutar las verificaciones completas:

```bash
docker compose exec -T laravel.test composer ci:check
```

Para detener los contenedores:

```bash
docker compose down
```

Para detenerlos y borrar también el volumen de PostgreSQL:

```bash
docker compose down -v
```
