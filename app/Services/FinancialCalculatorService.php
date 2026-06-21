<?php

namespace App\Services;

class FinancialCalculatorService
{
    public static function calculate(string $slug, array $input): array
    {
        return match ($slug) {
            'product-costing' => self::productCosting($input),
            'profit-margin' => self::profitMargin($input),
            'break-even' => self::breakEven($input),
            'roi' => self::roi($input),
            'roas' => self::roas($input),
            'cash-flow' => self::cashFlow($input),
            'business-loan' => self::businessLoan($input),
            'product-pricing' => self::productPricing($input),
            'service-pricing' => self::servicePricing($input),
            'cogs' => self::cogs($input),
            'manufacturing-cost' => self::manufacturingCost($input),
            'wholesale-price' => self::wholesalePrice($input),
            'retail-price' => self::retailPrice($input),
            'discount' => self::discount($input),
            'commission' => self::commission($input),
            'freelancer-rate' => self::freelancerRate($input),
            'hourly-rate' => self::hourlyRate($input),
            'vat-gst' => self::vatGst($input),
            'invoice-discount' => self::invoiceDiscount($input),
            'startup-runway' => self::startupRunway($input),
            default => throw new \InvalidArgumentException("Unknown calculator: {$slug}"),
        };
    }

    public static function amortizationSchedule(float $principal, float $annualRatePercent, int $termMonths): array
    {
        $termMonths = max(1, $termMonths);
        $monthlyRate = $annualRatePercent / 100 / 12;
        $payment = $monthlyRate > 0
            ? $principal * $monthlyRate / (1 - (1 + $monthlyRate) ** -$termMonths)
            : $principal / $termMonths;

        $balance = $principal;
        $schedule = [];

        for ($month = 1; $month <= $termMonths; $month++) {
            $interest = $balance * $monthlyRate;
            $principalPaid = $payment - $interest;

            if ($month === $termMonths) {
                $principalPaid = $balance;
                $payment = $principalPaid + $interest;
            }

            $balance -= $principalPaid;
            $balance = max(0, $balance);

            $schedule[] = [
                'month' => $month,
                'payment' => round($payment, 2),
                'principal' => round($principalPaid, 2),
                'interest' => round($interest, 2),
                'balance' => round($balance, 2),
            ];
        }

        return [
            'schedule' => $schedule,
            'totalInterest' => round(array_sum(array_column($schedule, 'interest')), 2),
        ];
    }

    private static function safeDiv(float $numerator, float $denominator): ?float
    {
        if (abs($denominator) < 0.0000001) {
            return null;
        }

        return $numerator / $denominator;
    }

    private static function productCosting(array $i): array
    {
        $total = $i['materialCost'] + $i['laborCost'] + $i['overheadCost'];
        $units = max(0.0, $i['unitsProduced']);

        return [
            'totalCost' => round($total, 2),
            'costPerUnit' => round(self::safeDiv($total, $units) ?? 0, 2),
        ];
    }

    private static function profitMargin(array $i): array
    {
        $profit = $i['revenue'] - $i['cost'];

        return [
            'profit' => round($profit, 2),
            'grossMarginPercent' => round((self::safeDiv($profit, $i['revenue']) ?? 0) * 100, 2),
            'markupPercent' => round((self::safeDiv($profit, $i['cost']) ?? 0) * 100, 2),
        ];
    }

    private static function breakEven(array $i): array
    {
        $contributionMargin = $i['pricePerUnit'] - $i['variableCostPerUnit'];
        $units = self::safeDiv($i['fixedCosts'], $contributionMargin);

        return [
            'contributionMargin' => round($contributionMargin, 2),
            'breakEvenUnits' => $units !== null ? round($units, 2) : null,
            'breakEvenRevenue' => $units !== null ? round($units * $i['pricePerUnit'], 2) : null,
        ];
    }

