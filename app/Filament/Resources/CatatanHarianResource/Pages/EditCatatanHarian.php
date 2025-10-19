<?php

namespace App\Filament\Resources\CatatanHarianResource\Pages;

use App\Filament\Resources\CatatanHarianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCatatanHarian extends EditRecord
{
    protected static string $resource = CatatanHarianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
