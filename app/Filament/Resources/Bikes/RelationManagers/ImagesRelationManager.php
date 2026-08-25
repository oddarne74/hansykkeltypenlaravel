<?php

namespace App\Filament\Resources\Bikes\RelationManagers;

use App\Models\Bike;
use App\Models\BikeImage;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'Bilder';

    protected static ?string $modelLabel = 'bilde';

    protected static ?string $pluralModelLabel = 'bilder';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('path')
                    ->label('Bildefil')
                    ->image()
                    ->disk('local')
                    ->directory(fn (): string => 'originals/bikes/'.$this->getOwnerRecord()->getAttribute('slug'))
                    ->maxSize(5120)
                    ->required()
                    ->helperText('Bildet lagres privat. En optimalisert miniatyr- og storversjon genereres automatisk og vises på nettsiden.'),
                TextInput::make('alt')
                    ->label('Alternativ tekst (for skjermlesere og SEO)')
                    ->placeholder('F.eks. Trek 7200 sett fra siden')
                    ->required()
                    ->maxLength(190),
                Select::make('stage')
                    ->label('Stadium')
                    ->options([
                        'after' => 'Etter oppussing',
                        'before' => 'Før oppussing',
                    ])
                    ->default('after')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('alt')
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('thumbnail_path')
                    ->label('Bilde')
                    ->disk('public')
                    ->getStateUsing(fn (BikeImage $record): string => $record->thumbnail_path ?? $record->path),
                TextColumn::make('alt')
                    ->label('Alternativ tekst')
                    ->searchable(),
                TextColumn::make('stage')
                    ->label('Stadium')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'after' ? 'Etter' : 'Før')
                    ->color(fn (string $state): string => $state === 'after' ? 'success' : 'gray'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        /** @var Bike $bike */
                        $bike = $this->getOwnerRecord();

                        $data['sort_order'] = ((int) $bike->images()->max('sort_order')) + 1;

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
