# Docker Setup Guide

-B> @C:>2>4AB2> ?> 8A?>;L7>20=8N Docker 4;O @0725@BK20=8O ?@8;>65=8O C2 Manager Backend.

## !>45@60=85

- ["@51>20=8O](#B@51>20=8O)
- [Development >:@C65=85](#development->:@C65=85)
- [Production >:@C65=85](#production->:@C65=85)
- [>;57=K5 :><0=4K](#?>;57=K5-:><0=4K)
- [Troubleshooting](#troubleshooting)

## "@51>20=8O

- Docker Engine 20.10+
- Docker Compose 2.0+
- 8=8<C< 4GB RAM 4;O development
- 8=8<C< 8GB RAM 4;O production

## Development >:@C65=85

### KAB@K9 AB0@B

1. !:>?8@C9B5 `.env.example` 2 `.env`:
```bash
cp .env.example .env
```

2. 0AB@>9B5 ?5@5<5==K5 >:@C65=8O 2 `.env`:
```env
DB_HOST=db
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel

REDIS_HOST=redis
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

3. 0?CAB8B5 :>=B59=5@K:
```bash
# 0?CA: 2A5E A5@28A>2
docker-compose up -d

# 0?CA: A phpMyAdmin 8 Mailpit
docker-compose --profile dev up -d
```

4. #AB0=>28B5 7028A8<>AB8 8 70?CAB8B5 <83@0F88:
```bash
docker-compose exec php composer install
docker-compose exec php php artisan key:generate
docker-compose exec php php artisan migrate
docker-compose exec php php artisan storage:link
```

5. #AB0=>28B5 Node.js 7028A8<>AB8:
```bash
npm install
npm run dev
```

### >ABC?=K5 A5@28AK

| !5@28A | URL | ?8A0=85 |
|--------|-----|----------|
| Nginx | http://localhost | A=>2=>5 ?@8;>65=85 |
| phpMyAdmin | http://localhost:8080 | #?@02;5=85  (B>;L:> dev profile) |
| Mailpit | http://localhost:8025 | "5AB>2K9 ?>GB>2K9 A5@25@ (B>;L:> dev profile) |
| MySQL | localhost:3306 | 070 40==KE |

## Production >:@C65=85

### >43>B>2:0

1. !>7409B5 `.env.production`:
```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=your-generated-key

DB_HOST=db
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=strong_password
DB_ROOT_PASSWORD=strong_root_password

REDIS_HOST=redis
REDIS_PASSWORD=strong_redis_password

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

2. !>15@8B5 Docker >1@07:
```bash
docker-compose -f docker-compose.prod.yml build php
```

3. 0?CAB8B5 production >:@C65=85:
```bash
docker-compose -f docker-compose.prod.yml --env-file .env.production up -d
```

4. 0?CAB8B5 <83@0F88:
```bash
docker-compose -f docker-compose.prod.yml exec php php artisan migrate --force
docker-compose -f docker-compose.prod.yml exec php php artisan config:cache
docker-compose -f docker-compose.prod.yml exec php php artisan route:cache
docker-compose -f docker-compose.prod.yml exec php php artisan view:cache
```

### Backup 107K 40==KE

!>740=85 backup:
```bash
docker-compose -f docker-compose.prod.yml --profile backup run --rm backup
```

>AAB0=>2;5=85 87 backup:
```bash
docker-compose -f docker-compose.prod.yml exec -T db mysql -u root -p${DB_ROOT_PASSWORD} ${DB_DATABASE} < backup_file.sql
```

## >;57=K5 :><0=4K

### #?@02;5=85 :>=B59=5@0<8

```bash
# @>A<>B@ ;>3>2
docker-compose logs -f [service_name]

# 5@570?CA: A5@28A0
docker-compose restart [service_name]

# AB0=>2:0 2A5E A5@28A>2
docker-compose down

# AB0=>2:0 A C40;5=85< volumes (: C40;8B 40==K5 !)
docker-compose down -v

# @>A<>B@ AB0BCA0
docker-compose ps
```

###  01>B0 A PHP

```bash
# K?>;=5=85 :><0=4 Artisan
docker-compose exec php php artisan [command]

# #AB0=>2:0 Composer 7028A8<>AB59
docker-compose exec php composer install

# 0?CA: B5AB>2
docker-compose exec php php artisan test

# E>4 2 :>=B59=5@
docker-compose exec php sh
```

###  01>B0 A >G5@54O<8

```bash
# @>A<>B@ ;>3>2 queue worker
docker-compose logs -f queue

# 5@570?CA: queue worker
docker-compose restart queue

# G8AB:0 failed jobs
docker-compose exec php php artisan queue:flush
```

###  01>B0 A 

```bash
# MySQL CLI
docker-compose exec db mysql -u laravel -p laravel

# -:A?>@B 
docker-compose exec db mysqldump -u laravel -p laravel > backup.sql

# <?>@B 
docker-compose exec -T db mysql -u laravel -p laravel < backup.sql
```

## @E8B5:BC@0 Docker setup

### Multi-stage Dockerfile

Dockerfile 8A?>;L7C5B multi-stage build:
- **node-builder**: !1>@:0 frontend assets
- **base**: 07>2K9 >1@07 PHP A @0AH8@5=8O<8
- **development**: Development >:@C65=85 A debug =0AB@>9:0<8
- **production**: Production >1@07 A >?B8<870F8O<8 8 OPcache

### !5@28AK

#### Development (docker-compose.yml)

- **nginx**: Web A5@25@ (Alpine Linux)
- **php**: PHP-FPM 8.4 A @0AH8@5=8O<8
- **queue**: Laravel queue worker
- **db**: MySQL 8.0
- **redis**: Redis 7 4;O :5H0 8 A5AA89
- **pma**: phpMyAdmin (dev profile)
- **mailpit**: "5AB>2K9 SMTP A5@25@ (dev profile)

#### Production (docker-compose.prod.yml)

- **nginx**: Web A5@25@ A >3@0=8G5=8O<8 @5AC@A>2
- **php**: ?B8<878@>20==K9 PHP A OPcache
- **queue**: 2 @5?;8:8 queue workers
- **scheduler**: Laravel scheduler (cron)
- **db**: MySQL A production =0AB@>9:0<8
- **redis**: Redis A ?0@>;5<
- **backup**: !5@28A 4;O backup  (backup profile)

### Health checks

A5 A5@28AK 8<5NB health checks:
- **nginx**: HTTP ?@>25@:0
- **php**: PHP-FPM status
- **db**: MySQL ping
- **redis**: Redis ping

### Resource limits

Production >:@C65=85 8<55B >3@0=8G5=8O @5AC@A>2:
- CPU limits
- Memory limits
- Reservations 4;O :@8B8G=KE A5@28A>2

## Troubleshooting

### @>1;5<K A ?@020<8 4>ABC?0

```bash
# A?@02;5=85 ?@02 4;O storage 8 cache
docker-compose exec php chown -R laravel:laravel storage bootstrap/cache
docker-compose exec php chmod -R 755 storage bootstrap/cache
```

### @>1;5<K A ?>4:;NG5=85< : 

```bash
# @>25@:0 4>ABC?=>AB8 MySQL
docker-compose exec php php artisan tinker
>>> DB::connection()->getPdo();
```

### G8AB:0 :5H0

```bash
docker-compose exec php php artisan cache:clear
docker-compose exec php php artisan config:clear
docker-compose exec php php artisan route:clear
docker-compose exec php php artisan view:clear
```

### 5@5A1>@:0 >1@07>2

```bash
# 5@5A1>@:0 157 :5H0
docker-compose build --no-cache

# 5@5A1>@:0 :>=:@5B=>3> A5@28A0
docker-compose build --no-cache php
```

### @>1;5<K A ?0<OBLN

A;8 :>=B59=5@K ?040NB 87-70 =5E20B:8 ?0<OB8:

1. #25;8GLB5 ;8<8BK 2 docker-compose.yml
2. #25;8GLB5 Docker Desktop memory limit
3. #<5=LH8B5 :>;8G5AB2> @5?;8: queue workers

## 57>?0A=>ABL

### Development

- >@BK MySQL 8 Redis >B:@KBK 4;O C4>1AB20 >B;04:8
- Debug @568< 2:;NG5=
- phpMyAdmin 4>ABC?5=

### Production

- >@BK  8 Redis 70:@KBK (B>;L:> 2=CB@8 A5B8)
- Debug >B:;NG5=
- A?>;L7CNBAO ?5@5<5==K5 >:@C65=8O 4;O ?0@>;59
- OPcache 2:;NG5= 4;O ?@>872>48B5;L=>AB8
- Security headers 2 nginx
- 8=8<0;L=K9 >1@07 157 dev 7028A8<>AB59

###  5:><5=40F88

1. A?>;L7C9B5 A8;L=K5 ?0@>;8 2 production
2. 0AB@>9B5 SSL/TLS 4;O nginx
3.  53C;O@=> A>740209B5 backup 
4. >=8B>@LB5 ;>38: `docker-compose logs -f`
5. A?>;L7C9B5 secrets 4;O GC2AB28B5;L=KE 40==KE
6. 3@0=8GLB5 A5B52>9 4>ABC? G5@57 firewall

## >?>;=8B5;L=K5 @5AC@AK

- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [Laravel Docker Documentation](https://laravel.com/docs/deployment#docker)
