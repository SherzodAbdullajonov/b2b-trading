<?php

declare(strict_types=1);

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class RemainingStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
        ];
    }

    public function validationData(): array
    {
        return [
            'date' => $this->query('date'),
        ];
    }

    public function asOfDate(): Carbon
    {
        $raw  = (string) $this->query('date');
        $date = Carbon::parse($raw);

        if (!preg_match('/\d{1,2}:\d{2}/', $raw)) {
            $date = $date->endOfDay();
        }

        return $date;
    }
}
