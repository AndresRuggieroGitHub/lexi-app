# Lexi

Aplicación web de aprendizaje de idiomas basada en Laravel, manteniendo el aspecto del prototipo original y sus URLs terminadas en `.html`.

## Estado actual

Lexi ya funciona como aplicación web real:

- Laravel 12 gestiona rutas, autenticación, sesión, permisos y APIs.
- El `.env` actual del repositorio sigue apuntando a `sqlite` como estado operativo por defecto.
- SQLite sigue disponible como base de desarrollo simple.
- MySQL/MariaDB local ya tiene flujo completo de bootstrap, smoke test y arranque sin tocar `.env`.
- El frontend conserva el prototipo original, pero ya reutiliza layouts Blade y endpoints reales.
- Las páginas principales públicas, privadas y admin del prototipo ya resuelven Blade directamente desde Laravel, manteniendo URLs `.html` por routing.
- Auth, perfil, biblioteca y panel admin ya están migrados a Blade.

## Estado operativo de base de datos

Ahora mismo no está "todo pasado a MySQL" por defecto.

- Si no haces ningún cambio manual, Lexi arranca con `sqlite` usando `database/database.sqlite`.
- MySQL/MariaDB ya está soportado y es el camino objetivo para producción y validación local más realista.
- El cambio a MySQL local puede hacerse con `scripts/switch-env-to-mysql-local.ps1`.
- Para comprobar qué conexión está activa en este momento, puedes ejecutar `scripts/show-current-db-config.ps1`.

## Funcionalidades ya implementadas

- Login, registro, logout y recuperación de contraseña con sesión real.
- Perfil persistido con idioma nativo bloqueado y nombre editable.
- Biblioteca de vocabulario persistida en base de datos.
- Catálogo base de biblioteca servido desde backend con fallback al HTML estático.
- Colecciones de usuario persistidas en base de datos.
- Registro de intentos de ejercicios en backend.
- Progreso calculado desde endpoints reales.
- Rutas privadas protegidas con `auth`.
- Rutas de administración protegidas por rol `admin`.

## Arquitectura actual

### Routing y renderizado

- Laravel mantiene las rutas públicas y privadas con sufijo `.html`, pero el render real se hace desde vistas Blade y controladores.
- Las páginas privadas y administrativas principales ya usan vistas reales en `resources/views`.

### Frontend

- `public/style.css` y `public/js/script.js` son la fuente de verdad de estilos y comportamiento.
- `biblioteca.html` ya mezcla render estático heredado con catálogo cargado desde `/api/library/state` cuando la base de datos tiene contenido.

### Base de datos

- Fallback local simple: `sqlite` en `database/database.sqlite`.
- Camino recomendado para acercarse a producción: `mysql` o `mariadb` local con los scripts de `scripts/`.
- Las migraciones están aplicadas.
- `php artisan db:seed` crea el usuario admin inicial, catálogo base, datos base de billing y registros iniciales de AI.
- `admin-words.html` ya permite ver visualmente filas reales de `words`, `translations`, `categories` y la conexión activa.
- La configuración vive en `.env` y [config/database.php](config/database.php).

## Dirección de despliegue recomendada

- App Laravel en `AWS EC2` con `Apache + PHP`.
- Base de datos en `AWS RDS MySQL`.
- `SQLite` queda como opción cómoda de desarrollo local, no como destino final de producción.
- Plantilla de entorno MySQL: `.env.mysql.example`.
- Plantilla local para pruebas MySQL: `.env.mysql.local.example`.
- Compose local opcional: `docker-compose.mysql.yml`.
- Guía corta de referencia: `docs/deployment-aws-mysql.md`.
- Guía local MySQL: `docs/mysql-local.md`.

## ¿Hace falta pasar a phpMyAdmin?

No.

`phpMyAdmin` no es una base de datos; es solo una interfaz para administrar MySQL o MariaDB. El proyecto puede funcionar perfectamente sin `phpMyAdmin`.

Ahora mismo SQLite es una elección correcta para desarrollo porque:

- reduce complejidad local
- no requiere instalar un servidor de base de datos
- permite avanzar rápido en modelo de datos y lógica

## Cuándo conviene migrar a MySQL o MariaDB

Para un proyecto real desplegado, sí suele ser recomendable pasar a MySQL/MariaDB o PostgreSQL cuando quieras:

- despliegue multiusuario real
- backups y administración más cómodos
- hosting PHP tradicional
- separar aplicación y base de datos en servicios distintos

## Cómo pasar de SQLite a MySQL/MariaDB

1. Crear una base de datos nueva en MySQL o MariaDB.
2. Cambiar en `.env` los valores `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`.
3. Ejecutar `php artisan config:clear`.
4. Ejecutar `php artisan migrate`.
5. Si hace falta contenido inicial, preparar seeders y ejecutar `php artisan db:seed`.

