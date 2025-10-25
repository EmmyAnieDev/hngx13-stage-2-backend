# HNGX13 Stage 2 — Country Currency & Exchange API

A **RESTful API** built for **HNG13 Backend — Stage 2 Task**.  
It fetches country data from external APIs, computes exchange rates and estimated GDP, caches everything in a database, and generates summary images.

---

## 🚀 Features

- **POST /countries/refresh** — Fetch and cache all countries with exchange rates
- **GET /countries** — Retrieve all countries with advanced filtering and sorting
- **GET /countries/{name}** — Get a specific country by name
- **DELETE /countries/{name}** — Remove a country record
- **GET /status** — View total countries and last refresh timestamp
- **GET /countries/image** — Serve auto-generated summary image
- Computes:
  - Exchange rates from USD base
  - Estimated GDP using: `population × random(1000–2000) ÷ exchange_rate`
  - Handles multiple currencies per country (stores first only)
  - Auto-generates summary images with top 5 countries by GDP
- Proper error handling with appropriate HTTP status codes
- Clean Laravel architecture with Services, Controllers, and Models
- Deployed to **Railway**

---

## 🧠 Tech Stack

- **Framework:** Laravel 11
- **Language:** PHP 8.2+
- **Database:** MySQL
- **Image Processing:** Intervention Image (GD Driver)
- **Hosting:** AWS

---

## 📁 Project Structure

```
HNGX13-STAGE-2-BACKEND/
├── app/
│   ├── Helpers/
│   │   └── ImageHelper.php          # Image generation logic
│   ├── Http/Controllers/
│   │   ├── Controller.php
│   │   ├── CountryController.php    # Main country endpoints
│   │   └── StatusController.php     # Status endpoint
│   ├── Models/
│   │   ├── Country.php              # Country model
│   │   └── User.php
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   └── Services/
│       ├── CountryService.php       # Country data fetching logic
│       └── ExchangeRateService.php  # Exchange rate API client
├── bootstrap/
├── config/
├── database/
│   └── migrations/
│       └── *_create_countries_table.php
├── nginx/
│   └── conf.d/
│       └── default.conf             # Nginx configuration
├── public/
├── resources/
├── routes/
│   ├── api.php                      # API routes definition
│   ├── console.php
│   └── web.php
├── storage/
│   └── app/public/cache/            # Generated images location
├── tests/
├── vendor/
├── .env                             # Environment variables (not tracked)
├── .env.example                     # Sample environment configuration
├── .gitignore
├── .gitattributes
├── artisan
├── composer.json                    # PHP dependencies
├── composer.lock
├── docker-compose.yml               # Docker orchestration
├── Dockerfile                       # Docker image configuration
├── package.json
├── phpunit.xml
├── README.md                        # Documentation (this file)
└── vite.config.js
```

---

## ⚙️ Installation & Setup

Follow the steps below to set up and run this project locally.

### 1. Clone the repository
```bash
git clone https://github.com/emmy-anie-dev/hngx13-stage-2-backend.git
cd hngx13-stage-2-backend
```

### 2. Install dependencies

Make sure you have Composer installed. Then install required PHP packages:
```bash
composer install
```

### 3. Configure environment variables

Copy the example environment file:
```bash
cp .env.example .env
```

Edit `.env` with your database credentials:
```env
APP_NAME="Country API"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=country_api
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Generate application key
```bash
php artisan key:generate
```

### 5. Run database migrations
```bash
php artisan migrate
```

### 6. Create storage link
```bash
php artisan storage:link
```

---

## 🐳 Running with Docker

### Prerequisites
- Docker
- Docker Compose

### Start the application
```bash
docker-compose up --build -d
```

This will start:
- **PHP-FPM** container
- **MySQL** database container
- **Nginx** web server

The API will be available at `http://localhost`

---

## ▶️ Running Locally (Without Docker)

### Prerequisites
- PHP 8.2+
- MySQL v8.0+
- Composer

### 1. Start MySQL
Ensure your MySQL service is running.

### 2. Update environment variables
Edit `.env` with your local MySQL credentials.

### 3. Start Laravel development server
```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

---

## 🧪 API Endpoints & Testing

### 1. Refresh Countries Data
Fetches data from external APIs and caches in database. Also generates summary image.

```bash
curl -X POST http://localhost:8000/countries/refresh | jq
```

**Expected Response (200 OK):**
```json
{
  "total_countries": 250,
  "last_refreshed_at": "2025-10-25T14:30:00.000000Z"
}
```

### 2. Get All Countries
```bash
curl -X GET http://localhost:8000/countries | jq
```

**With Filters:**
```bash
# Filter by region
curl -X GET "http://localhost:8000/countries?region=Africa" | jq

# Filter by currency
curl -X GET "http://localhost:8000/countries?currency=NGN" | jq

# Sort by GDP (descending)
curl -X GET "http://localhost:8000/countries?sort=gdp_desc" | jq

