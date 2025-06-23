<?php

namespace App\Filament\Resources\GroubMessageResource\Pages;

use App\Filament\Resources\GroubMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGroubMessages extends ListRecords
{
    protected static string $resource = GroubMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
