# Analizador de Excel

Sistema en Laravel para:
- Importar archivos Excel
- Normalizar datos por CUE
- Guardar histórico en MySQL
- Consultar por filtros
- Exportar reportes

## Stack
- Laravel
- MySQL
- maatwebsite/excel
- Blade
- Laragon (dev)

## Setup local
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
