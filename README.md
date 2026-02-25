# Sistema de Gestión Escolar — I.E.P. Jesús y María

Sistema web para la gestión académica y de pagos del colegio I.E.P. Jesús y María, desarrollado con Laravel 12.

---

## Requisitos previos

Asegúrate de tener instalado lo siguiente antes de continuar:

| Herramienta | Versión mínima |
|-------------|----------------|
| PHP         | 8.2            |
| Composer    | 2.x            |
| Node.js     | 18.x           |
| npm         | 9.x            |
| MySQL       | 8.0            |

---

## Instalación

### 1. Clonar el repositorio

```bash
git clone <url-del-repositorio> sistema-colegio
cd sistema-colegio
```

### 2. Instalar dependencias PHP

```bash
composer install
```

### 3. Instalar dependencias JavaScript

```bash
npm install
```

### 4. Configurar el entorno

Copia el archivo de ejemplo y edítalo:

```bash
cp .env.example .env
```

Abre `.env` y configura la conexión a la base de datos:

```env
APP_NAME="I.E.P. Jesús y María"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_colegio
DB_USERNAME=root
DB_PASSWORD=tu_contraseña
```

### 5. Generar la clave de la aplicación

```bash
php artisan key:generate
```

### 6. Crear la base de datos

Crea la base de datos `sistema_colegio` en MySQL (o el nombre que configuraste en `.env`), luego ejecuta las migraciones y seeders:

```bash
php artisan migrate:fresh --seed
```

Esto crea todas las tablas y carga los datos iniciales:
- Usuario administrador
- Tipos de pago (Matrícula, Mensualidad)
- Meses del año escolar (Marzo–Diciembre)
- Año académico actual con niveles y grados del sistema educativo peruano

### 7. Crear el enlace de almacenamiento

Necesario para mostrar las fotos de comprobantes de pago:

```bash
php artisan storage:link
```

### 8. Compilar los assets

```bash
npm run build
```

---

## Ejecutar el servidor de desarrollo

```bash
php artisan serve
```

La aplicación estará disponible en [http://localhost:8000](http://localhost:8000).

Para desarrollo con recarga automática de estilos:

```bash
# En una terminal:
php artisan serve

# En otra terminal:
npm run dev
```

---

## Credenciales por defecto

| Campo      | Valor                        |
|------------|------------------------------|
| Email      | admin@jesusymaria.edu.pe     |
| Contraseña | admin123                     |

> **Importante:** Cambia la contraseña después del primer inicio de sesión.

---

## Tareas programadas (Scheduler)

El sistema ejecuta tareas automáticas que requieren el scheduler de Laravel activo:

| Tarea | Frecuencia | Descripción |
|-------|-----------|-------------|
| `mensualidades:generar` | 1ro de cada mes, 00:01 | Genera las cuotas mensuales de todos los alumnos matriculados |
| `app:nuevo-anio` | 1 de enero, 00:05 GMT-5 | Crea y activa el nuevo año académico con sus niveles y grados |

### En desarrollo (Windows)

```bash
php artisan schedule:work
```

### En producción (Linux/cPanel)

Agrega esta línea al crontab del servidor:

```cron
* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

---

## Primeros pasos tras instalar

1. **Configurar precios** — Ve a `Configuración → Nivel Educativo` y ajusta el precio mensual de cada nivel (se inicializa en S/ 0.00).
2. **Crear secciones** — Ve a `Configuración → Secciones` y añade las secciones del colegio (ej: A, B, C) para cada grado.
3. **Registrar alumnos** — Ve a `Alumnos → Lista de Alumnos` para agregar alumnos.
4. **Matricular alumnos** — Ve a `Pagos → Matricular Alumno` para registrar la matrícula del año.

---

## Estructura del sistema educativo peruano (precargada)

| Nivel | Grados |
|-------|--------|
| Inicial | 3 Años, 4 Años, 5 Años |
| Primaria | 1°, 2°, 3°, 4°, 5°, 6° |
| Secundaria | 1°, 2°, 3°, 4°, 5° |

---

## Comandos útiles

```bash
# Resetear la base de datos completamente
php artisan migrate:fresh --seed

# Limpiar caché de vistas
php artisan view:clear

# Compilar assets para producción
npm run build

# Ver todas las rutas del sistema
php artisan route:list

# Crear manualmente el año académico actual
php artisan app:nuevo-anio
```

---

## Stack tecnológico

- **Backend:** Laravel 12, PHP 8.2
- **Frontend:** Tailwind CSS v3, Alpine.js, Font Awesome 6.6.0
- **Base de datos:** MySQL 8.0
- **PDF:** barryvdh/laravel-dompdf v3.1.1
- **Gráficos:** Chart.js 4.4.0
