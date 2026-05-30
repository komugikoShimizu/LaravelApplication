import type { Category, MonthlySummary, Transaction } from "./api";

export const categories: Category[] = [
  { id: 1, name: "給与", type: "income", color: "#2563eb" },
  { id: 2, name: "食費", type: "expense", color: "#dc2626" },
  { id: 3, name: "住居", type: "expense", color: "#7c3aed" },
  { id: 4, name: "交通", type: "expense", color: "#0891b2" },
  { id: 5, name: "趣味", type: "expense", color: "#ca8a04" }
];

export const transactions: Transaction[] = [
  {
    id: 1,
    occurred_on: "2026-05-25",
    type: "income",
    category_id: 1,
    category_name: "給与",
    amount: 320000,
    memo: "5月給与"
  },
  {
    id: 2,
    occurred_on: "2026-05-26",
    type: "expense",
    category_id: 2,
    category_name: "食費",
    amount: 6800,
    memo: "スーパー"
  },
  {
    id: 3,
    occurred_on: "2026-05-27",
    type: "expense",
    category_id: 3,
    category_name: "住居",
    amount: 82000,
    memo: "家賃"
  },
  {
    id: 4,
    occurred_on: "2026-05-28",
    type: "expense",
    category_id: 4,
    category_name: "交通",
    amount: 9600,
    memo: "定期券"
  },
  {
    id: 5,
    occurred_on: "2026-05-29",
    type: "expense",
    category_id: 5,
    category_name: "趣味",
    amount: 12400,
    memo: "書籍"
  }
];

export const monthlySummary: MonthlySummary = {
  month: "2026-05",
  income_total: 320000,
  expense_total: 110800,
  balance: 209200,
  by_category: [
    { category_id: 2, category_name: "食費", type: "expense", total: 6800, color: "#dc2626" },
    { category_id: 3, category_name: "住居", type: "expense", total: 82000, color: "#7c3aed" },
    { category_id: 4, category_name: "交通", type: "expense", total: 9600, color: "#0891b2" },
    { category_id: 5, category_name: "趣味", type: "expense", total: 12400, color: "#ca8a04" }
  ]
};
