<?php

namespace App\Http\Requests\CropsFeed;

class FeedReportRequest extends BaseCropsFeedRequest
{
    public function rules(): array
    {
        return [
            'month' => ['nullable', 'date_format:Y-m'],
        ];
    }
}
