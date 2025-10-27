## About Client Management

A comprehensive client management system with duplicate detection, built with Laravel backend and Vue.js frontend.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Usage](#usage)
5. [Features](#features)
6. [API Endpoints](#api-endpoints)
7. [Testing](#testing)
8. [Technologies Used](#technologies-used)
9. [Contributing](#contributing)

## Prerequisites

Before you begin, ensure you have the following installed on your local machine:

- [PHP](https://www.php.net/) (v8.1 or higher)
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) (v14.x or higher)
- [Vue CLI](https://cli.vuejs.org/) (v2.x)
- [MySQL](https://www.mysql.com/) (or any other supported database)

## Installation

### Clone the repository:

```bash
git clone https://github.com/kultosh/client-management.git
cd client-management
```

### Install the dependencies :

#### For the Laravel Backend:

```bash
cd backend
composer install
```
#### For the Vue Frontend:
```bash
cd frontend
npm install
```

## Configuration

### For the Laravel Backend:
1. Create .env file copying .env.exmaple
2. Link your database in .env file
3. Generate the application key:
```bash
php artisan key:generate
```
4. Run Migrations:
```bash
php artisan migrate
```
5. Seed the database seeder to get dummy clients data:
```bash
php artisan db:seed
```

### For the Vue Frontend:
#### .env Setup
Add the following variables to your frontend/.env file:
```bash
VUE_APP_ROOT_API="http://localhost:8000/api"
```

## Usage

### Running the Application

#### For the backend:
The backend will be available at http://localhost:8000
```bash
cd backend
php artisan serve
```
#### Important Notes:
For queue processing (required for CSV imports):
```bash
cd backend
php artisan queue:work
```

#### For the frontend:
The frontend will be available at http://localhost:8080
```bash
cd frontend
npm run serve
```
## Features
- **Client Management** - Create, read, update, and delete client records
- **Duplicate Detection** - Automatic detection of duplicate clients based on company name, email, and phone number
- **CSV Import/Export** - Bulk import clients via CSV with background queue processing
- **Search & Filter** - Advanced search and filtering capabilities
- **Pagination** - Efficient handling of large datasets
- **Real-time Import Status** - Track background import job progress

## API Endpoints
| HTTP Method                     | Endpoint                               | Description                                                                      |
| --------------------------------|----------------------------------------|----------------------------------------------------------------------------------|
| `GET`                           | `/clients`                             | Fetch all clients with filtering options                                         |
| `POST`                          | `/clients/import`                      | Import clients from CSV file                                                     |
| `GET`                           | `/clients/imports/{importId}/status`   | Check import job status                                                          |
| `GET`                           | `/clients/export`                      | Export clients to CSV                                                            |
| `PUT`                           | `/clients/{id}`                        | Update client data                                                               |
| `DELETE`                        | `/clients/{id}`                        | Delete client record                                                             |

[![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/24958376-cd2208e5-10b0-4279-89a6-4a5284b8b814?action=collection%2Ffork&collection-url=entityId%3D24958376-cd2208e5-10b0-4279-89a6-4a5284b8b814%26entityType%3Dcollection%26workspaceId%3Daca9472c-0c3e-4f80-823d-97a34ef95bfd)

*Click the button above to import and test the API collection directly in Postman.*

## Testing
```bash
cd backend

# Run all tests
php artisan test

# Run specific test suites
php artisan test tests/Unit/
php artisan test tests/Feature/
```

## Technologies Used
- Frontend: Vue.js 2, Axios, Vue Router 3.6, Boostrap 5.3
- Backend: Laravel, Maatwebsite\Excel, Queue Jobs & Workers, Custom Logging Channels

## Contributing
Feel free to fork this repository and make your changes. If you would like to contribute, submit a pull request.