# Combine filters
curl -X GET "http://localhost:8000/countries?region=Africa&sort=gdp_desc" | jq
```

**Expected Response (200 OK):**
```json
[
  {
    "id": 1,
    "name": "Nigeria",
    "capital": "Abuja",
    "region": "Africa",
    "population": 206139589,
    "currency_code": "NGN",
    "exchange_rate": 1600.23,
    "estimated_gdp": 257674481.25,
    "flag_url": "https://flagcdn.com/ng.svg",
    "last_refreshed_at": "2025-10-25T14:30:00.000000Z",
    "created_at": "2025-10-25T14:30:00.000000Z",
    "updated_at": "2025-10-25T14:30:00.000000Z"
  },
  {
    "id": 2,
    "name": "Ghana",
    "capital": "Accra",
    "region": "Africa",
    "population": 31072940,
    "currency_code": "GHS",
    "exchange_rate": 15.34,
    "estimated_gdp": 3029834520.6,
    "flag_url": "https://flagcdn.com/gh.svg",
    "last_refreshed_at": "2025-10-25T14:30:00.000000Z",
    "created_at": "2025-10-25T14:30:00.000000Z",
    "updated_at": "2025-10-25T14:30:00.000000Z"
  }
]
```

### 3. Get Specific Country
```bash
curl -X GET http://localhost:8000/countries/Nigeria | jq
```

**Expected Response (200 OK):**
```json
{
  "id": 1,
  "name": "Nigeria",
  "capital": "Abuja",
  "region": "Africa",
  "population": 206139589,
  "currency_code": "NGN",
  "exchange_rate": 1600.23,
  "estimated_gdp": 257674481.25,
  "flag_url": "https://flagcdn.com/ng.svg",
  "last_refreshed_at": "2025-10-25T14:30:00.000000Z",
  "created_at": "2025-10-25T14:30:00.000000Z",
  "updated_at": "2025-10-25T14:30:00.000000Z"
}
```

**Not Found (404):**
```json
{
  "error": "Country not found"
}
```

### 4. Delete Country
```bash
curl -X DELETE http://localhost:8000/countries/Ghana | jq
```

**Expected Response (204 OK):**
```json
{}
```

### 5. Get System Status
```bash
curl -X GET http://localhost:8000/status | jq
```

**Expected Response (200 OK):**
```json
{
  "total_countries": 250,
  "last_refreshed_at": "2025-10-25T14:30:00.000000Z"
}
```

### 6. Get Summary Image
```bash
curl -X GET http://localhost:8000/countries/image --output summary.png
```

Opens or downloads the generated summary image containing:
- Total number of countries
- Top 5 countries by estimated GDP
- Last refresh timestamp

**Not Found (404):**
```json
{
  "error": "Summary image not found"
}
```

---

## 📋 Query Parameters

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `region` | string | Filter by region | `?region=Africa` |
| `currency` | string | Filter by currency code | `?currency=NGN` |
| `sort` | string | Sort results (`gdp_desc`, `gdp_asc`) | `?sort=gdp_desc` |

---

## 🚨 Error Responses

| Status Code | Description | Example |
|-------------|-------------|---------|
| 400 | Bad Request | Invalid or missing required data |
| 404 | Not Found | Country does not exist |
| 500 | Internal Server Error | Unexpected server error |
| 503 | Service Unavailable | External API failure |

**Example Error (503 Service Unavailable):**
```json
{
  "error": "External data source unavailable",
  "details": "Could not fetch data from restcountries.com"
}
```

---

## 🔄 Refresh Behavior

### Currency Handling
- **Multiple currencies:** Stores only the first currency code from the array
- **Empty currencies array:**
  - Does NOT call exchange rate API
  - Sets `currency_code` to `null`
  - Sets `exchange_rate` to `null`
  - Sets `estimated_gdp` to `0`
  - Still stores the country record
- **Currency not in exchange rates:**
  - Sets `exchange_rate` to `null`
  - Sets `estimated_gdp` to `null`
  - Still stores the country record

### Update vs Insert Logic
- Matches existing countries by name (case-insensitive)
- **If country exists:** Updates all fields including recalculating GDP with new random multiplier
- **If country doesn't exist:** Inserts new record
- Random multiplier (1000-2000) is generated fresh on each refresh
- Successful refresh updates the global `last_refreshed_at` timestamp

---

## 🧰 Dependencies

| Package | Description | Version |
|---------|-------------|---------|
| `laravel/framework` | Laravel framework | ^11.0 |
| `intervention/image` | Image manipulation library | ^3.0 |
| `guzzlehttp/guzzle` | HTTP client | ^7.2 |
| `php` | PHP runtime | ^8.2 |
| `mysql` | MySQL database | ^8.0 |

Install PHP dependencies via Composer:
```bash
composer install
```

---

## 🌍 External APIs

### Countries API
**Endpoint:** `https://restcountries.com/v2/all?fields=name,capital,region,population,flag,currencies`

Provides country data including name, capital, region, population, flag URL, and currencies.

### Exchange Rates API
**Endpoint:** `https://open.er-api.com/v6/latest/USD`

Provides exchange rates for all currencies relative to USD.

---

## 🖼️ Image Generation

After each successful refresh, the system automatically generates a summary image containing:

1. **Total number of countries** stored in database
2. **Top 5 countries by estimated GDP** with formatted values
3. **Last refresh timestamp**

**Image specifications:**
- Dimensions: 600x400 pixels
- Format: PNG
- Location: `storage/app/public/cache/summary.png`
- Access via: `GET /countries/image`

---

## 🧹 Stopping the Application

### Docker
```bash
docker-compose down
```

To remove volumes as well:
```bash
docker-compose down -v
```

### Local Development
Press `Ctrl+C` in the terminal running `php artisan serve`

---

## 📝 Notes

- The API uses Laravel's built-in HTTP client for external API calls with 15-second timeout
- All country names are matched case-insensitively for retrieval and deletion
- The random GDP multiplier ensures variety in calculated values during each refresh
- Summary images are regenerated on every refresh to reflect current data
- Cache is used to store `last_refreshed_at` and `total_countries` for performance

---