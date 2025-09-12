<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use ReflectionClass;
use Spatie\LaravelData\Optional;

class BaseAPIRequest extends FormRequest
{
    public function __get($key)
    {
        $docs = (string) (new ReflectionClass($this))->getDocComment();

        preg_match(
            pattern: '/@property\s+(\S+)\s+\$'.$key.'/',
            subject: $docs,
            matches: $matches
        );

        $match = $matches[1] ?? null;

        $types = explode(separator: '|', string: $match);

        if (! in_array(needle: 'None', haystack: $types)) {
            return parent::__get(key: $key);
        }

        return $this->get(key: $key, default: new Optional);
    }
}
