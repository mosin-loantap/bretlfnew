# BRE for Financial Products

A Laravel-based Business Rules Engine (BRE) for financial products that allows multiple NBFC partners to define and apply rules dynamically.

## 🎯 Domain Context

This is an API-first Laravel application designed for:
- Multiple NBFC (Non-Banking Financial Company) partners
- Dynamic rule definition and management for financial products
- Database-driven configuration of products, rules, conditions, variables, and actions
- Real-time loan application evaluation against business rules

## 📊 Database Schema

### Core Tables

#### `partners`
Stores NBFC partner information
- `partner_id` (auto-increment primary key)
- `NBFCName`
- `RegistrationNumber`
- `RBI_LicenseType`
- `Date_of_incorporation`
- `BusinessLimit`
- `created_by`, `updated_by` (audit fields)

#### `products`
Defines loan products (Personal Loan, Business Loan, etc.)
- `product_id` (auto-increment primary key)
- `name`
- `description`
- `created_by`, `updated_by`

#### `rules`
Business rules linked to products
- `rule_id` (auto-increment primary key)
- `product_id` (foreign key to products)
- `name`
- `priority`
- `status`
- `created_by`, `updated_by`

#### `rule_conditions`
Stores conditions within a rule
- `condition_id` (auto-increment primary key)
- `rule_id` (foreign key to rules)
- `variable_id` (foreign key to variables)
- `operator`
- `value`
- `created_by`, `updated_by`

#### `variables`
Dynamic fields used in rules (salary, age, credit score, etc.)
- `variable_id` (auto-increment primary key)
- `name`
- `data_type`
- `description`
- `created_by`, `updated_by`

#### `actions`
Defines what happens when a rule matches (approve, reject, refer, etc.)
- `action_id` (auto-increment primary key)
- `rule_id` (foreign key to rules)
- `action_type`
- `parameters`
- `created_by`, `updated_by`

### Relationships
- Partner `hasMany` Products
- Product `hasMany` Rules
- Rule `hasMany` RuleConditions and `hasMany` Actions
- RuleCondition `belongsTo` Variable

## 🚀 Setup Instructions

### Prerequisites
- PHP 8.1+
- Composer
- Laravel 11.x
- SQLite/MySQL database

### Installation

1. Clone the repository
```bash
git clone <repository-url>
cd breltf
```

2. Install dependencies
```bash
composer install
npm install
```

3. Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

4. Database setup
```bash
php artisan migrate:fresh --seed
```

## 📋 Current Implementation Status

### ✅ Completed
- [x] Database schema design and migrations
- [x] Eloquent models with relationships
- [x] API controllers with validation
- [x] Demo seeders for all tables
- [x] Basic CRUD operations

### 🔄 In Progress
- [ ] Rule evaluation engine
- [ ] API authentication (JWT/Laravel Passport)
- [ ] Request/Response logging middleware
- [ ] Admin panel (Filament/Bootstrap UI)
- [ ] Bulk demo data with Faker factories

## 🛠 Models

### Core Models
- `Partner` - NBFC partner management
- `Product` - Financial product definitions
- `Rule` - Business rule configuration
- `RuleCondition` - Rule condition logic
- `Variable` - Dynamic field definitions
- `Action` - Rule outcome actions

## 🔗 API Endpoints

### Planned API Structure
```
/api/partners     - Manage NBFC partners
/api/products     - Manage loan products
/api/rules        - Manage business rules
/api/conditions   - Manage rule conditions
/api/variables    - Manage dynamic fields
/api/actions      - Manage rule actions
/api/evaluate     - Evaluate loan applications
```

## 📝 Seeders

Demo data includes:
- **PartnerSeeder**: Demo NBFC partner (Loantap)
- **ProductSeeder**: Personal Loan, Business Loan products
- **VariableSeeder**: Variables like salary, age
- **RuleSeeder**: Sample rule for Personal Loan
- **RuleConditionSeeder**: Conditions (salary ≥ 30000, age ≥ 21)
- **ActionSeeder**: Approval action with max_amount = 500000

Run seeders:
```bash
php artisan migrate:fresh --seed
```

## 🏗 Architecture

### Key Features
- **API-First Design**: RESTful APIs for all operations
- **Audit Trail**: All tables include `created_by`, `updated_by` tracking
- **Foreign Key Relationships**: Proper database integrity
- **Validation**: Request validation in controllers
- **Seeding**: Demo data for development and testing

### Tech Stack
- **Backend**: Laravel 11.x
- **Database**: SQLite (development), MySQL (production)
- **Authentication**: Laravel Passport (planned)
- **Admin Panel**: Filament (planned)
- **Frontend**: API-first (can be consumed by any frontend)

## 🔧 Development Commands

```bash
# Run migrations
php artisan migrate

# Fresh migration with seeders
php artisan migrate:fresh --seed

# Create new migration
php artisan make:migration create_table_name

# Create new model
php artisan make:model ModelName

# Create new controller
php artisan make:controller ControllerName

# Create new seeder
php artisan make:seeder SeederName

# Run specific seeder
php artisan db:seed --class=SeederName
```

## 📁 Project Structure

```
app/
├── Http/Controllers/    # API Controllers
├── Models/             # Eloquent Models
├── Services/           # Business Logic Services
└── Providers/          # Service Providers

database/
├── migrations/         # Database Migrations
├── seeders/           # Data Seeders
└── factories/         # Model Factories

config/                # Configuration files
routes/
├── api.php           # API Routes
└── web.php           # Web Routes
```

## 🎯 Next Development Steps

1. **Rule Evaluation Engine**
   - Implement dynamic rule evaluation logic
   - Create loan application evaluation API
   - Add rule priority and conflict resolution

2. **Authentication & Security**
   - Implement JWT/Laravel Passport
   - Add API rate limiting
   - Partner-specific access control

3. **Logging & Audit**
   - Request/Response logging middleware
   - Database audit trail
   - Performance monitoring

4. **Admin Interface**
   - Filament admin panel for rule management
   - Rule builder UI
   - Analytics dashboard

5. **Testing & Documentation**
   - Unit tests for rule evaluation
   - API documentation (OpenAPI/Swagger)
   - Performance testing

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

---

**Repository**: bretlfnew  
**Owner**: mosin-loantap  
**Current Branch**: main  
**Last Updated**: August 20, 2025
