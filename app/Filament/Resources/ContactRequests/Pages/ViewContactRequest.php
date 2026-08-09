<?php

namespace App\Filament\Resources\ContactRequests\Pages;

use App\Filament\Resources\ContactRequests\ContactRequestResource;
use App\Models\ContactRequest;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewContactRequest extends ViewRecord
{
    protected static string $resource = ContactRequestResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        /** @var ContactRequest $contactRequest */
        $contactRequest = $this->getRecord();

        if ($contactRequest->read_at === null) {
            $contactRequest->forceFill(['read_at' => now()])->save();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('mark_unread')
                ->label('Merk som ulest')
                ->icon('heroicon-o-envelope')
                ->action(function (): void {
                    /** @var ContactRequest $contactRequest */
                    $contactRequest = $this->getRecord();

                    $contactRequest->forceFill(['read_at' => null])->save();

                    $this->redirect(ContactRequestResource::getUrl());
                }),
            DeleteAction::make(),
        ];
    }
}
