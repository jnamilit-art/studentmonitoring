# Subdomain Setup Instructions

This document explains how to configure subdomain routing for the School Monitoring System.

## Overview

Each school can now have its own subdomain (e.g., `school1.yoursite.com`, `school2.yoursite.com`).

## Configuration Steps

### 1. Update .env File

Add or update the following environment variables in your `.env` file:

```env
APP_DOMAIN=yourdomain.com
APP_URL=http://yourdomain.com
```

**Important Notes:**
- Replace `yourdomain.com` with your actual domain
- For local development, you can use:
  - `APP_DOMAIN=localhost`
  - `APP_URL=http://localhost`
- For production, use your actual domain (e.g., `APP_DOMAIN=myschools.com`)

### 2. DNS Configuration

You need to configure a wildcard DNS record for your domain:

**For Production:**
- Add a wildcard A record: `*.yourdomain.com` pointing to your server IP
- Or add individual A records for each school subdomain

**For Local Development:**
You have two options:

**Option A: Use localhost (Recommended for development)**
- No DNS configuration needed
- Access via: `http://localhost` (super admin)
- Schools will be accessed via: `http://localhost` (no subdomain isolation in dev)

**Option B: Configure local DNS**
- Edit your `hosts` file (`C:\Windows\System32\drivers\etc\hosts` on Windows)
- Add entries like:
  ```
  127.0.0.1 school1.localhost
  127.0.0.1 school2.localhost
  ```
- Update `APP_DOMAIN=localhost` in `.env`

### 3. Web Server Configuration

**Apache:**
Add a virtual host with wildcard subdomain support:

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias *.yourdomain.com
    DocumentRoot /path/to/your/project/public
    
    <Directory /path/to/your/project/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Nginx:**
Configure server block with wildcard:

```nginx
server {
    listen 80;
    server_name yourdomain.com *.yourdomain.com;
    root /path/to/your/project/public;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 4. SSL Configuration (Production)

For HTTPS, you'll need SSL certificates. Options:

**Option A: Wildcard SSL Certificate**
- Get a wildcard SSL for `*.yourdomain.com`
- Most expensive but most convenient

**Option B: Let's Encrypt with Certbot**
```bash
certbot certonly --manual -d *.yourdomain.com -d yourdomain.com
```

**Option C: Individual Certificates**
- Get separate SSL for each subdomain
- More work but can be cheaper

## How It Works

### Super Admin Access
- Access via main domain: `http://yourdomain.com` or `http://localhost`
- Can manage all schools
- Can create/edit schools and assign subdomains

### School Access
- Each school accesses via its subdomain: `http://school1.yoursite.com`
- Middleware detects the subdomain and loads the correct school
- Users are automatically scoped to their school's data

### Data Isolation
- When a user logs in on a school subdomain, they only see data for that school
- Super admins can access any school via the main domain
- School admins can only access their assigned school

## Testing

### Local Development
1. Create a school in the Super Admin dashboard
2. Set subdomain to `test` (or any valid name)
3. Access via `http://localhost` (will show all schools for super admin)
4. For proper subdomain testing, configure local DNS as shown above

### Production
1. Create a school in the Super Admin dashboard
2. Set subdomain (e.g., `school1`)
3. Access via `http://school1.yourdomain.com`
4. Verify that only that school's data is visible

## Troubleshooting

### Subdomain not detected
- Check that `APP_DOMAIN` is set correctly in `.env`
- Verify DNS configuration
- Check web server wildcard configuration
- Clear config cache: `php artisan config:clear`

### School not found (404)
- Verify school has `is_active = true`
- Check subdomain matches exactly (case-sensitive)
- Ensure middleware is registered in `bootstrap/app.php`

### Mixed content errors (HTTPS)
- Ensure all assets use HTTPS
- Update `APP_URL` to use `https://`
- Configure SSL properly on your web server

## Security Notes

1. **Subdomain Validation**: Subdomains are validated to only contain alphanumeric characters and hyphens
2. **School Activation**: Only active schools are accessible via subdomain
3. **User Scoping**: Users are automatically scoped to their school's data when on a subdomain
4. **Super Admin Access**: Super admins should only access via the main domain to manage all schools

## Next Steps

After setup, you'll need to:
1. Update controllers to filter data by school when on a subdomain
2. Assign users to schools via the `school_id` field
3. Ensure all school-specific data is linked to schools
