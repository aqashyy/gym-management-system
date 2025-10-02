<?php

namespace App\Filament\Customer\Resources\Payments;

use App\Filament\Customer\Resources\Payments\Pages\CreatePayment;
use App\Filament\Customer\Resources\Payments\Pages\EditPayment;
use App\Filament\Customer\Resources\Payments\Pages\ListPayments;
use App\Filament\Customer\Resources\Payments\Pages\ViewPayment;
use App\Filament\Customer\Resources\Payments\Schemas\PaymentForm;
use App\Filament\Customer\Resources\Payments\Schemas\PaymentInfolist;
use App\Filament\Customer\Resources\Payments\Tables\PaymentsTable;
use App\Models\Payment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationLabel = 'Transactions';
    protected static ?string $label = 'Transactions';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::CreditCard;

    public static function form(Schema $schema): Schema
    {
        return PaymentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PaymentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentsTable::configure($table);
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
            'index' => ListPayments::route('/'),
            // 'create' => CreatePayment::route('/create'),
            // 'view' => ViewPayment::route('/{record}'),
            // 'edit' => EditPayment::route('/{record}/edit'),
        ];
    }
}
