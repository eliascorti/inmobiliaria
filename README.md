# Sistema de Gestión Inmobiliaria

Proyecto de **Práctica Profesionalizante I**. Sistema web interno para una inmobiliaria: gestión de clientes, propiedades (con imagen) y contratos de alquiler.

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

## Estructura

Es la estructura que crea Symfony, más la carpeta `src/Manager` (lógica de negocio, como en los ejercicios de la cursada):

```
src/
  Controller/    reciben el pedido y llaman al Manager
  Manager/       resuelven cada caso de uso
  Entity/        Usuario, Cliente, Propiedad, Contrato
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

3. Abrir `http://localhost/inmobiliaria/public`.

El detalle de cada comando está en [COMANDOS.md](COMANDOS.md).
