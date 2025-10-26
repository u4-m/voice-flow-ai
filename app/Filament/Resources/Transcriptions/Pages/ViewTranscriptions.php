<?php

namespace App\Filament\Resources\Transcriptions\Pages;

use App\Filament\Resources\Transcriptions\TranscriptionsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTranscriptions extends ViewRecord
{
    protected static string $resource = TranscriptionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
