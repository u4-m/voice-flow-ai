<?php

namespace App\Filament\Resources\Transcriptions\Schemas;

use App\Models\Transcriptions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Http\File as HttpFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class TranscriptionsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, $state) => $set('audio_file_path', null)),

                FileUpload::make('audio_file_path')
                    ->label('Audio File')
                    ->required()
                    ->acceptedFileTypes(['audio/*'])
                    ->maxSize(10240) // 10MB
                    ->directory('transcription-audios')
                    ->visibility('private')
                    ->preserveFilenames()
                    ->getUploadedFileNameForStorageUsing(
                        fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                            ->prepend(now()->timestamp . '_')
                    )
                    ->columnSpanFull()
                    ->helperText('Maximum file size: 10MB. Supported formats: mp3, wav, m4a, ogg')
                    ->hint('Large files will be processed in the background')
                    ->previewable()
                    ->downloadable()
                    ->openable(),

                Textarea::make('transcription')
                    ->label('Transcription')
                    ->nullable()
                    ->rows(5)
                    ->maxLength(65535)
                    ->hidden(fn (string $operation) => $operation !== 'edit'),

                Select::make('status')
                    ->options(Transcriptions::getStatusOptions())
                    ->default('pending')
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->in(Transcriptions::getStatusOptions())
                    ->hidden(fn (string $operation) => $operation !== 'edit'),
                    

                Textarea::make('error_message')
                    ->label('Error Details')
                    ->nullable()
                    ->rows(3)
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->hidden(fn (string $operation) => $operation !== 'edit'),
            ]);
    }
}
