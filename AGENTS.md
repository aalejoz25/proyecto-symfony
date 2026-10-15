# AGENTS.md

## Project Shape
- Symfony 7.4 app running on PHP 8.5; dependencies are locked in `composer.lock` and require PHP `>=8.2`.
- Web entrypoint is `public/index.php`; Symfony kernel wiring is in `src/Kernel.php`.
- Main app code is small: controllers in `src/Controller`, Doctrine attribute entities in `src/Entity`, Symfony forms in `src/Form`, Twig views in `templates`.
- Routes are explicitly declared in `config/routes.yaml` with Spanish URLs (`/`, `/registro`, `/tareas`, `/tarea/{id}`, `/crear-tarea`, `/mis-tareas`, `/editar-tarea/{id}`, `/borrar-tarea/{id}`).

## Local Setup
- Install dependencies with `composer install`; vendor packages are expected because `bin/console` and `bin/phpunit` use `vendor/autoload.php`.
- `.env` currently points Doctrine at MySQL `mysql://root@127.0.0.1:3306/symfony_master`; `config/packages/doctrine.yaml` assumes `pdo_mysql` and MySQL server version `5.7`.
- If the database is missing, create it from the current entities with `php bin/console doctrine:database:create` then `php bin/console doctrine:schema:create`; there are no migrations checked in.
- Swiftmailer was removed during the Symfony upgrade; `MAILER_URL` in `.env` is currently unused.

## Commands
- Validate composer metadata: `composer validate --no-check-publish`.
- Check the container: `php bin/console lint:container`.
- Run tests: `php bin/phpunit` or `composer test`; the current repository has no test files, so PHPUnit reports `No tests executed!`.
- Start the app locally with the PHP built-in server: `php -S 127.0.0.1:8000 -t public public/index.php`.
- Do not use `php bin/console server:run`; `symfony/web-server-bundle` was removed.
- There are no repo-defined lint, formatter, static-analysis, or npm commands.

## Verification Notes
- Verified on this machine with PHP 8.5.11: `php bin/console about`, `php bin/console debug:router`, `php bin/console lint:container`, `composer validate --no-check-publish`, `php bin/phpunit`, `php bin/console doctrine:query:sql "SELECT 1"`, and HTTP `200` for `/` and `/registro`.
- `/tareas` is protected by security and redirects to `/` when unauthenticated; this is expected.

## Code Conventions To Preserve
- Controllers use `AbstractController` plus constructor-injected `EntityManagerInterface`; do not reintroduce `$this->getDoctrine()`.
- Entities use PHP attributes for Doctrine mapping and Validator constraints; do not reintroduce Doctrine annotation mappings.
- Keep route names and template paths aligned with `config/routes.yaml` and `templates/...`; controllers render `task/*` and `user/*` Twig templates directly.
- Authentication uses `App\Entity\User` as the entity provider by `email`; protected task routes require `ROLE_USER` via `config/packages/security.yaml`.
- User registration sets role to `ROLE_USER`, hashes with `UserPasswordHasherInterface`, then redirects to route `tasks`.
