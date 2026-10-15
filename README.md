# Proyecto Symfony

Aplicación de gestión de tareas desarrollada con Symfony 7.4.

## Requisitos

- PHP 8.2 o superior
- Composer
- Docker con Docker Compose

## Ejecución local

```bash
composer install
cp .env.example .env
# Cambia APP_SECRET y las contraseñas MYSQL_* antes de continuar.
docker compose up -d --wait database
php bin/console doctrine:migrations:migrate --no-interaction
php -S 127.0.0.1:8000 -t public
```

La aplicación queda disponible en <http://127.0.0.1:8000> y MySQL se publica únicamente en `127.0.0.1:3306`.

Para detener la base de datos:

```bash
docker compose down
```

## Autor

Desarrollado por **aalejoz25**.
