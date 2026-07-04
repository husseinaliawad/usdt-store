<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NetworkResource\Pages;
use App\Models\Network;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NetworkResource extends Resource
{
    protected static ?string $model = Network::class;

    protected static ?string $navigationLabel = 'الشبكات';

    protected static ?string $modelLabel = 'شبكة';

    protected static ?string $pluralModelLabel = 'الشبكات';

    protected static string|\UnitEnum|null $navigationGroup = 'الإعدادات';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('الاسم')->required()->maxLength(120),
            TextInput::make('code')->label('الكود')->required()->maxLength(60),
            TextInput::make('withdraw_fee')->label('رسوم السحب')->numeric()->required(),
            TextInput::make('send_fee_percent')->label('نسبة رسوم الإرسال')->numeric()->required(),
            Toggle::make('is_active')->label('فعالة')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('الاسم')->searchable()->sortable(),
                TextColumn::make('code')->label('الكود')->searchable()->badge(),
                TextColumn::make('withdraw_fee')->label('رسوم السحب')->suffix(' USDT'),
                TextColumn::make('send_fee_percent')->label('نسبة الإرسال')->suffix('%'),
                IconColumn::make('is_active')->label('فعالة')->boolean(),
            ])
            ->recordActions([
                EditAction::make()->label('تعديل'),
                DeleteAction::make()->label('حذف'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNetworks::route('/'),
            'create' => Pages\CreateNetwork::route('/create'),
            'edit' => Pages\EditNetwork::route('/{record}/edit'),
        ];
    }
}
