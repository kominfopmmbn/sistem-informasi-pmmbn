<?php

namespace Tests\Unit;

use App\Rules\QuillContentNotEmpty;
use PHPUnit\Framework\TestCase;

class QuillContentNotEmptyTest extends TestCase
{
    public function test_fails_when_html_has_no_visible_text(): void
    {
        $rule = new QuillContentNotEmpty;
        $failed = false;
        $rule->validate('content', '<p></p><br>', function () use (&$failed): void {
            $failed = true;
        });
        $this->assertTrue($failed);
    }

    public function test_fails_when_text_is_only_whitespace(): void
    {
        $rule = new QuillContentNotEmpty;
        $failed = false;
        $rule->validate('content', " \t  \n", function () use (&$failed): void {
            $failed = true;
        });
        $this->assertTrue($failed);
    }

    public function test_passes_when_paragraph_has_text(): void
    {
        $rule = new QuillContentNotEmpty;
        $failed = false;
        $rule->validate('content', '<p>Ada isi</p>', function () use (&$failed): void {
            $failed = true;
        });
        $this->assertFalse($failed);
    }
}
