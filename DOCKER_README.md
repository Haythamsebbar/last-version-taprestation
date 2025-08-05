# Laravel Application Docker Setup

This Docker setup provides a complete environment for running your Laravel application with MySQL database, automatic migrations, and seeding.

## Services Included

- **Laravel App**: PHP 8.2 with Apache
- **MySQL 8.0**: Database server
- **phpMyAdmin**: Database management interface

## Quick Start

### Production Environment

1. **Build and start the containers:**
   ```bash
   docker-compose up -d --build
   ```

2. **Access the application:**
   - Laravel App: http://localhost:8000
   - phpMyAdmin: http://localhost:8080

### Development Environment

1. **Build and start the development containers:**
   ```bash
   docker-compose -f docker-compose.dev.yml up -d --build
   ```

2. **Access the application:**
   - Laravel App: http://localhost:8000
   - phpMyAdmin: http://localhost:8080

## What Happens During Startup

The startup script (`docker/start.sh`) automatically:

1. Waits for MySQL to be ready
2. Copies the Docker environment configuration
3. Clears Laravel caches
4. Generates application key
5. Runs database migrations
6. Runs database seeders
7. Caches configuration for production
8. Sets proper file permissions
9. Starts Apache server

## Database Configuration

- **Database Name**: taprestation
- **Username**: laravel_user
- **Password**: laravel_password
- **Root Password**: root_password

## Useful Commands

### View logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
docker-compose logs -f mysql
```

### Execute commands in containers
```bash
# Laravel artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan tinker

# Access MySQL
docker-compose exec mysql mysql -u laravel_user -p taprestation
```

### Stop and remove containers
```bash
docker-compose down

# Remove volumes as well (WARNING: This will delete your database data)
docker-compose down -v
```

### Rebuild containers
```bash
docker-compose up -d --build
```

## File Structure

```
docker/
├── apache/
│   └── 000-default.conf    # Apache virtual host configuration
├── mysql/
│   └── init.sql           # MySQL initialization script
├── .env.docker           # Docker environment variables
└── start.sh              # Application startup script
```

## Troubleshooting

### Container won't start
- Check logs: `docker-compose logs app`
- Ensure ports 8000, 3306, and 8080 are not in use

### Database connection issues
- Verify MySQL is healthy: `docker-compose ps`
- Check MySQL logs: `docker-compose logs mysql`

### Permission issues
- The startup script sets proper permissions automatically
- If issues persist, run: `docker-compose exec app chown -R www-data:www-data /var/www/html/storage`

### Reset database
```bash
docker-compose down -v
docker-compose up -d --build
```

## Environment Variables

The Docker setup uses environment variables defined in `docker/.env.docker`. Key variables:

- `DB_HOST=mysql` (container name)
- `DB_DATABASE=taprestation`
- `DB_USERNAME=laravel_user`
- `DB_PASSWORD=laravel_password`

## Production Considerations

- Change default passwords in production
- Use environment-specific `.env` files
- Consider using Docker secrets for sensitive data
- Set up proper backup strategies for the MySQL volume
- Configure SSL/TLS for production domains