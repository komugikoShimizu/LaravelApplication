export type TransactionType = "income" | "expense";

export type Transaction = {
  id: number;
  occurred_on: string;
  type: TransactionType;
  category_id: number;
  category_name: string;
  amount: number;
  memo: string | null;
};

export type Category = {
  id: number;
  name: string;
  type: TransactionType;
  color: string;
};

export type MonthlySummary = {
  month: string;
  income_total: number;
  expense_total: number;
  balance: number;
  by_category: Array<{
    category_id: number;
    category_name: string;
    type: TransactionType;
    total: number;
    color: string;
  }>;
};

export type TransactionPayload = {
  occurred_on: string;
  type: TransactionType;
  category_id: number;
  amount: number;
  memo?: string;
};

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL ?? "http://localhost/api";

async function request<T>(path: string, init?: RequestInit): Promise<T> {
  const response = await fetch(`${API_BASE_URL}${path}`, {
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
      ...init?.headers
    },
    ...init
  });

  if (!response.ok) {
    throw new Error(`API request failed: ${response.status}`);
  }

  return response.json() as Promise<T>;
}

export const budgetApi = {
  listTransactions: (month: string) =>
    request<{ data: Transaction[] }>(`/transactions?month=${month}`),
  listCategories: () => request<{ data: Category[] }>("/categories"),
  getMonthlySummary: (month: string) =>
    request<{ data: MonthlySummary }>(`/summaries/monthly?month=${month}`),
  createTransaction: (payload: TransactionPayload) =>
    request<{ data: Transaction }>("/transactions", {
      method: "POST",
      body: JSON.stringify(payload)
    })
};
