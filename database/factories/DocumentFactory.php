<?php

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * @var class-string<Document>
     */
    protected $model = Document::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => rtrim(fake()->sentence(fake()->numberBetween(4, 8)), '.'),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Document $document): void {
            $filename = Str::lower(fake()->lexify('???????')).'-'.uniqid('', true).'.pdf';
            $file = UploadedFile::fake()->create($filename, 120, 'application/pdf');
            $document->addMedia($file)->toMediaCollection(Document::FILE_COLLECTION);
        });
    }
}
