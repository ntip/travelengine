# Routes & Route Configuration Documentation

## Overview

The TravelEngine routing system manages travel routes between airport codes (origin → destination pairs) and their associated configurations. The system consists of two models (`Route` and `RouteConfig`), two Livewire components for management, and database migrations for persistence.

---

## Database Schema

### Routes Table

```sql
CREATE TABLE routes (
    id UUID PRIMARY KEY,
    ori VARCHAR(3) NOT NULL,
    dst VARCHAR(3) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY unique_route (ori, dst)
);
```

**Details:**
- **id**: UUID (not auto-incrementing)
- **ori**: Origin airport code (3 characters, uppercase)
- **dst**: Destination airport code (3 characters, uppercase)
- **unique constraint**: No duplicate route pairs allowed (ori, dst)

### Routes Config Table

```sql
CREATE TABLE routes_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    route_id UUID NOT NULL,
    name VARCHAR(255) NOT NULL,
    value LONGTEXT NULLABLE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY unique_config_name (route_id, name),
    FOREIGN KEY (route_id) REFERENCES routes(id) ON DELETE CASCADE
);
```

**Details:**
- **id**: Auto-incrementing primary key
- **route_id**: UUID reference to routes table (cascade delete enabled)
- **name**: Configuration key (required, max 255 chars)
- **value**: Configuration value (optional, can be null)
- **unique constraint**: One config name per route

---

## Models

### Route Model

**Location:** `app/Models/Route.php`

```php
class Route extends Model
{
    use HasUuids;

    protected $fillable = ['ori', 'dst'];
    public $incrementing = false;
    protected $keyType = 'string';

    public function configs()
    {
        return $this->hasMany(RouteConfig::class, 'route_id');
    }
}
```

**Attributes:**
- `id` (string/UUID): Primary key
- `ori` (string): Origin airport code
- `dst` (string): Destination airport code
- `created_at` (timestamp)
- `updated_at` (timestamp)

**Relationships:**
- `configs()`: One-to-many relationship with `RouteConfig` model

**Methods:**
- None (standard Eloquent methods available)

**Supported Operations:**
- ✅ Create route with `ori` and `dst`
- ✅ Read/retrieve routes
- ✅ Update route data
- ✅ Delete route (cascades delete to all related configs)
- ✅ Query/filter routes by `ori` or `dst`
- ✅ Load related configs via `$route->configs()`

**Not Supported:**
- ❌ Batch operations with direct queries
- ❌ Custom query scopes (must use standard Eloquent methods)

---

### RouteConfig Model

**Location:** `app/Models/RouteConfig.php`

```php
class RouteConfig extends Model
{
    use HasFactory;

    protected $table = 'routes_config';

    protected $fillable = [
        'route_id',
        'name',
        'value',
    ];

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }
}
```

**Attributes:**
- `id` (int): Primary key
- `route_id` (UUID): Foreign key to Route
- `name` (string): Configuration key
- `value` (text/nullable): Configuration value
- `created_at` (timestamp)
- `updated_at` (timestamp)

**Relationships:**
- `route()`: Belongs-to relationship with `Route` model

**Supported Operations:**
- ✅ Create config for a route
- ✅ Update config values (including null)
- ✅ Delete config by ID and route_id validation
- ✅ Query configs by route_id or name
- ✅ UpdateOrCreate pattern for upsert operations

**Not Supported:**
- ❌ Bulk config creation without route association
- ❌ Updating name for existing config (unique constraint prevents this)
- ❌ Direct config creation without a valid route_id

---

## Livewire Components

### RoutesManager Component

**Location:** `app/Livewire/RoutesManager.php`

**Purpose:** Manage the creation, listing, and deletion of routes.

#### Properties

- `ori` (string): Origin airport code input
- `dst` (string): Destination airport code input
- `showModal` (boolean): Modal visibility state

#### Validation Rules

```php
[
    'ori' => ['required', 'string', 'size:3'],
    'dst' => ['required', 'string', 'size:3', 'different:ori'],
]
```

**Validation Details:**
- Both codes must be exactly 3 characters
- Both codes are required
- Origin and destination must be different
- No validation on airport code format (any 3-char string accepted)

#### Methods

| Method | Parameters | Description | Validation |
|--------|-----------|-------------|-----------|
| `openModal()` | None | Opens modal and resets form | Resets validation errors & clears inputs |
| `closeModal()` | None | Closes modal | None |
| `save()` | None | Creates/retrieves route and closes modal | Validates input, converts to uppercase |
| `delete()` | `string $id` | Deletes route by UUID | None (assumes valid ID) |
| `render()` | None | Renders view with ordered routes list | Fetches routes ordered by ori then dst |

#### Behavior

**Creating a Route:**
1. User inputs origin (e.g., "nyc") and destination (e.g., "lax")
2. Component validates inputs (3 chars, different from each other)
3. Converts to uppercase ("NYC" → "LAX")
4. Uses `firstOrCreate()` to avoid duplicates
5. Flashes session message: "Route NYC → LAX saved."
6. Closes modal

**Listing Routes:**
- Routes are sorted by origin, then destination
- Routes are passed to view for rendering

