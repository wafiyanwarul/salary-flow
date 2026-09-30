# SalaryFlow — Native PHP Cashflow & Salary Tracker

A modular monolith personal cashflow & salary tracker built with native PHP + vanilla CSS/JS and a zero-dependency JSON storage layer.

## Features

- **Multi-Income per Month**: Record multiple income streams in a single month (Salary, Monthly Allowance / Uang Bulanan, Freelance, Performance Bonus, Investment, etc.).
- **Monthly Expenses Tracking**: Record final monthly expenses or categorized expenses (Living cost, Rent/Housing, Utilities, Lifestyle, etc.).
- **Dual-Bar Cashflow Chart**: Side-by-side vertical bar chart comparing monthly Incomes (Neon Lime) and Expenses (Coral Red) with unified scale and interactive hover tooltips.
- **Harta Bersih & Savings Rate**:
  - **Total Harta Bersih (Net Worth)**: $\text{Total Income} - \text{Total Expense}$ with surplus/deficit indicators.
  - **Savings Rate (%)**: Percentage of income retained with health indicator (20%+ target).
  - Monthly net breakdown and running averages.
- **Custom Brand Favicon**: Crisp SVG favicon matching the SalaryFlow brand mark.
- **Modular Monolith Architecture**: Clean separation by domain modules (`Income`, `Expense`, `Dashboard`, `Core`) with a zero-dependency native autoloader.
- **Ultra-Responsive UI**: Optimized for compact flip phones (Galaxy Z Flip ~320px–360px), smartphones (iPhone, Android ~380px–440px) with bottom app bar, tablets (iPad, Galaxy Tab), and widescreen monitors.
- **Zero-Config Persistent Storage**: Local data stored in `storage/salaries.json` and `storage/expenses.json` with file locking (`flock`) to prevent race conditions.
- **Security**: CSRF token validation, strict type hints, HTML output escaping, and defensive input sanitization.

## Requirements

- PHP 8.1+
- No database server or database extension required.

## Run locally

From this project directory:

```bash
php -S localhost:8000 -t public
```

Then open `http://localhost:8000`.

Data files (`storage/salaries.json` and `storage/expenses.json`) are maintained automatically.

## Project Structure (Modular Monolith)

```text
salary-tracker-php/
├── app/
│   ├── Core/
│   │   ├── Autoloader.php            # Native PSR-4 style autoloader
│   │   └── JsonStorage.php           # Locked JSON persistence engine
│   ├── Modules/
│   │   ├── Income/
│   │   │   ├── IncomeRepository.php  # Multi-income CRUD & normalization
│   │   │   ├── IncomeController.php  # Income request handler & validation
│   │   │   └── views/
│   │   │       └── form.php          # Income input/edit form
│   │   ├── Expense/
│   │   │   ├── ExpenseRepository.php # Monthly expenses CRUD
│   │   │   ├── ExpenseController.php # Expense request handler & validation
│   │   │   └── views/
│   │   │       └── form.php          # Expense input/edit form
│   │   └── Dashboard/
│   │       ├── DashboardService.php  # Aggregations, Net worth & Savings rate
│   │       ├── DashboardController.php
│   │       └── views/
│   │           └── index.php         # Dual-bar chart, stats & tabbed tables
│   ├── views/
│   │   └── layouts/
│   │       └── app.php               # App shell, mobile headers & bottom nav
│   ├── bootstrap.php                 # Core autoloader & module wiring
│   └── helpers.php                   # Escaping, CSRF, flash & currency formatters
├── config/
│   └── config.php                    # Application configuration & storage paths
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   └── app.css               # Responsive design across all breakpoints
│   │   ├── js/
│   │   │   └── app.js                # Rupiah masking, tab switching & mobile touch
│   │   └── img/
│   │       └── favicon.svg           # High-DPI neon SVG brand favicon
│   └── index.php                     # Route dispatcher (income, expense, dashboard)
└── storage/
    ├── salaries.json                 # Income data (100% backward compatible)
    └── expenses.json                 # Expense data
```
