<?php

namespace App\Filament\Resources\WaMessageTemplates;

use App\Filament\Resources\WaMessageTemplates\Pages\CreateWaMessageTemplate;
use App\Filament\Resources\WaMessageTemplates\Pages\EditWaMessageTemplate;
use App\Filament\Resources\WaMessageTemplates\Pages\ListWaMessageTemplates;
use App\Filament\Resources\WaMessageTemplates\Schemas\WaMessageTemplateForm;
use App\Filament\Resources\WaMessageTemplates\Tables\WaMessageTemplatesTable;
use App\Models\WaMessageTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WaMessageTemplateResource extends Resource
{
    protected static ?string $model = WaMessageTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

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