**Deleting a Route:**
- Removes route by UUID
- Cascades delete all associated RouteConfig entries

#### Supported Features

✅ Create routes with 3-character airport codes
✅ Validation prevents duplicate origin/destination pairs
✅ Modal interface for route creation
✅ Display list of all routes (sorted)
✅ Delete routes with confirmation capability
✅ Session flash messages for user feedback
✅ Uppercase normalization of airport codes

#### Not Supported

❌ Bulk route import/export
❌ Edit existing route codes (create new route instead)
❌ Route search/filtering (displays all routes)
❌ Route validation against real IATA codes
❌ Multi-language support
❌ Custom ordering options (fixed by ori, dst)
❌ Route notes or descriptions

---

### RouteConfigManager Component

**Location:** `app/Livewire/RouteConfigManager.php`

**Purpose:** Manage key-value configurations for a specific route.

#### Properties

- `route` (Route model): The associated route (injected via mount)
- `configs` (array): List of configurations for the route
- `name` (string): Configuration key input
- `value` (string/nullable): Configuration value input

#### Validation Rules

```php
[
    'name'  => ['required', 'string', 'max:255'],
    'value' => ['nullable', 'string'],
]
```

**Validation Details:**
- Configuration name is required and max 255 characters
- Configuration value is optional (can be null or empty string)
- No validation on value format or content type

#### Methods

| Method | Parameters | Description | Validation |
|--------|-----------|-------------|-----------|
| `mount()` | `Route $route` | Initializes component with route and loads configs | Route injection via Livewire model binding |
| `loadConfigs()` | None | Refreshes config list from database | Fetches and sorts by name |
| `addConfig()` | None | Creates or updates a config | Validates name and value |
| `deleteConfig()` | `int $id` | Removes config by ID | Validates route_id matches |
| `render()` | None | Renders the config manager view | None |

#### Behavior

**Adding a Configuration:**
1. User enters config name (e.g., "Max Passengers") and value (e.g., "150")
2. Component validates inputs
3. Uses `updateOrCreate()` to create new or update existing config with same name
4. Resets form inputs
5. Reloads configs list from database

**Listing Configurations:**
- All configs for the route displayed
- Sorted by configuration name alphabetically
- Converted to array format for rendering

**Deleting a Configuration:**
- Removes config by ID
- Validates route_id to prevent unauthorized deletion
- Reloads config list

**Lifecycle:**
- Component mounted with route injected
- Configs loaded automatically on mount
- Manual refresh after any add/delete operation

#### Supported Features

✅ Add configurations for a route
✅ Update existing configuration values (by name)
✅ Delete configurations safely (with route_id validation)
✅ Display all configs sorted by name
✅ Support null/empty values
✅ Automatic config reload after operations
✅ Model binding for route injection

#### Not Supported

❌ Bulk config import/export
❌ Config value type validation (JSON, numbers, etc.)
❌ Config history/versioning
❌ Config templates or presets
❌ Duplicate config detection by value
❌ Config search/filtering
❌ Config permissions/access control
❌ Rename existing config (must delete and recreate)
❌ Config descriptions or metadata
❌ Batch config operations

---

## Usage Examples

### Creating a Route via Livewire

```php
// RoutesManager component automatically handles this
// User enters:
// ori: "NYC"
// dst: "LAX"
// Component creates: Route::firstOrCreate(['ori' => 'NYC', 'dst' => 'LAX'])
```

### Adding Route Configuration

```php
// RouteConfigManager component injected with a Route
// User enters:
// name: "Max Passengers"
// value: "150"
// Component creates: RouteConfig::updateOrCreate(
//     ['route_id' => $route->id, 'name' => 'Max Passengers'],
//     ['value' => '150']
// )
```

### Programmatic Route Creation

```php
$route = Route::create([
    'ori' => 'JFK',
    'dst' => 'LHR',
]);

// Add configurations
$route->configs()->create([
    'name' => 'Daily Frequency',
    'value' => '3',
]);

// Retrieve all configs
$configs = $route->configs;
```

---

## Constraints & Limitations

### Database Constraints

1. **Route Uniqueness**: Only one route per (ori, dst) pair allowed
2. **Config Uniqueness**: Only one config per (route_id, name) pair allowed
3. **Cascade Delete**: Deleting a route deletes all its configs

### Model Constraints

1. Routes must have exactly 3-character codes
2. Routes require both origin and destination
3. Configs must belong to a valid route
4. No built-in validation for real airport codes

### Component Constraints

1. RoutesManager requires modal state management in view
2. RouteConfigManager requires route parameter in view
3. Both components use real-time database calls (no caching)
4. No built-in pagination for large route/config lists

---

## Related Views

- `resources/views/livewire/routes-manager.blade.php` - Rendering for RoutesManager
- `resources/views/livewire/route-config-manager.blade.php` - Rendering for RouteConfigManager

---

## Migration Files

- `database/migrations/2025_11_25_011540_create_routes_table.php` - Routes table
- `database/migrations/2025_11_25_023238_create_route_configs_table.php` - Route configs table

