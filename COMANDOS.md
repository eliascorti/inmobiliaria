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
