# Database Switching Guide

This project supports **two database options**:

## Option 1: Local PostgreSQL (Docker)
- **Best for**: Local development
- **Setup**: Includes a PostgreSQL container
- **No external dependencies**

## Option 2: Supabase PostgreSQL
- **Best for**: Production/External database
- **Setup**: Uses cloud Supabase database
- **Requires**: Supabase account & credentials

---

## How to Switch

### Windows Users:
```bash
# Switch to LOCAL database
switch-db.bat local

# Switch to SUPABASE database
switch-db.bat supabase
```

### Mac/Linux Users:
```bash
# Make script executable (first time only)
chmod +x switch-db.sh

# Switch to LOCAL database
./switch-db.sh local

# Switch to SUPABASE database
./switch-db.sh supabase
```

---

## After Switching

Once you've switched, start Docker:
```bash
docker-compose up -d
```

---

## Files Explained

| File | Purpose |
|------|---------|
| `.env.local` | Environment config for local PostgreSQL |
| `.env.supabase` | Environment config for Supabase |
| `docker-compose.local.yml` | Docker setup with local PostgreSQL included |
| `docker-compose.supabase.yml` | Docker setup using external Supabase |
| `.env` | Current active config (auto-updated) |
| `docker-compose.yml` | Current active Docker setup (auto-updated) |

---

## Database Details

### Local Setup
- **Host**: postgres (Docker)
- **Port**: 5432
- **Username**: postgres
- **Password**: password
- **Database**: autosurat

### Supabase Setup
- **Host**: db.mljztpiomzshxngezgum.supabase.co
- **Port**: 5432
- **Username**: postgres
- **Password**: dbautosurat
- **Database**: postgres

---

## Quick Start

### First Time - Choose Local:
```bash
# Windows
switch-db.bat local

# Mac/Linux
./switch-db.sh local

# Start
docker-compose up -d

# Access: http://localhost
```

### Switch to Supabase Later:
```bash
# Stop current containers
docker-compose down

# Windows
switch-db.bat supabase

# Mac/Linux
./switch-db.sh supabase

# Start
docker-compose up -d

# Access: http://localhost
```

---

## Troubleshooting

**"Database connection failed"**
- Make sure Docker is running
- Check `.env` file is correct
- For Supabase: verify credentials in `.env.supabase`

**"Port 5432 already in use"**
- Change the port in `.env.local`

**Script not working (Mac/Linux)**
- Run: `chmod +x switch-db.sh`
