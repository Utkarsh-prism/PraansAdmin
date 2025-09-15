<?php

namespace App\Filament\Resources\GazetteNortificationResource\Pages;

use App\Filament\Resources\GazetteNortificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGazetteNortification extends EditRecord
{
    protected static string $resource = GazetteNortificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
