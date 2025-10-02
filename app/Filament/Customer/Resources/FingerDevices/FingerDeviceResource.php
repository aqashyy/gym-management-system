<?php

namespace App\Filament\Customer\Resources\FingerDevices;

use App\Filament\Customer\Resources\FingerDevices\Pages\CreateFingerDevice;
use App\Filament\Customer\Resources\FingerDevices\Pages\EditFingerDevice;
use App\Filament\Customer\Resources\FingerDevices\Pages\ListFingerDevices;
use App\Filament\Customer\Resources\FingerDevices\Schemas\FingerDeviceForm;
use App\Filament\Customer\Resources\FingerDevices\Tables\FingerDevicesTable;
use App\Models\FingerDevice;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FingerDeviceResource extends Resource
{
    protected static ?string $model = FingerDevice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::FingerPrint;

    protected static ?int $navigationSort = 10;
    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();
        // dd($user->Customer->id);
        return parent::getEloquentQuery()->where('customer_id',$user->Customer->id);
    }
    public static function form(Schema $schema): Schema
    {
        return FingerDeviceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FingerDevicesTable::configure($table);
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
            'index' => ListFingerDevices::route('/'),
            // 'create' => CreateFingerDevice::route('/create'),
            // 'edit' => EditFingerDevice::route('/{record}/edit'),
        ];
    }
}
