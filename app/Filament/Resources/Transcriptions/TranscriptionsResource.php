<?php

namespace App\Filament\Resources\Transcriptions;

use App\Filament\Resources\Transcriptions\Pages\CreateTranscriptions;
use App\Filament\Resources\Transcriptions\Pages\EditTranscriptions;
use App\Filament\Resources\Transcriptions\Pages\ListTranscriptions;
use App\Filament\Resources\Transcriptions\Schemas\TranscriptionsForm;
use App\Filament\Resources\Transcriptions\Tables\TranscriptionsTable;
use App\Models\Transcriptions;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TranscriptionsResource extends Resource
{
    protected static ?string $model = Transcriptions::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Transcriptions';

    public static function form(Schema $schema): Schema
    {
        return TranscriptionsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TranscriptionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTranscriptions::route('/'),
            'create' => CreateTranscriptions::route('/create'),
            'edit' => EditTranscriptions::route('/{record}/edit'),
        ];
    }
}
