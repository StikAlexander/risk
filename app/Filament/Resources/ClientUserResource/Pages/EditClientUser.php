<?php

namespace App\Filament\Resources\ClientUserResource\Pages;

use App\Filament\Resources\ClientUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClientUser extends EditRecord
{
    protected static string $resource = ClientUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
