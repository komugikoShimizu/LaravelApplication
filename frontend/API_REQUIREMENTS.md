# 家計簿 React フロントが希望する API

React 側は `VITE_API_BASE_URL` を API の起点として利用します。Docker では未指定時に `http://localhost/api` を見に行く想定です。

## エンドポイント

### `GET /api/categories`

入出金カテゴリ一覧。

```json
{
  "data": [
    { "id": 1, "name": "給与", "type": "income", "color": "#2563eb" },
    { "id": 2, "name": "食費", "type": "expense", "color": "#dc2626" }
  ]
}
```

### `GET /api/transactions?month=2026-05`

指定月の明細一覧。`month` は `YYYY-MM` 形式です。

```json
{
  "data": [
    {
      "id": 1,
      "occurred_on": "2026-05-25",
      "type": "income",
      "category_id": 1,
      "category_name": "給与",
      "amount": 320000,
      "memo": "5月給与"
    }
  ]
}
```

### `POST /api/transactions`

明細作成。

Request:

```json
{
  "occurred_on": "2026-05-30",
  "type": "expense",
  "category_id": 2,
  "amount": 4800,
  "memo": "スーパー"
}
```

Response:

```json
{
  "data": {
    "id": 10,
    "occurred_on": "2026-05-30",
    "type": "expense",
    "category_id": 2,
    "category_name": "食費",
    "amount": 4800,
    "memo": "スーパー"
  }
}
```

### `GET /api/summaries/monthly?month=2026-05`

月次集計。

```json
{
  "data": {
    "month": "2026-05",
    "income_total": 320000,
    "expense_total": 110800,
    "balance": 209200,
    "by_category": [
      {
        "category_id": 2,
        "category_name": "食費",
        "type": "expense",
        "total": 6800,
        "color": "#dc2626"
      }
    ]
  }
}
```

## バリデーション希望

- `occurred_on`: required, date
- `type`: required, `income` または `expense`
- `category_id`: required, categories.id に存在
- `amount`: required, integer, min:1
- `memo`: nullable, string, max:255

## CORS

開発時は React が `http://localhost:5173`、Laravel API が `http://localhost` になるため、Laravel 側 CORS で `http://localhost:5173` からのリクエストを許可してください。
