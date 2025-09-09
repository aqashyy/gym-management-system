<?php

namespace App\Filament\Customer\Resources\Members;

use App\Filament\Customer\Resources\Members\Pages\CreateMember;
use App\Filament\Customer\Resources\Members\Pages\EditMember;
use App\Filament\Customer\Resources\Members\Pages\ListMembers;
use App\Filament\Customer\Resources\Members\Pages\ViewMember;
use App\Filament\Customer\Resources\Members\Schemas\MemberForm;
use App\Filament\Customer\Resources\Members\Schemas\MemberInfolist;
use App\Filament\Customer\Resources\Members\Tables\MembersTable;
use App\Filament\Customer\Resources\Members\Widgets\MemberOverview;
use App\Models\Member;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();
        // dd($user->Customer->id);
        return parent::getEloquentQuery()->where('customer_id',$user->Customer->id);
    }
    public static function form(Schema $schema): Schema
    {
        return MemberForm::configure($schema);
    }

    // public static function getWidgets(): array
    // {
    //     return [
    //         MemberOverview::class
    //     ];
    // }

    public static function infolist(Schema $schema): Schema
    {
        return MemberInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MembersTable::configure($table);
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
            'index' => ListMembers::route('/'),
            // 'create' => CreateMember::route('/create'),
            // 'view' => ViewMember::route('/{record}'),
            // 'edit' => EditMember::route('/{record}/edit'),
        ];
    }
}
