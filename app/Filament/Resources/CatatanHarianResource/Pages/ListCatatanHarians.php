<?php

namespace App\Filament\Resources\CatatanHarianResource\Pages;

use App\Filament\Resources\CatatanHarianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCatatanHarians extends ListRecords
{
    protected static string $resource = CatatanHarianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
