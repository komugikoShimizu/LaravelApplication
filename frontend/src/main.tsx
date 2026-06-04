import React, { useCallback, useEffect, useMemo, useState } from "react";
import ReactDOM from "react-dom/client";
import { CalendarDays, CircleDollarSign, Landmark, Plus, ReceiptText, WalletCards } from "lucide-react";
import { budgetApi } from "./lib/api";
import type { Category, MonthlySummary, TransactionPayload, TransactionType } from "./lib/api";
import "./styles.css";

const currency = new Intl.NumberFormat("ja-JP", {
  style: "currency",
  currency: "JPY",
  maximumFractionDigits: 0
});

function App() {
  const [month, setMonth] = useState("2026-05");
  const [type, setType] = useState<TransactionType>("expense");
  const [categories, setCategories] = useState<Category[]>([]);
  const [monthlyTransactions, setMonthlyTransactions] = useState<MonthlySummaryTransaction[]>([]);
  const [totals, setTotals] = useState<MonthlySummary>({
    month,
    income_total: 0,
    expense_total: 0,
    balance: 0,
    by_category: []
  });
  const [isLoading, setIsLoading] = useState(true);
  const [isSaving, setIsSaving] = useState(false);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const availableCategories = categories.filter((category) => category.type === type);
  const expenseCategoryTotals = totals.by_category.filter((category) => category.type === "expense");

  const defaultDate = useMemo(() => `${month}-01`, [month]);

  const loadBudgetData = useCallback(async () => {
    setIsLoading(true);
    setErrorMessage(null);

    try {
      const [categoriesResponse, transactionsResponse, summaryResponse] = await Promise.all([
        budgetApi.listCategories(),
        budgetApi.listTransactions(month),
        budgetApi.getMonthlySummary(month)
      ]);

      setCategories(categoriesResponse.data);
      setMonthlyTransactions(transactionsResponse.data);
      setTotals(summaryResponse.data);
    } catch (error) {
      console.error(error);
      setErrorMessage("APIから家計簿データを取得できませんでした。Laravel APIの起動状態を確認してください。");
    } finally {
      setIsLoading(false);
    }
  }, [month]);

  useEffect(() => {
    void loadBudgetData();
  }, [loadBudgetData]);

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    const form = new FormData(event.currentTarget);
    const payload: TransactionPayload = {
      occurred_on: String(form.get("occurred_on")),
      type,
      category_id: Number(form.get("category_id")),
      amount: Number(form.get("amount")),
      memo: String(form.get("memo") ?? "")
    };

    setIsSaving(true);
    setErrorMessage(null);

    try {
      await budgetApi.createTransaction(payload);
      await loadBudgetData();
      event.currentTarget.reset();
    } catch (error) {
      console.error(error);
      setErrorMessage("記録を保存できませんでした。入力内容とAPIのレスポンスを確認してください。");
    } finally {
      setIsSaving(false);
    }
  };

  return (
    <main className="appShell">
      <aside className="sidebar">
        <div className="brand">
          <WalletCards aria-hidden="true" />
          <span>家計簿</span>
        </div>
        <nav>
          <a className="active" href="#dashboard">
            <Landmark aria-hidden="true" />
            ダッシュボード
          </a>
          <a href="#transactions">
            <ReceiptText aria-hidden="true" />
            明細
          </a>
        </nav>
      </aside>

      <section className="workspace" id="dashboard">
        <header className="topbar">
          <div>
            <p className="eyebrow">Monthly Budget</p>
            <h1>{month.replace("-", "年")}月の家計</h1>
          </div>
          <label className="monthPicker">
            <CalendarDays aria-hidden="true" />
            <input type="month" value={month} onChange={(event) => setMonth(event.target.value)} />
          </label>
        </header>

        <section className="metrics" aria-label="月次集計">
          <Metric label="収入" value={totals.income_total} tone="income" />
          <Metric label="支出" value={totals.expense_total} tone="expense" />
          <Metric label="残高" value={totals.balance} tone="balance" />
        </section>

        {errorMessage ? <p className="errorMessage">{errorMessage}</p> : null}

        <section className="contentGrid">
          <form className="entryPanel" onSubmit={handleSubmit}>
            <div className="panelHeader">
              <CircleDollarSign aria-hidden="true" />
              <h2>入出金を記録</h2>
            </div>
            <div className="segmented" role="group" aria-label="入出金種別">
              <button type="button" className={type === "expense" ? "selected" : ""} onClick={() => setType("expense")}>
                支出
              </button>
              <button type="button" className={type === "income" ? "selected" : ""} onClick={() => setType("income")}>
                収入
              </button>
            </div>
            <label>
              日付
              <input name="occurred_on" type="date" defaultValue={defaultDate} required />
            </label>
            <label>
              カテゴリ
              <select name="category_id" required disabled={availableCategories.length === 0}>
                {availableCategories.map((category) => (
                  <option key={category.id} value={category.id}>
                    {category.name}
                  </option>
                ))}
              </select>
            </label>
            <label>
              金額
              <input name="amount" type="number" min="1" inputMode="numeric" placeholder="4800" required />
            </label>
            <label>
              メモ
              <input name="memo" type="text" placeholder="任意" />
            </label>
            <button className="submitButton" type="submit" disabled={isSaving || availableCategories.length === 0}>
              <Plus aria-hidden="true" />
              {isSaving ? "保存中" : "追加"}
            </button>
          </form>

          <section className="categoryPanel" aria-label="カテゴリ別支出">
            <div className="panelHeader">
              <ReceiptText aria-hidden="true" />
              <h2>カテゴリ別</h2>
            </div>
            <div className="categoryList">
              {expenseCategoryTotals.map((category) => (
                <div className="categoryRow" key={category.category_id}>
                  <span className="swatch" style={{ backgroundColor: category.color }} />
                  <span>{category.category_name}</span>
                  <strong>{currency.format(category.total)}</strong>
                </div>
              ))}
            </div>
          </section>
        </section>

        <section className="tablePanel" id="transactions">
          <div className="panelHeader">
            <ReceiptText aria-hidden="true" />
            <h2>最近の明細</h2>
          </div>
          <div className="tableWrap">
            <table>
              <thead>
                <tr>
                  <th>日付</th>
                  <th>カテゴリ</th>
                  <th>メモ</th>
                  <th>金額</th>
                </tr>
            </thead>
              <tbody>
                {isLoading ? (
                  <tr>
                    <td colSpan={4}>読み込み中</td>
                  </tr>
                ) : monthlyTransactions.length === 0 ? (
                  <tr>
                    <td colSpan={4}>記録がありません</td>
                  </tr>
                ) : (
                  monthlyTransactions.map((transaction) => (
                  <tr key={transaction.id}>
                    <td>{formatDate(transaction.occurred_on)}</td>
                    <td>{transaction.category_name}</td>
                    <td>{transaction.memo}</td>
                    <td className={transaction.type}>{currency.format(transaction.amount)}</td>
                  </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </section>
      </section>
    </main>
  );
}

type MonthlySummaryTransaction = {
  id: number;
  occurred_on: string;
  type: TransactionType;
  category_id: number;
  category_name: string;
  amount: number;
  memo: string | null;
};

function formatDate(value: string) {
  return value.slice(0, 10);
}

function Metric({ label, value, tone }: { label: string; value: number; tone: "income" | "expense" | "balance" }) {
  return (
    <article className={`metric ${tone}`}>
      <span>{label}</span>
      <strong>{currency.format(value)}</strong>
    </article>
  );
}

ReactDOM.createRoot(document.getElementById("root")!).render(
  <React.StrictMode>
    <App />
  </React.StrictMode>
);
