# Sistema de Gestión Escolar

Un sistema web desarrollado en **PHP**, **Bootstrap** y **MySQL** para la administración académica de un centro educativo.  
Permite gestionar estudiantes, docentes, materias, calificaciones, asistencia, comportamiento y más, con control de roles (Administrador, Docente y Estudiante).

---

## 🚀 Características principales

- **Autenticación y roles de usuario**
  - Administrador: acceso completo a todos los módulos.
  - Docente: gestión de calificaciones, asistencia y comportamiento.
  - Estudiante: acceso solo a sus propios datos (calificaciones, comportamiento, asistencia).

- **Módulos incluidos**
  - Menú principal con acceso rápido.
  - Configuración institucional (nombre del centro, director, dirección, distrito, teléfono, correo).
  - Gestión de **Niveles, Secciones y Grados**.
  - Registro y administración de **Materias**.
  - Inscripción de **Estudiantes y Docentes**.
  - Control de **Usuarios y Roles**.
  - Registro de **Calificaciones**.
  - Control de **Asistencia**.
  - Registro y reporte de **Comportamiento**.
  - **Chat interno** para comunicación.

- **Reportes**
  - Reporte de asistencia por grado y fecha.
  - Reporte de comportamiento por grado y fecha.
  - Historial individual para estudiantes.

---

## 🛠️ Tecnologías utilizadas

- **Backend:** PHP 8+
- **Frontend:** Bootstrap 5, Bootstrap Icons
- **Base de datos:** MySQL
- **Servidor local recomendado:** XAMPP / Laragon

---

## 📂 Estructura del proyecto
GestioEscolar/
│
├── principal.php              # Página principal con control de roles
├── conexion/                  # Configuración de conexión a la BD
│   └── conexion.php
├── Configuracion/             # Configuración institucional
├── Niveles/                   # Gestión de niveles
├── Secciones/                 # Gestión de secciones
├── Grados/                    # Gestión de grados
├── Materias/                  # Gestión de materias
├── Usuarios/                  # Gestión de usuarios
├── Roles/                     # Gestión de roles
├── Docentes/                  # Gestión de docentes
├── Estudiantes/               # Gestión de estudiantes
├── Calificaciones/            # Registro de calificaciones
├── Asistencia/                # Control de asistencia
├── Comportamiento/            # Registro y reportes de comportamiento
└── Chat/                      # Chat interno

