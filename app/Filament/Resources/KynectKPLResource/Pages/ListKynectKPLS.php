<?php

namespace App\Filament\Resources\KynectKPLResource\Pages;

use App\Filament\Resources\KynectKPLResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKynectKPLS extends ListRecords
{
    protected static string $resource = KynectKPLResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
