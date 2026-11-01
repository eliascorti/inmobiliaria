# Comandos utilizados

Todos los comandos se ejecutan desde la **terminal de Laragon** (usa PHP 8.3, el mismo que Apache), parados en `C:\laragon\www\inmobiliaria`, salvo que se indique otra carpeta.

La columna **Fuente** indica en qué apunte de la cursada aparece el comando.

## 1. Creación del proyecto

| # | Comando | Para qué | Fuente |
|---|---|---|---|
| 1 | `cd C:\laragon\www` | Pararse en la carpeta que publica Laragon. | — |
| 2 | `composer create-project symfony/skeleton:"^6.4.2" inmobiliaria` | Crear el esqueleto de Symfony 6.4. | Unidad 7 (práctico) |
| 3 | `cd inmobiliaria` | Entrar al proyecto. | Unidad 7 (práctico) |
| 4 | `composer require symfony/webapp-pack` | Instalar las librerías de una aplicación web (Twig, Doctrine, seguridad, maker, etc.). | Unidad 7 (práctico) |
| 5 | `composer require symfony/apache-pack` | Instalar el soporte para Apache. | Unidad 7 (práctico) |
| 6 | `composer config extra.symfony.allow-contrib true` | Permitir las recetas *contrib*: la de apache-pack no se había ejecutado y faltaba `public/.htaccess`. | — |
| 7 | `composer recipes:install symfony/apache-pack --force` | Ejecutar la receta de apache-pack, que crea `public/.htaccess`. | — |

Prueba: `http://localhost/inmobiliaria/public` (o `http://inmobiliaria.test`).

## 2. Repositorio git

| # | Comando | Para qué |
|---|---|---|
| 8 | `git init -b main` | Crear el repositorio local. |
| 9 | `git add .` y `git commit -m "..."` | Guardar cada avance (un commit por pantalla). |

## 3. Base de datos

En `.env` se cambió `DATABASE_URL` por la de MySQL de Laragon (usuario `root` sin contraseña):

```
DATABASE_URL="mysql://root:@127.0.0.1:3307/inmobiliaria?serverVersion=8.4.3&charset=utf8mb4"
```

**Puerto 3307:** en esta computadora el puerto 3306 lo ocupa otro MySQL (el servicio de Windows *MySQL80*). Por eso el MySQL de Laragon se pasó al puerto 3307, cambiando `port=3306` por `port=3307` en `C:\laragon\bin\mysql\mysql-8.4.3-winx64\my.ini` (hay una copia del original en `my.ini.bak-3306`). En una computadora sin MySQL80 se puede volver al 3306, como en el apunte de la Unidad 9.

| # | Comando | Para qué | Fuente |
|---|---|---|---|
| 20 | `php bin/console doctrine:database:create` | Crear la base `inmobiliaria`. | Unidad 8 / 9 |
| 21 | `php bin/console make:migration` | Generar el script SQL de las cuatro entidades (`src/Migrations/`). | Unidad 8 / 9 |
| 22 | `php bin/console doctrine:migrations:migrate` | Crear las tablas. | Unidad 8 / 9 |
| 23 | `php bin/console doctrine:fixtures:load` | Cargar los datos de prueba (borra los datos anteriores). | Unidad 9 |

## 4. Entidades

| # | Comando | Respuestas | Fuente |
|---|---|---|---|
| 10 | `php bin/console make:user Usuario` | Guardar en la base: sí · identificador: `email` · contraseña con hash: sí | Unidad 9 (Seguridad) / Unidad 10 |
| 11 | `php bin/console make:entity Usuario` | `nombre` string(100), no nulo | Unidad 10 |
| 12 | `php bin/console make:entity Cliente` | `nombre` string(100) · `apellido` string(100) · `dni` string(20) · `telefono` string(30) · `email` string(150) — todos no nulos | Unidad 8 / 9 |
| 13 | `php bin/console make:entity Propiedad` | `direccion` string(150) · `tipo` string(30) · `ambientes` integer · `precio` integer · `estado` string(20) · `imagen` string(255) nulo · `propietario` **ManyToOne** → `Cliente`, no nulo, acceso desde Cliente: sí (`propiedades`), orphanRemoval: no | Unidad 8 / 11 |
| 14 | `php bin/console make:entity Contrato` | `fechaInicio` date · `fechaFin` date · `precioMensual` integer · `estado` string(20) · `propiedad` **ManyToOne** → `Propiedad` (acceso: `contratos`) · `inquilino` **ManyToOne** → `Cliente` (acceso: `contratos`) — todos no nulos | Unidad 8 / 11 |

Después de `make:entity` se agregó a mano `unique: true` en el DNI de `Cliente` (RN-01) y los métodos de negocio de cada entidad (`puedeEliminarse()`, `alquilar()`, `liberar()`, `cancelar()`, etc.).

## 5. Datos de prueba (fixtures)

| # | Comando | Para qué | Fuente |
|---|---|---|---|
| 15 | `composer require --dev doctrine/doctrine-fixtures-bundle` | Instalar el bundle de fixtures. | Unidad 9 (Entidades) |
| 16 | `php bin/console make:fixtures UsuarioFixtures` | Usuario de prueba con la contraseña hasheada. | Unidad 9 / 10 |
| 17 | `php bin/console make:fixtures ClienteFixtures` | Seis clientes. | Unidad 9 |
| 18 | `php bin/console make:fixtures PropiedadFixtures` | Cinco propiedades con imagen de ejemplo (depende de clientes). | Unidad 9 |
| 19 | `php bin/console make:fixtures ContratoFixtures` | Tres contratos (depende de propiedades y clientes). | Unidad 9 |
