# Project Specification - Krayin CRM Fork

## 1. Basic Information

| Field | Value |
|-------|-------|
| Project Name | Krayin CRM Fork |
| Type | CRM Web Application |
| Original License | MIT |
| Current Branch | main |
| PHP Version | 8.3+ |
| Laravel Version | 12.0 |

## 2. Core Features

### 2.1 Lead Management
- Create, edit, delete leads
- Pipeline stages configuration (New → Contacted → Qualified → Proposal → Won/Lost)
- Lead scoring
- Lead assignment to users
- Activity logging per lead

### 2.2 Quote Management
- Quote creation from products
- Multiple line items
- Discount support (per item / global)
- PDF export
- Quote versioning
- Email sending

### 2.3 Contact Management
- Person records (individual contacts)
- Organization records (companies)
- Contact info: email, phone, address
- Notes and history

### 2.4 Activity Tracking
- Call logs (incoming/outgoing)
- Meeting schedules
- Task management
- Participant association

### 2.5 Email Integration
- IMAP integration (receive emails)
- Send emails via SMTP
- Email templates
- Email tracking (opened/clicks)

### 2.6 Product Catalog
- Product database
- Inventory management
- Multiple warehouses
- Price lists

### 2.7 User & Permission Management
- User accounts
- Roles and permissions (ACL)
- User groups
- Activity logging

### 2.8 Automation
- Workflows (trigger → action)
- Webhooks
- Email campaigns
- Marketing events
- Data import/export

## 3. Technical Architecture

### 3.1 Module System
```
Framework: Konekt Concord (Laravel modularity)
├── Each module = package under packages/Webkul/
├── Self-contained: Models, Controllers, Views, Config
├── Dependency injection via service providers
```

### 3.2 Database Schema
```
Main tables (in database/migrations/):
├── users, roles, permissions
├── leads, lead_stages, lead_pipelines
├── quotes, quote_items
├── persons, organizations
├── activities, activity_participants
├── products, product_inventories
├── emails, email_attachments
├── tags
├── attributes
├── warehouses
├── workflows, webhooks
├── campaigns, events
├── data_imports, data_exports
```

### 3.3 API Structure
- RESTful controllers
- Laravel Sanctum authentication
- Token-based API access
- Resource transformers (Fractal)

### 3.4 Frontend
- Vue.js 3 (via Vite)
- Blade templates for server rendering
- DataGrid component for tables
- AJAX for dynamic content

## 4. User Interface Modules

| Menu Item | Module | Description |
|---------|-------|-------------|
| Dashboard | Admin | Main dashboard |
| Leads | Lead | Lead pipeline |
| Quotes | Quote | Quote management |
| Mail | Email | Email client |
| Activities | Activity | Calls, meetings, tasks |
| Contacts | Contact | Persons & orgs |
| Products | Product | Product catalog |
| Settings → Users | User | User management |
| Settings → Roles | User | Role & permission |
| Settings → Pipelines | Lead | Pipeline config |
| Settings → Attributes | Attribute | Custom fields |
| Settings → Warehouses | Warehouse | Warehouse mgmt |
| Settings → Workflows | Automation | Automation rules |
| Settings → Webhooks | Automation | Webhook config |
| Configuration | Admin | System config |

## 5. External Integrations

| Service | Package | Purpose |
|---------|---------|---------|
| SMTP | swiftmailer | Email sending |
| IMAP | webklex/laravel-imap | Email receiving |
| PDF | dompdf/mpdf | Document generation |
| Excel | maatwebsite/excel | Spreadsheet I/O |

## 6. Development Standards

- **Code Style:** Laravel Pint (PSR-12)
- **Testing:** Pest PHP
- **Error Handling:** Laravel Ignition (debug) + custom prod handler
- **Logging:** Laravel Log (stack driver)

## 7. Deployment Requirements

- PHP 8.3+
- MySQL 8.0+
- Composer 2.5+
- Apache 2 or Nginx
- RAM: 3GB minimum

## 8. Known Customizations Applied

*To be documented by developer*

---

## 9. Future Considerations

- [ ] Evaluate for specific industry customization
- [ ] API documentation
- [ ] Third-party CRM integrations
- [ ] Mobile responsiveness audit
- [ ] Performance optimization
- [ ] Custom reporting needs