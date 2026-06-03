# API save message investigation

Date: 2026-06-02

## Symptom

The frontend showed this message after adding an expense:

```text
記録を保存できませんでした。入力内容とAPIのレスポンスを確認してください。
```

At the same time, the expense total increased, so at least one transaction was persisted.

## Current observation

- Browser-visible page no longer shows the save failure message after reload.
- Recent browser-origin API logs show successful requests:
  - `POST /api/transactions` -> `200`
  - `GET /api/categories` -> `200`
  - `GET /api/transactions?month=2026-05` -> `200`
  - `GET /api/summaries/monthly?month=2026-05` -> `200`
- The frontend save failure message is only set in `handleSubmit` when `budgetApi.createTransaction(...)` throws.
- In the current frontend implementation, a failure in the post-save reload path uses a different message:
  - `APIから家計簿データを取得できませんでした。Laravel APIの起動状態を確認してください。`

## Confirmed backend issue from malformed input

Sending malformed or missing request fields to `POST /api/transactions` returns `500 Internal Server Error`.

Observed example:

```text
SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'occurred_on' cannot be null
```

Trigger point:

- `TransactionController::store()` copies request fields without validation.
- `TransactionService::createTransaction()` immediately calls `Transaction::create(...)`.
- Required fields missing from the request reach MySQL and fail as database errors.

This can produce the frontend save failure message because the frontend treats any non-2xx POST response as a failed save.

## Previously observed backend environment issue

Before API connection stabilized, Laravel returned `500` because the PHP process could not write under:

```text
storage/framework/cache/data
```

The error was:

```text
fopen(/var/www/html/storage/framework/cache/data/...): Failed to open stream: No such file or directory
```

The immediate environment permission issue was corrected during connection verification by creating the framework directories and changing ownership of `storage` and `bootstrap/cache` to `www-data`.

## Data consistency note

The database currently contains persisted test rows added during verification. The expense total increased because POST requests did reach the backend successfully.

Some memo values inserted from non-browser command-line checks appeared as `???`. Browser-origin UTF-8 behavior was not conclusively isolated in this pass. If Japanese memo persistence matters, verify request encoding from the browser and MySQL connection charset before changing frontend behavior.

## Likely cause of the reported mismatch

Most likely:

1. A transaction POST reached the backend and inserted data.
2. The frontend still received a failed POST response or parse/network error for that submit attempt.
3. The frontend displayed the save failure message even though the persisted data affected the totals.

The backend has no request validation layer, so malformed/missing fields currently surface as `500` responses instead of controlled `422` validation errors. That is the clearest backend-side trigger found in this investigation.
