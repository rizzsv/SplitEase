<?php

namespace App\Filament\Resources\SubscriptionGroupResource\Pages;

use App\Filament\Resources\SubscriptionGroupResource;
use App\Models\ProductSubscription;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateSubscriptionGroup extends CreateRecord
{
    protected static string $resource = SubscriptionGroupResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
{
    $trxId = 'TRX-' . strtoupper(Str::random(8));

    $subscription = ProductSubscription::create([
        'name' => $data['customer_name'],
        'email' => $data['email'],
        'phone' => $data['phone'],
        'product_id' => $data['product_id'],
        'duration' => $data['duration'],
        'price' => $data['price'],
        'total_amount' => $data['total_amount'],
        'total_tax_amount' => $data['total_tax_amount'],
        'booking_trx_id' => $trxId, // ✅ Diisi dari backend
        'proof' => $data['proof'],
        'customer_bank_name' => $data['customer_bank_name'],
        'customer_bank_account' => $data['customer_bank_account_name'],
        'customer_bank_number' => $data['customer_bank_account_number'],
        'is_paid' => $data['is_paid'] ?? false,
    ]);

    $data['product_subscription_id'] = $subscription->id;

    return $data;
}
}
