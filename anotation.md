app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   └── TenantController.php
│   │   └── Tenant/
│   │       └── DashboardController.php
│   ├── Middleware/
│   │   └── InitializeTenancyBySubdomain.php
│
├── Services/
│   ├── Tenant/
│   │   ├── CreateTenantService.php
│   │   └── DeleteTenantService.php
│
├── Models/
│   ├── Tenant.php
│   └── Domain.php
│
database/
├── migrations/
│   └── master/
│       └── create_tenants_table.php
│
├── tenant/
│   └── migrations/
│       └── create_patients_table.php
