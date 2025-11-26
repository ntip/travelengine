# Jobs System Documentation

## Overview
This document outlines the jobs system used in the application, detailing the models, migrations, and components involved.

## Models
### RouteJob
- **Namespace**: `App\Models`
- **Fillable Attributes**:
  - `route_id`: The ID of the associated route.
  - `job_date`: The date for the job.
  - `status`: The current status of the job.
  - `archived`: Indicates if the job is archived.
  - `next_run_at`: The next scheduled run time for the job.
  - `last_hydrated_at`: The last time the job was updated.

## Livewire Components
### RouteJobsManager
- **Namespace**: `App\Livewire`
- **Uses**: 
  - `Route` and `RouteJob` models.
  - Implements pagination with `WithPagination`.
- **Mount Method**: Initializes the component with a specific route.

## Migrations
1. **0001_01_01_000002_create_jobs_table.php**:
   - Creates a `jobs` table with fields:
     - `id`: Auto-incrementing primary key.
     - `queue`: The name of the queue.
     - `payload`: The job's payload.
     - `attempts`: Number of attempts made to execute the job.
     - `reserved_at`: Timestamp for when the job was reserved.
     - `available_at`: Timestamp for when the job becomes available.

2. **2025_11_25_093906_create_route_jobs_table.php**:
   - Creates a `route_jobs` table with fields:
     - `id`: Auto-incrementing primary key.
     - `route_id`: Foreign key referencing the `routes` table.
     - `job_date`: The date this job is responsible for.
     - `status`: The status of the job (e.g., pending, running, success, failed, archived).

3. **2025_11_25_094523_create_route_jobs_table.php**:
   - Similar to the previous migration, but uses a UUID for the `route_id`.
