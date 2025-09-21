# PHP Router

## Как использовать

```bash
composer dump-autoload
php -S localhost:8000
```

## Доступные маршруты

### 1. GET /categories

Получить список категорий

```bash
curl -X GET http://localhost:8000/categories
```

**Ответ:**

```json
{
  "categories": ["books", "electronics", "clothing"]
}
```

### 2. PUT /newsletter/subscribe

Подписка на рассылку (с rate limiting: 10 запросов в минуту)

```bash
curl -X PUT http://localhost:8000/newsletter/subscribe \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "name": "John Doe"}'
```

**Ответ:**

```json
{
  "result": "subscribed",
  "params": {
    "email": "user@example.com",
    "name": "John Doe"
  }
}
```

### 3. GET /categories/{category_name}/products

Получить продукты по категории

```bash
curl -X GET http://localhost:8000/categories/books/products
```

**Ответ:**

```json
{
  "category": "books",
  "products": ["books-item-1", "books-item-2"]
}
```