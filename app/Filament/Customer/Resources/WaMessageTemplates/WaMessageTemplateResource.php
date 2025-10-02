<?php

namespace App\Filament\Customer\Resources\WaMessageTemplates;

use App\Filament\Customer\Resources\WaMessageTemplates\Pages\CreateWaMessageTemplate;
use App\Filament\Customer\Resources\WaMessageTemplates\Pages\EditWaMessageTemplate;
use App\Filament\Customer\Resources\WaMessageTemplates\Pages\ListWaMessageTemplates;
use App\Filament\Customer\Resources\WaMessageTemplates\Schemas\WaMessageTemplateForm;
use App\Filament\Customer\Resources\WaMessageTemplates\Tables\WaMessageTemplatesTable;
use App\Models\WaMessageTemplate;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WaMessageTemplateResource extends Resource
{
    protected static ?string $model = WaMessageTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();
        // dd($user->Customer->id);
        return parent::getEloquentQuery()->where('customer_id',$user->Customer->id);
    }
    public static function form(Schema $schema): Schema
    {
        return WaMessageTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WaMessageTemplatesTable::configure($table);
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
            'index' => ListWaMessageTemplates::route('/'),
            // 'create' => CreateWaMessageTemplate::route('/create'),
            // 'edit' => EditWaMessageTemplate::route('/{record}/edit'),
        ];
    }
}
