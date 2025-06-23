<?php

namespace App\Filament\Resources\GroubMessageResource\Pages;

use App\Filament\Resources\GroubMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGroubMessage extends EditRecord
{
    protected static string $resource = GroubMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