Ejemplo mínimo para entorno MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lexi
DB_USERNAME=root
DB_PASSWORD=
```

Lo correcto es migrar por Laravel y sus migraciones, no mover tablas manualmente desde `phpMyAdmin`.

## Opción útil para ver MySQL de forma visual

Si quieres acercarte al escenario real y además ver la base de datos de forma visual, ahora tienes una opción local simple:

1. Levantar `MySQL` y `Adminer` con `docker-compose.mysql.yml`.
2. Usar `.env.mysql.local.example` como base para tu `.env`.
3. Ejecutar `php artisan migrate` y `php artisan db:seed`.

Referencia rápida: `docs/mysql-local.md`.

Scripts útiles ya preparados:

- `scripts/bootstrap-mysql-local.ps1` para crear la base si hace falta, migrar y sembrar el proyecto completo sobre MySQL local.
- `scripts/mysql-smoke-test.ps1` para migrar, sembrar y comprobar conteos sobre MySQL local.
- `scripts/serve-mysql-local.ps1` para arrancar Lexi en otro puerto usando MySQL sin tocar `.env`.
- `scripts/start-xampp-local.ps1` para levantar Apache + MariaDB de XAMPP y dejar `phpMyAdmin` operativo en `http://127.0.0.1/phpmyadmin/`.
- `scripts/stop-xampp-local.ps1` para bajar esos procesos de XAMPP cuando ya no los necesites.
- `scripts/show-current-db-config.ps1` para ver en consola la conexión efectiva que Laravel está usando ahora mismo.
- `scripts/switch-env-to-mysql-local.ps1` para convertir `.env` al flujo MySQL local con backup automático del estado SQLite.
- `scripts/switch-env-to-sqlite.ps1` para restaurar `.env` al flujo SQLite local.
- Ambos scripts aceptan parámetros `-DbUser`, `-DbPassword`, `-DbHost`, `-DbPort` y `-DbName`, útiles para XAMPP o MySQL local existente.
- `bootstrap-mysql-local.ps1`, `mysql-smoke-test.ps1` y `serve-mysql-local.ps1` también soportan `-EmptyPassword` para XAMPP con `root` sin password.

## ¿Hay que pasar todo a Blade?

Para la superficie principal, ya está prácticamente hecho.

No de golpe.

La estrategia correcta aquí es progresiva:

- mantener como archivos separados solo los assets y compatibilidades que no deciden lógica de servidor
- pasar a Blade las superficies que dependen de sesión, permisos o datos del servidor
- extraer después layouts compartidos para header, drawer y footer

Estado real ahora mismo:

- `index`, `contacto`, `info`, `privacidad`, `producto` y `terminos` ya salen por Blade.
- `app`, `biblioteca`, `carrito`, `ejercicios` y `progreso` ya salen por Blade.
- `login`, `registro`, `forgot-password` y `perfil` ya salen por Blade.
- Todo el panel `admin*` ya sale por Blade.
- Lo pendiente no es una migración grande a Blade, sino seguir reduciendo compatibilidad heredada y deuda de JS/layout.

Regla práctica para Lexi:

- páginas y formularios con auth, sesión, permisos o datos: Blade
- APIs: controladores + JSON
- estilos y comportamiento cliente: `public/style.css` y `public/js/script.js`
- mantener URLs `.html` en rutas no implica conservar archivos HTML estáticos en la raíz del proyecto

La prioridad técnica no es “todo a Blade” por sí solo, sino:

- una sola fuente de verdad para sesión y permisos
- una sola fuente de verdad para los assets
- menos duplicidad de layout
- menos lógica crítica en páginas estáticas

## Trabajo técnico recomendado a continuación

1. Seguir usando MySQL local como camino principal antes del despliegue a `RDS MySQL`.
2. Sustituir el bloque masivo de cards estáticas de biblioteca por render 100% desde base de datos.
3. Seguir conectando `app`, `ejercicios` y `progreso` a datos reales donde aún dependan de mockups del DOM.
4. Reducir más deuda de JavaScript heredado en `public/js/script.js`, separando mejor catálogo, progreso y carrito.
5. Preparar seeders/editorial workflow más amplio para catálogo real y administración de palabras.
6. Endurecer métricas y reporting de ejercicios, billing y AI sobre datos reales.

## Ejecución local

1. Instalar dependencias con `composer install`.
2. Asegurar que existe `database/database.sqlite`.
3. Configurar `.env`.
4. Ejecutar `php artisan migrate`.
5. Ejecutar `php artisan db:seed`.
6. Levantar el servidor con `php artisan serve`.

## Credenciales de desarrollo

- Admin inicial: `admin@lexi.app`
- Password: `password`

## Repositorio

- GitHub: https://github.com/AndresRuggieroGitHub/DIW6

## Autor

Andres Ruggiero
