<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class QuillContentNotEmpty implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $html = (string) $value;
        $text = trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $text = trim(preg_replace("/[\p{Z}\s]+/u", ' ', $text) ?? '');

        if ($text === '') {
            $fail('Isi konten wajib diisi jika menerbitkan artikel.');
        }
    }
}
