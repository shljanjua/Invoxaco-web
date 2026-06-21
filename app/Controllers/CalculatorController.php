<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\FinancialCalculatorService;
use App\Services\PdfService;
use App\Services\SeoService;

class CalculatorController extends Controller
{
    public function index(): void
    {
        $calculators = config('calculators');
        $grouped = [];
        foreach ($calculators as $slug => $def) {
            $grouped[$def['category']][$slug] = $def;
        }

        $this->view('calculators/index', [
            'metaTitle' => '16+ Free Financial Calculators - Invoxaco',
            'metaDescription' => 'Free, real-time financial calculators for pricing, costing, ROI, loans, runway, and more. Download results as PDF.',
            'grouped' => $grouped,
            'jsonLd' => [SeoService::breadcrumbSchema([
                ['name' => 'Home', 'url' => url()],
                ['name' => 'Calculators', 'url' => url('calculators')],
            ])],
        ]);
    }

    public function show(string $slug): void
    {
        $def = config('calculators.' . $slug);

        if (!$def) {
            Response::abort(404, 'Calculator not found');
        }

        $user = auth_user();

        $this->view('calculators/show', [
            'metaTitle' => $def['name'] . ' - Invoxaco',
            'metaDescription' => $def['description'],
            'slug' => $slug,
            'def' => $def,
            'currency' => $user['currency'] ?? 'USD',
        ]);
    }

    public function calculate(string $slug): void
    {
        $def = config('calculators.' . $slug);

        if (!$def) {
            $this->json(['ok' => false, 'message' => 'Unknown calculator'], 404);
        }

        $input = $this->collectInput($def);
        $values = FinancialCalculatorService::calculate($slug, $input);

        $this->json(['ok' => true, 'values' => $values]);
    }

    public function pdf(string $slug): void
    {
        $def = config('calculators.' . $slug);

        if (!$def) {
            Response::abort(404, 'Calculator not found');
        }

        $input = $this->collectInput($def);
        $values = FinancialCalculatorService::calculate($slug, $input);
        $currency = Request::string('currency', 'USD') ?: 'USD';

        $html = \App\Core\View::renderRaw('calculators/pdf', [
            'def' => $def,
            'slug' => $slug,
            'input' => $input,
            'values' => $values,
            'currency' => $currency,
            'generatedAt' => date('M j, Y, g:i a'),
        ]);

        PdfService::fromHtml($html, $slug . '-result.pdf');
    }

    private function collectInput(array $def): array
    {
        $input = [];
        foreach ($def['fields'] as $field) {
            if (($field['type'] ?? 'number') === 'select') {
                $options = array_keys($field['options'] ?? []);
                $value = Request::string($field['name'], (string) $field['default']);
                $input[$field['name']] = in_array($value, $options, true) ? $value : $field['default'];
                continue;
            }

            $input[$field['name']] = (float) Request::input($field['name'], $field['default']);
        }

        return $input;
    }
}
