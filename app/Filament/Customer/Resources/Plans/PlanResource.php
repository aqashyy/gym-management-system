<?php

namespace App\Filament\Customer\Resources\Plans;

use App\Filament\Customer\Resources\Plans\Pages\CreatePlan;
use App\Filament\Customer\Resources\Plans\Pages\EditPlan;
use App\Filament\Customer\Resources\Plans\Pages\ListPlans;
use App\Filament\Customer\Resources\Plans\Schemas\PlanForm;
use App\Filament\Customer\Resources\Plans\Tables\PlansTable;
use App\Models\Plan;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();
        // dd($user->Customer->id);
        return parent::getEloquentQuery()->where('customer_id',$user->Customer->id);
    }
    
    public static function form(Schema $schema): Schema
    {
        return PlanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlansTable::configure($table);
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
            'index' => ListPlans::route('/'),
            // 'create' => CreatePlan::route('/create'),
            // 'edit' => EditPlan::route('/{record}/edit'),
        ];
    }
}
