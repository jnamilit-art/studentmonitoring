# Wampserver Virtual Host Configuration for School Subdomains

This guide explains how to configure Wampserver to handle school subdomains for the Student Monitoring System.

## Option 1: Individual Virtual Hosts (Recommended for Testing)

For each school, create a separate virtual host in Apache.

### Step 1: Edit hosts file

Edit `C:\Windows\System32\drivers\etc\hosts` (run as administrator):

Add entries for each school:
```
127.0.0.1 school1.localhost
127.0.0.1 school2.localhost
127.0.0.1 school3.localhost
```

### Step 2: Configure Apache Virtual Hosts

1. Open Wampserver
2. Click the Apache icon → httpd-vhosts.conf
3. Add the following configuration:

```apache
<VirtualHost *:80>
    ServerName school1.localhost
    DocumentRoot "C:/wamp64/www/pilot/public"
    
    <Directory "C:/wamp64/www/pilot/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    ServerName school2.localhost
    DocumentRoot "C:/wamp64/www/pilot/public"
    
    <Directory "C:/wamp64/www/pilot/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    ServerName school3.localhost
    DocumentRoot "C:/wamp64/www/pilot/public"
    
    <Directory "C:/wamp64/www/pilot/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Step 3: Restart Apache

1. Click the Apache icon in Wampserver
2. Select "Restart All Services"

### Step 4: Test

Access the schools:
- http://school1.localhost
- http://school2.localhost
- http://school3.localhost

All should point to the same Student Monitoring System but will show different school data based on the subdomain.

---

## Option 2: Wildcard Virtual Host (Advanced)

Configure a single wildcard virtual host to handle all subdomains.

### Step 1: Edit hosts file

Add a wildcard entry (not typically supported in hosts file, so you'll still need individual entries or use a local DNS server like Dnsmasq).

### Step 2: Configure Apache

In `httpd-vhosts.conf`:

```apache
<VirtualHost *:80>
    ServerName localhost
    ServerAlias *.localhost
    DocumentRoot "C:/wamp64/www/pilot/public"
    
    <Directory "C:/wamp64/www/pilot/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Note:** This requires a local DNS server that supports wildcards, which the Windows hosts file does not.

---

## Option 3: Use Dnsmasq for Local Wildcard DNS (Best for Development)

### Step 1: Install Dnsmasq

Download and install Dnsmasq (lightweight DNS server).

### Step 2: Configure Dnsmasq

Edit `C:\Program Files (x86)\dnsmasq\dnsmasq.conf`:

```
address=/localhost/127.0.0.1
address=/.localhost/127.0.0.1
listen-address=127.0.0.1
```

### Step 3: Configure Windows DNS

1. Open Network Connections
2. Right-click your network adapter → Properties
3. Internet Protocol Version 4 (TCP/IPv4) → Properties
4. Set DNS server to `127.0.0.1` (your Dnsmasq)
5. Add a secondary DNS (like 8.8.8.8) for external sites

### Step 4: Configure Apache Wildcard

Use the wildcard virtual host configuration from Option 2.

### Step 5: Restart Services

1. Restart Dnsmasq service
2. Restart Apache in Wampserver

Now `anything.localhost` will resolve to your application!

---

## Quick Setup Script (Option 1)

To quickly set up for your existing schools, here's a script to add to your hosts file:

```
127.0.0.1 acer-high.localhost
127.0.0.1 surigao-city.localhost
127.0.0.1 pilot-school.localhost
```

And corresponding Apache virtual hosts:

```apache
<VirtualHost *:80>
    ServerName acer-high.localhost
    DocumentRoot "C:/wamp64/www/pilot/public"
    <Directory "C:/wamp64/www/pilot/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    ServerName surigao-city.localhost
    DocumentRoot "C:/wamp64/www/pilot/public"
    <Directory "C:/wamp64/www/pilot/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    ServerName pilot-school.localhost
    DocumentRoot "C:/wamp64/www/pilot/public"
    <Directory "C:/wamp64/www/pilot/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

---

## Important Notes

1. **DocumentRoot**: Must point to the `public` folder of your Laravel project: `C:/wamp64/www/pilot/public`

2. **Directory Permissions**: The `<Directory>` block must allow all access for local development

3. **Subdomain Matching**: The subdomain in the URL must exactly match what you set in the school's `subdomain` field in the database

4. **Case Sensitivity**: Subdomains are case-sensitive in the database validation

5. **Active Schools**: Only schools with `is_active = true` will be accessible

---

## Testing the Setup

1. Open your browser
2. Go to `http://school1.localhost` (or whatever subdomain you configured)
3. You should see the Student Monitoring System
4. The middleware will detect the subdomain and load the correct school

---

## Troubleshooting

### "Page Not Found" or 404
- Check that DocumentRoot points to the correct `public` folder
- Verify Apache is running
- Check if the hosts file entries are correct

### "Access Forbidden" (403)
- Check Directory permissions in virtual host config
- Ensure `Require all granted` is set

### Subdomain not detected
- Verify the subdomain matches exactly (including case)
- Check that the school has `is_active = true` in database
- Clear Laravel config: `php artisan config:clear`

### Links don't work
- Ensure you've added the subdomain to the school in the database
- Check that `APP_DOMAIN` is set to `localhost` in `.env`
