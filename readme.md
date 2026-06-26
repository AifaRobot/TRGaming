# TRGaming — Plataforma de Evaluación de Talento Gamificada

<p align="center">
  <img src="readme/images/talent_recruiters.jpg" alt="Talent Recruiters" width="300"/>
</p>

TRGaming es una plataforma web que gamifica el proceso de evaluación de candidatos en selección de personal. En lugar de tests tradicionales, los postulantes juegan un videojuego interactivo exportado a WebAssembly y responden un cuestionario psicológico. Sus acciones dentro del juego quedan registradas y son analizadas por reclutadores a través de un panel de administración que genera reportes detallados sobre el perfil cognitivo de cada candidato.

---

## Tabla de contenidos

- [¿Cómo funciona?](#cómo-funciona)
- [Stack tecnológico](#stack-tecnológico)
- [Panel de administración](#panel-de-administración)
- [El juego](#el-juego)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Correr el proyecto](#correr-el-proyecto)
- [Estructura del proyecto](#estructura-del-proyecto)
- [API Reference](#api-reference)

---

## ¿Cómo funciona?

El flujo completo tiene dos actores: el **reclutador** (usa el panel admin) y el **candidato** (juega el juego).

```
Reclutador                          Candidato
────────────────                    ──────────────────────────────
1. Crea el candidato                2. Recibe link al juego
   en el panel admin                   e ingresa su DNI
3. Monitorea el progreso            4. Juega "La Esquiadora"
5. Abre el reporte                  5. Responde el cuestionario
   y lo exporta                        psicológico
```

**Dentro del juego**, cada acción del candidato (movimientos, errores, uso de ayudas, tiempo) se registra en tiempo real contra la API de Laravel. Al finalizar, el panel construye automáticamente un reporte con gráficos comparativos.

---

## Stack tecnológico

| Capa | Tecnología |
|------|-----------|
| Backend / Admin | Laravel 5.8 (PHP 7.4) |
| Base de datos | MySQL 5.7 |
| Servidor web | Nginx (Alpine) |
| Frontend admin | Vue.js 2.5, Bootstrap 4, Bulma CSS |
| Build | Laravel Mix (Webpack), SASS |
| Juego | Godot 3.x (GDScript) → HTML5/WebAssembly |
| Contenedores | Docker & Docker Compose |
| Email | Gmail SMTP (notificaciones) |

---

## Panel de administración

### Login

El acceso al panel es mediante usuario y contraseña.

![Login](readme/images/image-1.png)

---

### Dashboard

Vista general con contadores en tiempo real: candidatos totales, los que completaron la evaluación, los que están en progreso y los que todavía no jugaron.

![Dashboard](readme/images/image-2.png)

---

### Gestión de candidatos

Lista paginada y buscable de todos los candidatos. Muestra DNI, nombre, tipo de evaluación y si ya jugaron. Desde aquí se puede agregar, editar, eliminar y ver el reporte de cada uno.

![Lista de candidatos](readme/images/image-5.png)

Al crear o editar un candidato se puede asignar el **tipo de evaluación** (Individual o Liderazgo), datos personales, la empresa y posición a la que postula, nivel de estudios y la selectora asignada.

![Formulario candidato](readme/images/image-6.png)

---

### Gestión de selectoras

Administración de los reclutadores que están a cargo de los candidatos. Se pueden agregar, editar y eliminar.

![Lista de selectoras](readme/images/image-3.png)

![Editar selectora](readme/images/image-4.png)

---

### Reporte del candidato

El reporte tiene cuatro pestañas:

**Resumen** — datos del candidato y tiempos registrados en cada etapa del juego.

![Reporte - Resumen](readme/images/image-10.png)

**Agilidad Mental** — analiza la capacidad de comprensión de las reglas y la velocidad de respuesta. Muestra el valor del candidato comparado contra el mínimo y máximo del grupo.

![Reporte - Agilidad Mental](readme/images/image-7.png)

**Comprensión y Resultados** — evalúa el uso de ayudas (mostrar camino, tiempo extra) y la frecuencia de errores. Alto uso de ayudas puede indicar baja autonomía bajo presión.

![Reporte - Comprensión y Resultados](readme/images/image-9.png)

**Respuestas** — muestra todas las respuestas del cuestionario psicológico, organizadas por categorías (liderazgo, relaciones interpersonales, resolución de conflictos, etc.).

![Reporte - Respuestas](readme/images/image-8.png)

El reporte se puede **exportar** desde el panel.

---

## El juego

El juego está desarrollado en **Godot 3** y exportado a **HTML5/WebAssembly**, por lo que corre directo en el navegador sin instalar nada.

### Pantalla de ingreso

El candidato ingresa su DNI para que el juego lo identifique y empiece a registrar sus acciones.

![Ingreso de DNI](readme/images/image-17.png)

---

### Introducción narrativa

Una intro animada con estilo cómic presenta la historia: una esquiadora queda atrapada en una avalancha y debe encontrar el camino de salida.

![Intro - avalancha](readme/images/image-12.png)

![Intro - la misión](readme/images/image-15.png)

---

### Instrucciones

Antes de empezar, el juego explica las dos partes de la evaluación (La Esquiadora y La Rueda Mágica) y las reglas de cada una.

![Instrucciones generales](readme/images/image-16.png)

![Instrucciones La Esquiadora](readme/images/image-18.png)

![Instrucciones de controles](readme/images/image-19.png)

---

### Demo / Tutorial

Una versión sin límite de tiempo donde el candidato aprende a moverse con las flechas del teclado antes de enfrentar los niveles reales.

![Demo - inicio](readme/images/image-20.png)

![Demo - controles](readme/images/image-23.png)

---

### Gameplay — La Esquiadora

Un puzzle 2D de arriba hacia abajo sobre una grilla. La esquiadora debe llegar a la cabaña (bandera) siguiendo un **camino oculto**. Si el candidato intenta moverse por una casilla incorrecta, vuelve al inicio. Hay un cronómetro visible y dos botones de ayuda disponibles:

- **Ver el camino descubierto hasta ahora**
- **Agregar 1 minuto al tiempo**

Todo esto queda registrado.

![Gameplay](readme/images/image-22.png)

![Nivel en progreso](readme/images/image-24.png)

![Nivel completado](readme/images/image-21.png)

---

### Cuestionario psicológico

Al terminar los niveles de La Esquiadora, el candidato responde un cuestionario con situaciones laborales reales. Cada pregunta tiene tres opciones y evalúa categorías como liderazgo, relaciones interpersonales, toma de decisiones y resolución de conflictos.

![Cuestionario - ejemplo 1](readme/images/image-25.png)

![Cuestionario - ejemplo 2](readme/images/image-26.png)

![Cuestionario - ejemplo 3](readme/images/image-28.png)

![Cuestionario - ejemplo 4](readme/images/image-31.png)

---

### La Rueda Mágica

Segunda parte del juego, accesible desde la cabaña al completar La Esquiadora.

![La Rueda Mágica](readme/images/image-33.png)

---

## Instalación

### Prerrequisitos

- [Docker](https://www.docker.com/) y Docker Compose
- Git

### Pasos

```bash
# 1. Clonar el repositorio
git clone <url-del-repo>
cd TRGaming

# 2. Copiar el archivo de entorno
cp application/.env.example application/.env

# 3. Configurar las variables de entorno (ver sección Configuración)
# Editar application/.env

# 4. Construir y levantar los contenedores
docker-compose up -d --build

# 5. Instalar dependencias PHP
docker exec trgaming_app composer install

# 6. Generar la clave de la aplicación
docker exec trgaming_app php artisan key:generate

# 7. Ejecutar las migraciones
docker exec trgaming_app php artisan migrate

# 8. Instalar dependencias JS y compilar assets
docker exec trgaming_app npm install
docker exec trgaming_app npm run production
```

### Accesos una vez levantado

| Servicio | URL |
|---------|-----|
| Panel de administración | http://localhost:8080/login |
| Juego (candidatos) | http://localhost:8080/game |
| MySQL | localhost:3306 |

---

## Configuración

Las variables clave en `application/.env`:

```env
# Base de datos
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=trgaming
DB_USERNAME=trgaming
DB_PASSWORD=secret

# URL del backend (usada por el juego Godot para comunicarse con la API)
APP_URL=http://localhost:8080

# Email (notificaciones a reclutadores)
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-cuenta@gmail.com
MAIL_PASSWORD=tu-app-password
MAIL_ENCRYPTION=tls
```

---

## Correr el proyecto

### Modo desarrollo

```bash
# Levantar los contenedores
docker-compose up -d

# Compilar assets con watch (recarga automática)
docker exec trgaming_app npm run watch
```

### Detener los contenedores

```bash
docker-compose down
```

### Sin Docker (desarrollo local)

```bash
cd application

# Instalar dependencias
composer install
npm install

# Configurar .env con datos de tu MySQL local
cp .env.example .env
php artisan key:generate

# Migrar la base de datos
php artisan migrate

# Compilar assets
npm run dev

# Levantar el servidor
php artisan serve
```

---

## Estructura del proyecto

```
TRGaming/
├── application/              # Backend Laravel + panel de administración
│   ├── app/
│   │   ├── Http/Controllers/ # WorkerController, LoginController, HomeController...
│   │   └── *.php             # Modelos: Workers, Registry, Selectora, User
│   ├── database/migrations/  # Esquema de base de datos
│   ├── routes/
│   │   ├── web.php           # Rutas del panel de administración
│   │   └── api.php           # API REST consumida por el juego
│   ├── resources/
│   │   ├── views/            # Plantillas Blade (admin panel)
│   │   └── js/               # Componentes Vue.js
│   ├── public/game/          # Juego exportado (HTML + JS + WASM)
│   ├── docker-compose.yml
│   └── Dockerfile
│
├── godot-game-proyect/       # Proyecto fuente del juego en Godot 3
│   ├── scenes/               # Escenas del juego (.tscn)
│   │   ├── game/             # Niveles principales
│   │   ├── questions/        # Cuestionario
│   │   └── player/           # Personaje
│   ├── Global.gd             # Estado global (música, UI, niveles)
│   ├── Events.gd             # Comunicación HTTP con la API
│   └── questions.json        # Banco de preguntas del cuestionario
│
└── readme/images/            # Capturas de pantalla para documentación
```

---

## API Reference

El juego Godot se comunica con estas rutas de la API:

| Método | Ruta | Descripción |
|--------|------|-------------|
| `GET` | `/api/workers/{dni}` | Obtiene datos del candidato |
| `POST` | `/api/workers` | Crea un candidato |
| `PUT` | `/api/workers/{dni}` | Actualiza datos del candidato |
| `POST` | `/api/registry/{dni}` | Registra un evento del juego |
| `GET` | `/api/workers/{dni}/report` | Genera el reporte del candidato |
| `GET` | `/api/workers/metrics` | Métricas globales (min/max) |

Los eventos que el juego registra incluyen: inicio de sesión, inicio de nivel, cada movimiento exitoso, cada error, uso de ayuda, tiempo por nivel y finalización del cuestionario.
