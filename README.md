# Sistema de Gestión Inmobiliaria

Proyecto de **Práctica Profesionalizante I**. Sistema web para una inmobiliaria: gestión de clientes, propiedades (con imagen) y contratos de alquiler, y una web pública donde cualquier persona ve las propiedades disponibles y pide un alquiler.

**Equipo:** Elías Corti · Marco Turchi · Tadeo Reinante · Joaquín Cavanna
**Docente:** Claudio Zonta

## Tecnologías

- PHP 8.3 + **Symfony 6.4** (rutas con `#[Route]`)
- **Doctrine ORM** con MySQL 8 (migraciones y fixtures)
- **Twig** + CSS propio (`public/css/estilos.css`)
- **Laragon** como servidor local (Apache + MySQL)

## Casos de uso

| CU | Caso de uso | Pantalla |
|---|---|---|
| CU-01 | Iniciar sesión | Login |
| CU-02 | Cerrar sesión | Todas |
| CU-03 | Consultar clientes | Clientes |
| CU-04 | Registrar cliente | Clientes |
| CU-05 | Modificar cliente | Clientes |
| CU-06 | Eliminar cliente | Clientes |
| CU-07 | Consultar propiedades | Propiedades y Detalle |
| CU-08 | Registrar propiedad | Propiedades |
| CU-09 | Modificar propiedad | Propiedades |
| CU-10 | Eliminar propiedad | Propiedades |
| CU-11 | Asignar propiedad a cliente | Detalle de propiedad |
| CU-12 | Cancelar contrato | Contratos |
| CU-13 | Modificar contrato | Contratos |
| CU-14 | Consultar solicitudes | Solicitudes |
| CU-15 | Aprobar solicitud | Solicitudes |
| CU-16 | Rechazar solicitud | Solicitudes |
| CU-17 | Consultar propiedades disponibles | Web pública: catálogo y detalle |
| CU-18 | Solicitar alquiler | Web pública: detalle |
| CU-19 | Consultar estado de solicitud | Web pública: consulta |

Actores: **Administrador** (CU-01 a CU-16, con sesión) y **Visitante** (CU-17 a CU-19, sin cuenta).

## Estructura

Es la estructura que crea Symfony, más la carpeta `src/Manager` (lógica de negocio, como en los ejercicios de la cursada):

```
src/
  Controller/    reciben el pedido y llaman al Manager
  Manager/       resuelven cada caso de uso
  Entity/        Usuario, Cliente, Propiedad, Contrato, Solicitud
  Repository/    consultas a la base
  DataFixtures/  datos de prueba
templates/       plantillas Twig
public/css/      estilos
```

## Cómo levantarlo

1. En Laragon: **Iniciar todo**.
2. En la terminal de Laragon, dentro de `C:\laragon\www\inmobiliaria`:

   ```
   composer install
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   php bin/console doctrine:fixtures:load
   ```

3. Abrir `http://localhost/inmobiliaria/public` (o `http://inmobiliaria.test`). La web pública está en `http://localhost/inmobiliaria/public/catalogo`.

Usuario de prueba (lo crea `UsuarioFixtures`): `admin@inmobiliaria.com` / `admin123`.

> En esta computadora MySQL de Laragon usa el puerto **3307**, porque el 3306 lo ocupa otro MySQL instalado como servicio de Windows. Ver [COMANDOS.md](COMANDOS.md).

El detalle de cada comando está en [COMANDOS.md](COMANDOS.md).