    private static function roi(array $i): array
    {
        $netProfit = $i['finalValue'] - $i['initialInvestment'];

        return [
            'netProfit' => round($netProfit, 2),
            'roiPercent' => round((self::safeDiv($netProfit, $i['initialInvestment']) ?? 0) * 100, 2),
        ];
    }

    private static function roas(array $i): array
    {
        $roas = self::safeDiv($i['revenueFromAds'], $i['adSpend']) ?? 0;

        return [
            'roas' => round($roas, 2),
            'roasPercent' => round($roas * 100, 2),
        ];
    }

    private static function cashFlow(array $i): array
    {
        $net = $i['totalInflows'] - $i['totalOutflows'];

        return [
            'netCashFlow' => round($net, 2),
            'endingBalance' => round($i['beginningBalance'] + $net, 2),
        ];
    }

    private static function businessLoan(array $i): array
    {
        $result = self::amortizationSchedule($i['principal'], $i['annualRatePercent'], (int) $i['termMonths']);
        $monthlyPayment = $result['schedule'][0]['payment'] ?? 0;
        $totalPayment = round(array_sum(array_column($result['schedule'], 'payment')), 2);

        return [
            'monthlyPayment' => round($monthlyPayment, 2),
            'totalInterest' => $result['totalInterest'],
            'totalPayment' => $totalPayment,
            'schedule' => $result['schedule'],
        ];
    }

    private static function productPricing(array $i): array
    {
        $margin = min(99.0, max(0.0, $i['desiredMarginPercent']));
        $price = self::safeDiv($i['cost'], 1 - $margin / 100) ?? $i['cost'];

        return [
            'price' => round($price, 2),
            'profitPerUnit' => round($price - $i['cost'], 2),
        ];
    }

    private static function servicePricing(array $i): array
    {
        $baseCost = $i['hourlyCost'] * $i['hoursRequired'] + $i['overheadPerProject'];
        $margin = min(99.0, max(0.0, $i['desiredMarginPercent']));
        $price = self::safeDiv($baseCost, 1 - $margin / 100) ?? $baseCost;

        return [
            'baseCost' => round($baseCost, 2),
            'price' => round($price, 2),
            'profit' => round($price - $baseCost, 2),
        ];
    }

    private static function cogs(array $i): array
    {
        return [
            'cogs' => round($i['beginningInventory'] + $i['purchases'] - $i['endingInventory'], 2),
        ];
    }

    private static function manufacturingCost(array $i): array
    {
        $total = $i['rawMaterialCost'] + $i['directLaborCost'] + $i['manufacturingOverhead'];
        $units = max(0.0, $i['unitsManufactured']);
        $costPerUnit = self::safeDiv($total, $units) ?? 0;
        $margin = min(99.0, max(0.0, $i['desiredMarginPercent']));
        $sellingPrice = self::safeDiv($costPerUnit, 1 - $margin / 100) ?? $costPerUnit;

        return [
            'totalManufacturingCost' => round($total, 2),
            'costPerUnit' => round($costPerUnit, 2),
            'suggestedSellingPrice' => round($sellingPrice, 2),
            'profitPerUnit' => round($sellingPrice - $costPerUnit, 2),
        ];
    }

    private static function wholesalePrice(array $i): array
    {
        $margin = min(99.0, max(0.0, $i['desiredMarginPercent']));
        $price = self::safeDiv($i['unitCost'], 1 - $margin / 100) ?? $i['unitCost'];
        $profitPerUnit = $price - $i['unitCost'];
        $qty = max(0.0, $i['orderQuantity']);

        return [
            'wholesalePricePerUnit' => round($price, 2),
            'profitPerUnit' => round($profitPerUnit, 2),
            'totalOrderRevenue' => round($price * $qty, 2),
            'totalOrderProfit' => round($profitPerUnit * $qty, 2),
        ];
    }

