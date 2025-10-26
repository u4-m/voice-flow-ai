<?php

namespace App\Filament\Resources\Transcriptions\Tables;

use App\Models\Transcriptions;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class TranscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(\App\Models\Transcriptions::query()->where('user_id', auth()->id()))
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Transcriptions::STATUS_COMPLETED => 'success',
                        Transcriptions::STATUS_PROCESSING => 'warning',
                        Transcriptions::STATUS_FAILED => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Date Added')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        Transcriptions::STATUS_COMPLETED => 'Completed',
                        Transcriptions::STATUS_PROCESSING => 'Processing',
                        Transcriptions::STATUS_FAILED => 'Failed',
                    ])
                    ->label('Status'),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('From Date'),
                        DatePicker::make('created_until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->recordActions([
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => route('transcriptions.download', $record))
                    ->visible(fn ($record) => $record->status === Transcriptions::STATUS_COMPLETED),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
