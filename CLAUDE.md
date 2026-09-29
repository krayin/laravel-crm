# Krayin CRM - Laravel Fork

## Información del Proyecto

**Proyecto Base:** Krayin CRM (https://krayincrm.com) - CRM open-source basado en Laravel
**Versión:** 2.2 (fork)
**Stack Backend:** Laravel 12 + PHP 8.3
**Stack Frontend:** Vue.js (vía packages Webkul/Admin)

## Rama Actual: main

Ramas disponibles:
- `main` - rama principal de producción
- `release` - rama para preparar releases
- `dev` - rama de desarrollo

**Workflow de cambios:** dev → release → main

## Estructura del Proyecto

```
/packages/Webkul/          # Módulos del CRM
├── Admin/             # Panel de administración (Vue.js)
├── Core/             # Funcionalidad central
├── Lead/             # Gestión de leads/oportunidades
├── Quote/            # Cotizaciones
├── Contact/          # Contactos (Persons, Organizations)
├── Activity/        # Actividades (LLamadas, Reuniones, Tareas)
├── Email/            # Gestión de emails
├── Product/          # Productos
├── Tag/              # Etiquetas
├── User/             # Gestión de usuarios/roles
├── Warehouse/        # Almacenes
├── Automation/       # Workflows y Webhooks
├── Attribute/        # Atributos personalizados
├── DataGrid/         # Tablas de datos
├── DataTransfer/     # Import/Export
├── EmailTemplate/    # Plantillas de email
├── Marketing/       # Campañas y eventos
├── WebForm/         # Formularios web
└── Installer/       # Instalador
```

## Entidades Principales

- **Leads:** Oportunidades de venta con pipeline stages
- **Quotes:** Cotizaciones enviadas a clientes
- **Contacts:** Personas y Organizaciones
- **Activities:** Llamadas, reuniones, tareas
- **Products:** Catálogo de productos
- **Emails:** Bandejas inbox/outbox/sent/draft

## Configuración

- **Auth:** Laravel Sanctum (token-based)
- **DB:** MySQL 8.0+
- **Paquetes importantes:**
  - barryvdh/laravel-dompdf (PDF)
  - maatwebsite/excel (Excel import/export)
  - webklex/laravel-imap ( emails IMAP)
  - konekt/concord (modulos)
  - prettus/l5-repository (Repository pattern)

## Credenciales Demo

```
admin@example.com / admin123
```

## Notas para Desarrollo

- Arquitectura modular via packages Webkul/*
- Cada paquete es un módulo Laravel independiente
- Rutas definidas en config/menu.php de cada paquete
- Modelos en src/Models/
- Controladores en src/Http/Controllers/
- Migrationes NO incluidas en paquetes (están en database/)