    private static function retailPrice(array $i): array
    {
        $markup = max(0.0, $i['desiredMarkupPercent']);
        $retail = $i['wholesaleCost'] * (1 + $markup / 100);
        $tax = $retail * $i['salesTaxPercent'] / 100;

        return [
            'retailPrice' => round($retail, 2),
            'profitPerUnit' => round($retail - $i['wholesaleCost'], 2),
            'priceWithTax' => round($retail + $tax, 2),
        ];
    }

    private static function discount(array $i): array
    {
        $discountAmount = $i['originalPrice'] * $i['discountPercent'] / 100;

        return [
            'discountAmount' => round($discountAmount, 2),
            'finalPrice' => round($i['originalPrice'] - $discountAmount, 2),
        ];
    }

    private static function commission(array $i): array
    {
        $commissionAmount = $i['saleAmount'] * $i['commissionPercent'] / 100;

        return [
            'commissionAmount' => round($commissionAmount, 2),
            'netAmount' => round($i['saleAmount'] - $commissionAmount, 2),
        ];
    }

    private static function freelancerRate(array $i): array
    {
        $totalNeeded = $i['desiredAnnualIncome'] + $i['businessExpensesAnnual'];
        $taxRate = min(99.0, max(0.0, $i['taxRatePercent']));
        $grossedUp = self::safeDiv($totalNeeded, 1 - $taxRate / 100) ?? $totalNeeded;
        $billableHours = $i['workWeeksPerYear'] * $i['billableHoursPerWeek'];
        $hourlyRate = self::safeDiv($grossedUp, $billableHours) ?? 0;

        return [
            'billableHoursPerYear' => round($billableHours, 2),
            'hourlyRate' => round($hourlyRate, 2),
            'dayRate' => round($hourlyRate * 8, 2),
        ];
    }

    private static function hourlyRate(array $i): array
    {
        $weeksPerMonth = 4.333;
        $weeklyHours = max(0.0, $i['hoursPerDay']) * max(0.0, $i['daysPerWeek']);
        $monthlyHours = $weeklyHours * $weeksPerMonth;
        $hourlyRate = self::safeDiv($i['desiredMonthlyIncome'], $monthlyHours) ?? 0;

        return [
            'hourlyRate' => round($hourlyRate, 2),
            'dayRate' => round($hourlyRate * $i['hoursPerDay'], 2),
            'weeklyHours' => round($weeklyHours, 2),
            'monthlyHours' => round($monthlyHours, 2),
        ];
    }

    private static function vatGst(array $i): array
    {
        $amount = $i['amount'];
        $rate = $i['vatRatePercent'] / 100;

        if (($i['mode'] ?? 'exclusive') === 'inclusive') {
            $netAmount = self::safeDiv($amount, 1 + $rate) ?? $amount;
            $vatAmount = $amount - $netAmount;
            $totalAmount = $amount;
        } else {
            $netAmount = $amount;
            $vatAmount = $amount * $rate;
            $totalAmount = $amount + $vatAmount;
        }

        return [
            'netAmount' => round($netAmount, 2),
            'vatAmount' => round($vatAmount, 2),
            'totalAmount' => round($totalAmount, 2),
        ];
    }

    private static function invoiceDiscount(array $i): array
    {
        $discountAmount = $i['subtotal'] * $i['discountPercent'] / 100;
        $afterDiscount = $i['subtotal'] - $discountAmount;
        $taxAmount = $afterDiscount * $i['taxPercent'] / 100;

        return [
            'discountAmount' => round($discountAmount, 2),
            'afterDiscount' => round($afterDiscount, 2),
            'taxAmount' => round($taxAmount, 2),
            'total' => round($afterDiscount + $taxAmount, 2),
        ];
    }

    private static function startupRunway(array $i): array
    {
        $netBurn = $i['monthlyBurnRate'] - $i['monthlyRevenue'];
        $runwayMonths = $netBurn > 0 ? round($i['currentCashBalance'] / $netBurn, 1) : null;

        return [
            'netBurn' => round($netBurn, 2),
            'runwayMonths' => $runwayMonths,
        ];
    }
}
