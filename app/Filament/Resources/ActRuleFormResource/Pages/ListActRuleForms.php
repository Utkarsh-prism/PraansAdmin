<?php

namespace App\Filament\Resources\ActRuleFormResource\Pages;

use App\Filament\Resources\ActRuleFormResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActRuleForms extends ListRecords
{
    protected static string $resource = ActRuleFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
