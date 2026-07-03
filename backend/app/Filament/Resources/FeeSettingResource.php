<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeSettingResource\Pages;
use App\Models\FeeSetting;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FeeSettingResource extends Resource
{
    protected static ?string $model = FeeSetting::class;
    protected static ?string $navigationLabel = 'إدارة العمولات';
    public static function form(Schema $schema): Schema { return $schema->components([TextInput::make('name')->label('الاسم')->required(), Select::make('type')->label('النوع')->options(['send'=>'send','receive'=>'receive','deposit'=>'deposit','withdraw'=>'withdraw'])->required(), TextInput::make('fixed_fee')->label('رسوم ثابتة')->numeric(), TextInput::make('percent_fee')->label('نسبة')->numeric()]); }
    public static function table(Table $table): Table { return $table->columns([TextColumn::make('name')->label('الاسم'), TextColumn::make('type')->label('النوع'), TextColumn::make('fixed_fee')->label('ثابت'), TextColumn::make('percent_fee')->label('نسبة')])->recordActions([EditAction::make(), DeleteAction::make()]); }
    public static function getPages(): array { return ['index' => Pages\ListFeeSettings::route('/'), 'create' => Pages\CreateFeeSetting::route('/create'), 'edit' => Pages\EditFeeSetting::route('/{record}/edit')]; }
}
