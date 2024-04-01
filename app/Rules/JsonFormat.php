<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 *  自定义规则
 *  命令行: php artisan make:rule JsonFormat
 *  用法: 'answers' => ['required', 'json', new JsonFormat],
 */
class JsonFormat implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $data = json_decode($value, true);
        if (!$data) {
            return false;
        }

        foreach ($data as $item) {
            if (!array_key_exists('exam_id', $item)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The validation error message.';
    }
}
