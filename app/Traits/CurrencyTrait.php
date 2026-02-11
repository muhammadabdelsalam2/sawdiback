<?php

namespace App\Traits;

use App\Models\Currency;

trait CurrencyTrait
{
    //

    public function currentCurrency()
    {
        return session('currency', Currency::defaultCurrency());
    }

    public function convertCurrency($amount)
    {
        $currency = $this->currentCurrency();
        return round($amount * $currency->rate, 2);
    }

    public function formatCurrency($amount)
    {
        $currency = $this->currentCurrency();
        return $currency->symbol . ' ' . number_format($this->convertCurrency($amount), 2);
    }
}
