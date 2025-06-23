<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Product';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('tagline')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\FileUpload::make('thumbnail')
                            ->image()
                            ->required(),

                        Forms\Components\FileUpload::make('photo')
                            ->image()
                            ->required(),

                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->live()
                            ->afterStateUpdated((function (callable $get, callable $set) {
                                $price = $get('price');
                                $capacity = $get('capacity');

                                if ($capacity > 0) {
                                    $set('price_per_person', $price / $capacity);
                                } else {
                                    $set('price_per_person', null);
                                }
                            })),

                        Forms\Components\TextInput::make('capacity')
                            ->required()
                            ->numeric()
                            ->prefix('People')
                            ->live()
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                $price = $get('price');
                                $capacity = $get('capacity');

                                if ($capacity > 0) {
                                    $set('price_per_person', $price / $capacity);
                                } else {
                                    $set('price_per_person', null);
                                }
                            }),

                        Forms\Components\TextInput::make('price_per_person')
                            ->required()
                            ->readOnly()
                            ->prefix('IDR')
                            ->afterStateHydrated(function (callable $get, callable $set) {
                                $price = $get('price');
                                $capacity = $get('capacity');
                                if ($capacity > 0) {
                                    $set('price_per_person', $price / $capacity);
                                } else {
                                    $set('price_per_person', null);
                                }
                            }),

                        Forms\Components\TextInput::make('duration')
                            ->required()
                            ->numeric()
                            ->prefix('Month')
                    ]),

                Fieldset::make('Additional')
                    ->schema([
                        Forms\Components\Textarea::make('about')
                            ->required(),

                        Forms\Components\Repeater::make('keypoints')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                            ]),

                        Forms\Components\Select::make('is_popular')
                            ->options([
                                true => 'Popular',
                                false => 'Not Popular'
                            ])
                            ->required()
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail'),

                Tables\Columns\TextColumn::make('name')
                ->searchable(),

                Tables\Columns\IconColumn::make('is_popular')
                ->boolean()
                ->trueColor('success')
                ->falseColor('danger')
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->label('Popular'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
