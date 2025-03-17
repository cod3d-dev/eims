<?php

namespace App\Filament\Resources\KynectKPLResource\Pages;

use App\Filament\Resources\KynectKPLResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKynectKPL extends EditRecord
{
    protected static string $resource = KynectKPLResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
