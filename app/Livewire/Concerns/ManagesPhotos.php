<?php

namespace App\Livewire\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait ManagesPhotos
{
    protected int $maxPhotos = 8;

    /** @var array<int, TemporaryUploadedFile> */
    public array $photos = [];

    /** @var array<int, int> */
    public array $removedPhotoIds = [];

    abstract protected function photoSubject(): ?Model;

    public function removePhoto(int $mediaId): void
    {
        $this->removedPhotoIds[] = $mediaId;
        $this->removedPhotoIds = array_values(array_unique($this->removedPhotoIds));
    }

    public function removeNewPhoto(int $index): void
    {
        unset($this->photos[$index]);

        $this->photos = array_values($this->photos);
    }

    /**
     * @return array<string, mixed>
     */
    protected function photoRules(): array
    {
        return [
            'photos' => ['array', 'max:'.$this->maxPhotos, function (string $attribute, mixed $value, Closure $fail) {
                if ($this->photoCount() + count($value) > $this->maxPhotos) {
                    $fail('You can add up to '.$this->maxPhotos.' photos.');
                }
            }],
            'photos.*' => ['image', 'max:5120'],
        ];
    }

    protected function photoCount(): int
    {
        return $this->photoSubject()?->media()
            ->where('type', 'image')
            ->whereNotIn('id', $this->removedPhotoIds)
            ->count() ?? 0;
    }

    /**
     * Delete marked photos and store newly uploaded ones against the subject.
     */
    protected function syncPhotos(string $directory): void
    {
        $owner = $this->photoSubject();

        if (! $owner) {
            return;
        }

        if ($this->removedPhotoIds !== []) {
            $photos = $owner->media()
                ->where('type', 'image')
                ->whereKey($this->removedPhotoIds)
                ->get();

            foreach ($photos as $photo) {
                if ($photo->path) {
                    Storage::disk($photo->disk ?: 'public')->delete($photo->path);
                }

                $photo->delete();
            }

            $this->removedPhotoIds = [];
        }

        $nextSortOrder = (int) $owner->media()->where('type', 'image')->max('sort_order') + 1;

        foreach ($this->photos as $photo) {
            $owner->media()->create([
                'user_id' => auth()->id(),
                'type' => 'image',
                'disk' => 'public',
                'path' => $photo->store($directory, 'public'),
                'sort_order' => $nextSortOrder++,
            ]);
        }

        $this->photos = [];
    }
}
