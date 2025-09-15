<?php

namespace App\Filament\Resources\ActRuleFormResource\Pages;

use App\Filament\Resources\ActRuleFormResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActRuleForm extends EditRecord
{
    protected static string $resource = ActRuleFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
