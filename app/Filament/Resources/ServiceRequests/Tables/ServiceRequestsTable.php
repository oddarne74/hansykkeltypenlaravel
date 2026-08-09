<?php

namespace App\Filament\Resources\ServiceRequests\Tables;

use App\Enums\ServiceStatus;
use App\Enums\ServiceType;
use App\Mail\ServiceStatusUpdated;
use App\Models\ServiceRequest;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class ServiceRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (ServiceStatus|string|null $state): string => $state instanceof ServiceStatus ? $state->color() : 'gray')
                    ->formatStateUsing(fn (ServiceStatus|string|null $state): string => $state instanceof ServiceStatus ? $state->value : (string) $state),
                TextColumn::make('name')
                    ->label('Navn')
                    ->description(fn (ServiceRequest $record): string => $record->email.($record->phone ? ' · '.$record->phone : ''))
                    ->searchable(['name', 'email', 'phone'])
                    ->sortable(),
                TextColumn::make('service_type')
                    ->label('Tjeneste')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (ServiceType|string|null $state): string => $state instanceof ServiceType ? $state->labelWithPrice() : (string) $state),
                TextColumn::make('week_start')
                    ->label('Uke')
                    ->state(fn (ServiceRequest $record): string => $record->week_start ? sprintf('Uke %d (%s)', Carbon::parse($record->week_start)->isoWeek(), Carbon::parse($record->week_start)->format('d.m.Y')) : '-')
                    ->sortable(),
                IconColumn::make('wants_pickup')
                    ->label('Henting')
                    ->boolean(),
                TextColumn::make('message')
                    ->label('Kommentar')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Mottatt')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(ServiceStatus::options()),
                SelectFilter::make('service_type')
                    ->label('Tjeneste')
                    ->options(ServiceType::options()),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('approve')
                    ->label('Godkjenn')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (ServiceRequest $record): bool => $record->status !== ServiceStatus::APPROVED)
                    ->requiresConfirmation()
                    ->action(function (ServiceRequest $record): void {
                        $record->update(['status' => ServiceStatus::APPROVED]);

                        Mail::to($record->email)->queue(new ServiceStatusUpdated($record));

                        Notification::make()
                            ->title('Serviceforespørsel godkjent')
                            ->success()
                            ->send();
                    }),
                Action::make('deny')
                    ->label('Avslå')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn (ServiceRequest $record): bool => $record->status !== ServiceStatus::DENIED)
                    ->requiresConfirmation()
                    ->action(function (ServiceRequest $record): void {
                        $record->update(['status' => ServiceStatus::DENIED]);

                        Mail::to($record->email)->queue(new ServiceStatusUpdated($record));

                        Notification::make()
                            ->title('Serviceforespørsel avslått')
                            ->danger()
                            ->send();
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
