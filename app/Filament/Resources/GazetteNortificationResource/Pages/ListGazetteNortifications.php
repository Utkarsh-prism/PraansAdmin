<?php

namespace App\Filament\Resources\GazetteNortificationResource\Pages;

use App\Filament\Resources\GazetteNortificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGazetteNortifications extends ListRecords
{
    protected static string $resource = GazetteNortificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
