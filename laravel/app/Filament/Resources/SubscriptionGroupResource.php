<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionGroupResource\Pages;
use App\Filament\Resources\SubscriptionGroupResource\RelationManagers;
use App\Models\Product;
use App\Models\ProductSubscription;
use App\Models\SubscriptionGroup;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriptionGroupResource extends Resource
{
    protected static ?string $model = SubscriptionGroup::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Transaction';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Product and Price')
                        ->schema([
                            Grid::make(2)->schema([
                                Forms\Components\Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $product = Product::find($state);
                                        $price = $product?->price_per_person ?? 0;
                                        $duration = $product?->duration ?? 0;

                                        $tax = 0.11;
                                        $totalTaxAmount = $tax * $price;
                                        $totalAmount = $price + $totalTaxAmount;

                                        $set('price', $price);
                                        $set('duration', $duration);
                                        $set('total_amount', number_format($totalAmount, 0, '', ''));
                                        $set('total_tax_amount', number_format($totalTaxAmount, 0, '', ''));
                                    })
                                    ->afterStateHydrated(function (callable $get, callable $set, $state) {
                                        $product = Product::find($state);
                                        $price = $product?->price_per_person ?? 0;

                                        $tax = 0.11;
                                        $totalTaxAmount = $tax * $price;
                                        $totalAmount = $price + $totalTaxAmount;

                                        $set('price', $price);
                                        $set('total_amount', number_format($totalAmount, 0, '', ''));
                                        $set('total_tax_amount', number_format($totalTaxAmount, 0, '', ''));
                                    }),

                                Forms\Components\TextInput::make('price')
                                    ->label('Price per person')
                                    ->required()
                                    ->readOnly()
                                    ->numeric()
                                    ->prefix('IDR'),

                                Forms\Components\TextInput::make('total_amount')
                                    ->required()
                                    ->readOnly()
                                    ->numeric()
                                    ->prefix('IDR'),

                                Forms\Components\TextInput::make('total_tax_amount')
                                    ->required()
                                    ->readOnly()
                                    ->numeric()
                                    ->prefix('IDR'),

                                Forms\Components\TextInput::make('duration')
                                    ->required()
                                    ->readOnly()
                                    ->numeric()
                                    ->prefix('month'),

                                Forms\Components\TextInput::make('max_capacity')
                                    ->readOnly()
                                    ->numeric()
                                    ->prefix('Max Capacity'),

                                Forms\Components\TextInput::make('participant_count')
                                    ->readOnly()
                                    ->numeric()
                                    ->prefix('Participant Count'),
                            ]),
                        ]),

                    Forms\Components\Wizard\Step::make('Customer Information')
                        ->schema([
                            Grid::make(2)->schema([
                                Forms\Components\TextInput::make('customer_name')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('phone')
                                    ->required()
                                    ->maxLength(225),

                                Forms\Components\TextInput::make('email')
                                    ->required()
                                    ->maxLength(225),
                            ]),
                        ]),

                    Forms\Components\Wizard\Step::make('Payment Information')
                        ->schema([

                            Forms\Components\TextInput::make('booking_trx_id')
                                ->label('Booking Transaction ID')
                                ->disabled() // Supaya user tidak ubah
                                ->default('Akan di-generate otomatis')
                                ->dehydrated(false), // Tidak dikirim ke backend


                            Forms\Components\TextInput::make('customer_bank_name')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('customer_bank_account_number')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('customer_bank_account_name')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\ToggleButtons::make('is_paid')
                                ->label('Paid')
                                ->boolean()
                                ->grouped()
                                ->icons([
                                    true => 'heroicon-o-check-circle',
                                    false => 'heroicon-o-x-circle',
                                ])
                                ->required(),

                            Forms\Components\FileUpload::make('proof')
                                ->required()
                                ->image(),
                        ]),
                ])
                    ->columnSpan('full')
                    ->columns(1)
                    ->skippable()
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('product.thumbnail'),

                Tables\Columns\TextColumn::make('product.name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('productSubscription.booking_trx_id')
                    ->label('Booking trx id')
                    ->searchable(),

                Tables\Columns\IconColumn::make('productSubscription.is_paid')->boolean()
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->label('Terverifikasi'),
            ])
            ->filters([
                SelectFilter::make('product_id')
                    ->label('Product')
                    ->relationship('product', 'name'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->action(function (SubscriptionGroup $record) {
                        if ($record->productSubscription) {
                            $record->productSubscription->is_paid = true;
                            $record->productSubscription->save();

                            Notification::make()
                                ->title('Order Approved')
                                ->success()
                                ->body('The order has been successfully approved.')
                                ->send();
                        }
                    })

                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(SubscriptionGroup $record) => !$record->productSubscription?->is_paid),
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
            'index' => Pages\ListSubscriptionGroups::route('/'),
            'create' => Pages\CreateSubscriptionGroup::route('/create'),
            'edit' => Pages\EditSubscriptionGroup::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with(['productSubscription']);
    }
}
