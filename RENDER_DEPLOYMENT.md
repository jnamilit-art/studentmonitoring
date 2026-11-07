# Render Deployment Guide

This guide walks you through deploying your **Laravel + Inertia.js (Vue 3)** School Monitoring System to **Render**.

---

## 1. Push Your Code to GitHub / GitLab

Make sure all your latest files (including the newly generated `Dockerfile`, `docker/` folder, and `render.yaml`) are committed and pushed:

```bash
git add .
git commit -m "Prepare project for Render Docker deployment"
git push origin main
```

---

## 2. Set Up a Cloud Database

Because Render free web services have ephemeral storage, you will need a cloud database.

### Option A: Free Cloud MySQL (Recommended to match your local setup)
You can get a free managed MySQL database from:
- **[Aiven](https://aiven.io/)** (Free tier MySQL)
- **[TiDB Cloud](https://tidbcloud.com/)** (Free 5GB Serverless MySQL)
- **[Railway](https://railway.com/)** (One-click MySQL service)

Copy your connection details: Host, Port, Database name, Username, and Password.

### Option B: Render Managed PostgreSQL
If you prefer Render's built-in database:
1. In Render, click **New +** > **PostgreSQL**.
2. Copy the **Internal Database URL**.
3. Set `DB_CONNECTION=pgsql` in your Web Service environment variables.

---

## 3. Create the Web Service on Render

1. Log in to [dashboard.render.com](https://dashboard.render.com/).
2. Click **New +** > **Web Service**.
3. Select **Build and deploy from a Git repository** and connect your repository.
4. Render will automatically detect the **Dockerfile** in the root directory.
5. Configure the service settings:
   - **Name:** `pilot-school-system` (or whatever you prefer)
   - **Region:** Choose the region closest to your users (e.g., Singapore, Oregon, Frankfurt)
   - **Branch:** `main` (or `master`)
   - **Runtime:** `Docker`
   - **Instance Type:** `Free` (or `Starter` for 24/7 without sleep)

---

## 4. Set Environment Variables

Scroll down to the **Environment Variables** section and add:

| Key | Example Value | Description |
| :--- | :--- | :--- |
| `APP_NAME` | `School Monitoring System` | Application name |
| `APP_ENV` | `production` | Production environment |
| `APP_DEBUG` | `false` | Disable debug error display |
| `APP_KEY` | *(Generate via `php artisan key:generate --show`)* | Laravel encryption key |
| `APP_URL` | `https://your-service-name.onrender.com` | Your public Render URL |
| `LOG_CHANNEL` | `stderr` | Pipe logs to Render dashboard |
| `DB_CONNECTION` | `mysql` *(or `pgsql`)* | Database driver |
| `DB_HOST` | *(Your cloud DB host)* | Remote DB hostname |
| `DB_PORT` | `3306` | Remote DB port |
| `DB_DATABASE` | `pilot` | Remote DB name |
| `DB_USERNAME` | *(Your DB user)* | Remote DB user |
| `DB_PASSWORD` | *(Your DB password)* | Remote DB password |
| `AUTO_MIGRATE` | `true` | Runs `php artisan migrate --force` automatically on startup |
| `FILESYSTEM_DISK` | `s3` | Routes file uploads to AWS S3 |
| `AWS_ACCESS_KEY_ID` | `AKIAIOSFODNN7EXAMPLE` | AWS IAM Access Key |
| `AWS_SECRET_ACCESS_KEY` | `wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY` | AWS IAM Secret Key |
| `AWS_DEFAULT_REGION` | `us-east-1` *(or `ap-southeast-1`)* | AWS S3 Bucket Region |
| `AWS_BUCKET` | `my-school-storage` | AWS S3 Bucket Name |

> [!TIP]
> If your cloud database provider provides a single connection string (e.g. `mysql://user:pass@host:3306/db`), you can simply provide `DATABASE_URL` or `DB_URL` instead of individual host/user/password variables.

---

## 5. Setting Up Your AWS S3 Bucket

To permanently store student photos and school documents on AWS S3:

1. **Create an S3 Bucket in AWS:**
   - Log in to the [AWS Management Console](https://s3.console.aws.amazon.com/s3/).
   - Click **Create bucket**.
   - Give it a unique name (e.g. `school-portal-media-2026`).
   - Select your preferred region (e.g., `us-east-1` or `ap-southeast-1`).
   - Under **Block Public Access settings for this bucket**:
     - If photos will be displayed directly in user browsers, uncheck "Block all public access" and acknowledge the warning.
   - Click **Create bucket**.

2. **Configure CORS (for direct browser display):**
   - In your bucket, go to the **Permissions** tab and scroll to **Cross-origin resource sharing (CORS)**.
   - Click **Edit** and paste:
     ```json
     [
         {
             "AllowedHeaders": ["*"],
             "AllowedMethods": ["GET", "HEAD"],
             "AllowedOrigins": ["*"],
             "ExposeHeaders": []
         }
     ]
     ```

3. **Create IAM Credentials:**
   - Go to [AWS IAM Console](https://us-east-1.console.aws.amazon.com/iam/home#/users) > **Users** > **Create user**.
   - Attach policies directly: select **AmazonS3FullAccess** (or create an inline policy restricted to your bucket).
   - Go to the **Security credentials** tab > **Create access key** (choose "Application running outside AWS").
   - Copy the **Access Key ID** and **Secret Access Key** and paste them into Render's Environment Variables.

---

## 6. Deploy & Verify

1. Click **Deploy Web Service**.
2. Render will start the build:
   - **Stage 1:** Node container installs dependencies and builds Vite/Vue assets (`npm run build`).
   - **Stage 2:** PHP 8.3 container installs PHP extensions, runs Composer, sets up Nginx, and launches the application.
3. Once complete, you will see a green **Live** status with your URL (e.g., `https://pilot-school-system.onrender.com`).
4. Click the URL to open your application!
