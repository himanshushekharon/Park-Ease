FROM php:8.4-cli

# ── System dependencies ────────────────────────────────────────────────────
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    zip \
    gnupg \
    ca-certificates \
    libssl-dev \
    pkg-config \
    && rm -rf /var/lib/apt/lists/*

# ── Node.js 20 (LTS) via NodeSource ───────────────────────────────────────
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# ── PHP extensions ─────────────────────────────────────────────────────────
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-install zip


# ── PHP production config ──────────────────────────────────────────────────
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# ── Composer ──────────────────────────────────────────────────────────────
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# ── Working directory ──────────────────────────────────────────────────────
WORKDIR /app

# ── PHP dependencies (cached layer — composer files copied first) ──────────
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-dev --no-scripts --no-autoloader

# ── Node dependencies (cached layer — package files copied first) ──────────
COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts

# ── Copy application source ────────────────────────────────────────────────
COPY . .

# ── Finish composer install (run scripts, generate autoloader) ─────────────
RUN composer dump-autoload --optimize --no-dev \
    && composer run-script post-autoload-dump

# ── Build frontend assets ──────────────────────────────────────────────────
RUN npm run build && rm -rf node_modules

# ── Environment setup ──────────────────────────────────────────────────────
# .env is NOT committed. Copy the example so artisan commands work at build
# time. APP_KEY must be injected via Render (or platform) environment variables.
# Never generate the key here — a build-time key breaks sessions on redeploy.
RUN cp .env.example .env

# ── Laravel production storage symlink ─────────────────────────────────────
RUN php artisan storage:link --force


# ── Permissions ───────────────────────────────────────────────────────────
RUN chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# ── Port ──────────────────────────────────────────────────────────────────
EXPOSE 10000

# ── Startup ───────────────────────────────────────────────────────────────
# At runtime Render injects real env vars (APP_KEY, MONGODB_URI, etc.).
# Runtime Steps:
#   1. Clear stale configuration cache.
#   2. Clear application cache.
#   3. Validate APP_KEY (warn/generate fallback if missing).
#   4. Regenerate all Laravel production caches (config, routes, views).
#   5. Serve the application on port 10000.
CMD ["sh", "-c", \
    "php artisan config:clear && \
     php artisan cache:clear && \
     if [ -z \"$APP_KEY\" ]; then echo 'WARNING: APP_KEY not set — generating fallback. Sessions will reset on redeploy!' && php artisan key:generate --force; fi && \
     php artisan config:cache && \
     php artisan route:cache && \
     php artisan view:cache && \
     php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]