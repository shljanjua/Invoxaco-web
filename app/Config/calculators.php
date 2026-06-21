<?php

return [
    'product-costing' => [
        'name' => 'Product Costing Calculator',
        'category' => 'Pricing & Costing',
        'icon' => 'bi-box-seam',
        'description' => 'Work out your true total cost and cost per unit from materials, labor, and overhead.',
        'fields' => [
            ['name' => 'materialCost', 'label' => 'Material Cost', 'default' => 1000],
            ['name' => 'laborCost', 'label' => 'Labor Cost', 'default' => 500],
            ['name' => 'overheadCost', 'label' => 'Overhead Cost', 'default' => 300],
            ['name' => 'unitsProduced', 'label' => 'Units Produced', 'default' => 100, 'min' => 1],
        ],
        'results' => [
            ['key' => 'totalCost', 'label' => 'Total Production Cost', 'format' => 'currency'],
            ['key' => 'costPerUnit', 'label' => 'Cost Per Unit', 'format' => 'currency'],
        ],
    ],
    'profit-margin' => [
        'name' => 'Profit Margin Calculator',
        'category' => 'Pricing & Costing',
        'icon' => 'bi-graph-up-arrow',
        'description' => 'Calculate gross profit, margin %, and markup % from revenue and cost.',
        'fields' => [
            ['name' => 'revenue', 'label' => 'Revenue', 'default' => 1000],
            ['name' => 'cost', 'label' => 'Cost', 'default' => 600],
        ],
        'results' => [
            ['key' => 'profit', 'label' => 'Gross Profit', 'format' => 'currency'],
            ['key' => 'grossMarginPercent', 'label' => 'Gross Margin', 'format' => 'percent'],
            ['key' => 'markupPercent', 'label' => 'Markup', 'format' => 'percent'],
        ],
    ],
    'break-even' => [
        'name' => 'Break-Even Calculator',
        'category' => 'Pricing & Costing',
        'icon' => 'bi-bullseye',
        'description' => 'Find the exact number of units and revenue needed to cover your fixed costs.',
        'fields' => [
            ['name' => 'fixedCosts', 'label' => 'Total Fixed Costs', 'default' => 5000],
            ['name' => 'pricePerUnit', 'label' => 'Price Per Unit', 'default' => 50],
            ['name' => 'variableCostPerUnit', 'label' => 'Variable Cost Per Unit', 'default' => 20],
        ],
        'results' => [
            ['key' => 'contributionMargin', 'label' => 'Contribution Margin / Unit', 'format' => 'currency'],
            ['key' => 'breakEvenUnits', 'label' => 'Break-Even Units', 'format' => 'number'],
            ['key' => 'breakEvenRevenue', 'label' => 'Break-Even Revenue', 'format' => 'currency'],
        ],
    ],
    'roi' => [
        'name' => 'ROI Calculator',
        'category' => 'Investment & Returns',
        'icon' => 'bi-cash-coin',
        'description' => 'Measure the return on investment between your initial outlay and final value.',
        'fields' => [
            ['name' => 'initialInvestment', 'label' => 'Initial Investment', 'default' => 10000],
            ['name' => 'finalValue', 'label' => 'Final Value', 'default' => 13000],
        ],
        'results' => [
            ['key' => 'netProfit', 'label' => 'Net Profit', 'format' => 'currency'],
            ['key' => 'roiPercent', 'label' => 'ROI', 'format' => 'percent'],
        ],
    ],
    'roas' => [
        'name' => 'ROAS Calculator',
        'category' => 'Investment & Returns',
        'icon' => 'bi-megaphone',
        'description' => 'Calculate your return on ad spend from campaign revenue and spend.',
        'fields' => [
            ['name' => 'revenueFromAds', 'label' => 'Revenue From Ads', 'default' => 5000],
            ['name' => 'adSpend', 'label' => 'Ad Spend', 'default' => 1000],
        ],
        'results' => [
            ['key' => 'roas', 'label' => 'ROAS', 'format' => 'ratio'],
            ['key' => 'roasPercent', 'label' => 'ROAS %', 'format' => 'percent'],
        ],
    ],
    'cash-flow' => [
        'name' => 'Cash Flow Calculator',
        'category' => 'Investment & Returns',
        'icon' => 'bi-bar-chart-line',
        'description' => 'Track net cash flow and your projected ending balance for the period.',
        'fields' => [
            ['name' => 'beginningBalance', 'label' => 'Beginning Cash Balance', 'default' => 10000],
            ['name' => 'totalInflows', 'label' => 'Total Cash Inflows', 'default' => 8000],
            ['name' => 'totalOutflows', 'label' => 'Total Cash Outflows', 'default' => 6000],
        ],
        'results' => [
            ['key' => 'netCashFlow', 'label' => 'Net Cash Flow', 'format' => 'currency'],
            ['key' => 'endingBalance', 'label' => 'Ending Cash Balance', 'format' => 'currency'],
        ],
    ],
    'business-loan' => [
        'name' => 'Business Loan Calculator',
        'category' => 'Investment & Returns',
        'icon' => 'bi-bank',
        'description' => 'Get your monthly payment, total interest, and a full downloadable amortization schedule.',
        'fields' => [
            ['name' => 'principal', 'label' => 'Loan Amount', 'default' => 20000],
            ['name' => 'annualRatePercent', 'label' => 'Annual Interest Rate (%)', 'default' => 8],
            ['name' => 'termMonths', 'label' => 'Loan Term (Months)', 'default' => 36, 'min' => 1],
        ],
        'results' => [
            ['key' => 'monthlyPayment', 'label' => 'Monthly Payment', 'format' => 'currency'],
            ['key' => 'totalInterest', 'label' => 'Total Interest', 'format' => 'currency'],
            ['key' => 'totalPayment', 'label' => 'Total of All Payments', 'format' => 'currency'],
        ],
        'hasAmortization' => true,
    ],
    'product-pricing' => [
        'name' => 'Product Pricing Calculator',
        'category' => 'Pricing & Costing',
        'icon' => 'bi-tags',
        'description' => 'Back into the selling price you need to hit a target profit margin.',
        'fields' => [
            ['name' => 'cost', 'label' => 'Unit Cost', 'default' => 40],
            ['name' => 'desiredMarginPercent', 'label' => 'Desired Margin (%)', 'default' => 30, 'max' => 99],
        ],
        'results' => [
            ['key' => 'price', 'label' => 'Selling Price', 'format' => 'currency'],
            ['key' => 'profitPerUnit', 'label' => 'Profit Per Unit', 'format' => 'currency'],
        ],
    ],
    'service-pricing' => [
        'name' => 'Service Pricing Calculator',
        'category' => 'Pricing & Costing',
        'icon' => 'bi-briefcase',
        'description' => 'Price a service project from your time cost, overhead, and target margin.',
        'fields' => [
            ['name' => 'hourlyCost', 'label' => 'Your Hourly Cost', 'default' => 25],
            ['name' => 'hoursRequired', 'label' => 'Hours Required', 'default' => 10],
            ['name' => 'overheadPerProject', 'label' => 'Overhead Per Project', 'default' => 50],
            ['name' => 'desiredMarginPercent', 'label' => 'Desired Margin (%)', 'default' => 30, 'max' => 99],
        ],
        'results' => [
            ['key' => 'baseCost', 'label' => 'Base Cost', 'format' => 'currency'],
            ['key' => 'price', 'label' => 'Project Price', 'format' => 'currency'],
            ['key' => 'profit', 'label' => 'Profit', 'format' => 'currency'],
        ],
    ],
    'cogs' => [
        'name' => 'COGS Calculator',
        'category' => 'Pricing & Costing',
        'icon' => 'bi-boxes',
        'description' => 'Calculate Cost of Goods Sold from beginning inventory, purchases, and ending inventory.',
        'fields' => [
            ['name' => 'beginningInventory', 'label' => 'Beginning Inventory', 'default' => 5000],
            ['name' => 'purchases', 'label' => 'Purchases', 'default' => 8000],
            ['name' => 'endingInventory', 'label' => 'Ending Inventory', 'default' => 4000],
        ],
        'results' => [
            ['key' => 'cogs', 'label' => 'Cost of Goods Sold', 'format' => 'currency'],
        ],
    ],
    'discount' => [
        'name' => 'Discount Calculator',
        'category' => 'Sales & Invoicing',
        'icon' => 'bi-percent',
        'description' => 'Calculate the discount amount and final price from a percentage off.',
        'fields' => [
            ['name' => 'originalPrice', 'label' => 'Original Price', 'default' => 100],
            ['name' => 'discountPercent', 'label' => 'Discount (%)', 'default' => 15],
        ],
        'results' => [
            ['key' => 'discountAmount', 'label' => 'Discount Amount', 'format' => 'currency'],
            ['key' => 'finalPrice', 'label' => 'Final Price', 'format' => 'currency'],
        ],
    ],
    'commission' => [
        'name' => 'Commission Calculator',
        'category' => 'Sales & Invoicing',
        'icon' => 'bi-person-check',
        'description' => 'Work out commission owed and net amount from a sale.',
        'fields' => [
            ['name' => 'saleAmount', 'label' => 'Sale Amount', 'default' => 2000],
            ['name' => 'commissionPercent', 'label' => 'Commission Rate (%)', 'default' => 10],
        ],
        'results' => [
            ['key' => 'commissionAmount', 'label' => 'Commission', 'format' => 'currency'],
            ['key' => 'netAmount', 'label' => 'Net Amount', 'format' => 'currency'],
        ],
    ],
    'freelancer-rate' => [
        'name' => 'Freelancer Rate Calculator',
        'category' => 'Sales & Invoicing',
        'icon' => 'bi-laptop',
        'description' => 'Find the hourly and day rate you need to charge to hit your income goal.',
        'fields' => [
            ['name' => 'desiredAnnualIncome', 'label' => 'Desired Annual Income', 'default' => 60000],
            ['name' => 'businessExpensesAnnual', 'label' => 'Annual Business Expenses', 'default' => 6000],
            ['name' => 'taxRatePercent', 'label' => 'Tax Rate (%)', 'default' => 25, 'max' => 99],
            ['name' => 'workWeeksPerYear', 'label' => 'Work Weeks Per Year', 'default' => 46],
            ['name' => 'billableHoursPerWeek', 'label' => 'Billable Hours Per Week', 'default' => 25],
        ],
        'results' => [
            ['key' => 'billableHoursPerYear', 'label' => 'Billable Hours / Year', 'format' => 'number'],
            ['key' => 'hourlyRate', 'label' => 'Hourly Rate', 'format' => 'currency'],
            ['key' => 'dayRate', 'label' => 'Day Rate (8h)', 'format' => 'currency'],
        ],
    ],
    'vat-gst' => [
        'name' => 'VAT/GST Calculator',
        'category' => 'Sales & Invoicing',
        'icon' => 'bi-receipt',
        'description' => 'Add or remove VAT/GST from an amount, exclusive or inclusive.',
        'fields' => [
            ['name' => 'amount', 'label' => 'Amount', 'default' => 1000],
            ['name' => 'vatRatePercent', 'label' => 'VAT/GST Rate (%)', 'default' => 20],
            ['name' => 'mode', 'label' => 'Amount Is', 'type' => 'select', 'options' => ['exclusive' => 'Exclusive of VAT', 'inclusive' => 'Inclusive of VAT'], 'default' => 'exclusive'],
        ],
        'results' => [
            ['key' => 'netAmount', 'label' => 'Net Amount', 'format' => 'currency'],
            ['key' => 'vatAmount', 'label' => 'VAT/GST Amount', 'format' => 'currency'],
            ['key' => 'totalAmount', 'label' => 'Total Amount', 'format' => 'currency'],
        ],
    ],
    'invoice-discount' => [
        'name' => 'Invoice Discount Calculator',
        'category' => 'Sales & Invoicing',
        'icon' => 'bi-file-earmark-text',
        'description' => 'Apply a discount and tax to an invoice subtotal to get the final total.',
        'fields' => [
            ['name' => 'subtotal', 'label' => 'Invoice Subtotal', 'default' => 1000],
            ['name' => 'discountPercent', 'label' => 'Discount (%)', 'default' => 10],
            ['name' => 'taxPercent', 'label' => 'Tax (%)', 'default' => 15],
        ],
        'results' => [
            ['key' => 'discountAmount', 'label' => 'Discount Amount', 'format' => 'currency'],
            ['key' => 'afterDiscount', 'label' => 'After Discount', 'format' => 'currency'],
            ['key' => 'taxAmount', 'label' => 'Tax Amount', 'format' => 'currency'],
            ['key' => 'total', 'label' => 'Invoice Total', 'format' => 'currency'],
        ],
    ],
    'startup-runway' => [
        'name' => 'Startup Runway Calculator',
        'category' => 'Investment & Returns',
        'icon' => 'bi-rocket-takeoff',
        'description' => 'Find out how many months of cash runway your startup has left.',
        'fields' => [
            ['name' => 'currentCashBalance', 'label' => 'Current Cash Balance', 'default' => 150000],
            ['name' => 'monthlyBurnRate', 'label' => 'Monthly Expenses', 'default' => 20000],
            ['name' => 'monthlyRevenue', 'label' => 'Monthly Revenue', 'default' => 5000],
        ],
        'results' => [
            ['key' => 'netBurn', 'label' => 'Net Monthly Burn', 'format' => 'currency'],
            ['key' => 'runwayMonths', 'label' => 'Runway', 'format' => 'months'],
        ],
    ],
];
