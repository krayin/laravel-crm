# Memoria Técnica - Krayin CRM Fork

## 1. Resumen del Proyecto

**Nombre:** Krayin CRM (Fork)
**Tipo:** CRM (Customer Relationship Management)
**Código Base:** krayin/laravel-crm
**Licencia:** MIT
**Versión actual del fork:** 2.2

## 2. Tecnología Principal

| Componente | Tecnología | Versión |
|------------|------------|---------|
| Framework PHP | Laravel | ^12.0 |
| PHP | PHP | ^8.3 |
| Frontend | Vue.js | (incluido en Admin package) |
| Autenticación | Laravel Sanctum | ^4.0 |
| Base de datos | MySQL | 8.0+ |

## 3. Arquitectura Modular

El proyecto usa **Konekt Concord** para modularidad. Cada dominio de negocio es un paquete PHP separado bajo `packages/Webkul/`:

### Paquetes Core
- **Core:** Configuraciones centrales, países, monedas, idiomas
- **Admin:** Panel de administración Vue.js (rutas, controladores, vistas)
- **User:** Usuarios, grupos, roles, permisos (ACL)
- **DataGrid:** Componente reutilizable para tablas de datos

### Paquetes de Negocio
- **Lead:** Gestión de leads y pipeline de ventas
- **Quote:** Generación y envío de cotizaciones
- **Contact:** Personas y Organizaciones
- **Activity:** Seguimiento de actividades (calls, meetings, tasks)
- **Product:** Catálogo de productos e inventario
- **Email:** Cliente de email integrado (IMAP)

### Paquetes de Automatización
- **Automation:** Workflows automáticos y webhooks
- **Marketing:** Eventos, campañas, triggers
- **EmailTemplate:** Plantillas de email

### Paquetes de Utilidad
- **Attribute:** Sistema de atributos personalizados
- **Tag:** Sistema de etiquetado
- **Warehouse:** Gestión de almacenes
- **DataTransfer:** Importación/exportación CSV/Excel
- **WebForm:** Formularios públicos para captura de leads
- **Installer:** Script de instalación

## 4. Entidades de Dominio Principales

### Leads (Oportunidades)
- Pipeline configurable
- Stages: New, Contacted, Qualified, Proposal, Won/Lost
- Fuentes: Web, Referral, Campaign, etc.
- Tipos: Commercial, Enterprise
- Seguimiento de actividades relacionadas

### Quotes (Cotizaciones)
- Line items con productos y cantidades
- Descuentos por línea y total
- Estados: Draft, Sent, Viewed, Accepted, Rejected
- PDF generation

### Contacts
- **Person:** Datos de contacto individual (email, phone, notes)
- **Organization:** Empresas (dirección, website, employees)

### Products
- SKU, nombre, descripción
- Inventario por warehouse
- Price lists

### Activities
- **Call:** Llamadas entrantes/salientes
- **Meeting:** Reuniones programadas
- **Task:** Tareas pendientes
- Participantes relacionados

## 5. Estructura de Archivos Clave

```
/app                     # App principal (_kernel.php, providers)
/bootstrap               # Bootstrapping Laravel
/packages/Webkul/*/src   # Código fuente de módulos
    ├── Config/          # Configuraciones (menu.php, acl.php)
    ├── Http/           # Controllers, Requests, Resources
    ├── Models/         # Modelos Eloquent
    ├── Repositories/   # Repository pattern
    ├── Resources/     # Vistas Vue, assets
    └── Mail/          # Mailable classes
/database               # Migraciones y seeders
/routes                 # Archivos de rutas
/config                 # Configuraciones globales
/resources              # Assets del frontend
/storage               # Logs, cache, uploads
```

## 6. Dependencias Externas Relevantes

- `barryvdh/laravel-dompdf` - generación PDFs
- `maatwebsite/excel` - importación/exportación Excel
- `smalot/pdfparser` - parsing PDFs incoming
- `webklex/laravel-imap` - acceso IMAP para emails
- `diglactic/laravel-breadcrumbs` - breadcrumb navigation
- `mpdf/mpdf` - motor PDF alternativo
- `prettus/l5-repository` - repository pattern

## 7. Configuración de Ambiente

Variables de entorno clave en `.env`:
- `APP_URL`
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`
- `SANCTUM_STATEFUL_DOMAINS`

Instalación inicial:
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan krayin-crm:install
php artisan serve
```

## 8. Acceso al Sistema

**Panel Admin:** `{APP_URL}/admin`
**Credenciales por defecto:** admin@example.com / admin123

## 9. Estado del Proyecto

- CRM funcional con todas las features base
- Necesita evaluación para personalización/transporte/logística
- Posible integración con sistemas externos (API)