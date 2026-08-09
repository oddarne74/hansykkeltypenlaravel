<?php

namespace App\Filament\Resources\ServiceRequests\Pages;

use App\Enums\ServiceStatus;
use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Mail\ServiceStatusUpdated;
use App\Models\ServiceRequest;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Mail;

class ViewServiceRequest extends ViewRecord
{
    protected static string $resource = ServiceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Godkjenn')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->visible(fn (): bool => $this->getRecord()->status !== ServiceStatus::APPROVED)
                ->requiresConfirmation()
                ->action(function (): void {
                    /** @var ServiceRequest $record */
                    $record = $this->getRecord();
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
                ->visible(fn (): bool => $this->getRecord()->status !== ServiceStatus::DENIED)
                ->requiresConfirmation()
                ->action(function (): void {
                    /** @var ServiceRequest $record */
                    $record = $this->getRecord();
                    $record->update(['status' => ServiceStatus::DENIED]);

                    Mail::to($record->email)->queue(new ServiceStatusUpdated($record));

                    Notification::make()
                        ->title('Serviceforespørsel avslått')
                        ->danger()
                        ->send();
                }),
            DeleteAction::make(),
        ];
    }
}
