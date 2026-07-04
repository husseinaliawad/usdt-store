<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExchangeRateResource\Pages;
use App\Models\ExchangeRate;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExchangeRateResource extends Resource
{
    protected static ?string $model = ExchangeRate::class;

    protected static ?string $navigationLabel = 'أسعار الصرف';

    protected static ?string $modelLabel = 'سعر صرف';

    protected static ?string $pluralModelLabel = 'أسعار الصرف';

    protected static string|\UnitEnum|null $navigationGroup = 'الإعدادات';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('base')->label('العملة الأساسية')->required()->default('USDT'),
            TextInput::make('quote')->label('عملة التسعير')->required()->default('USD'),
            TextInput::make('rate')->label('السعر')->numeric()->required()->default(1),
            Toggle::make('is_active')->label('فعال')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('base')->label('الأساس')->searchable(),
                TextColumn::make('quote')->label('مقابل')->searchable(),
                TextColumn::make('rate')->label('السعر')->sortable(),
                IconColumn::make('is_active')->label('فعال')->boolean(),
                TextColumn::make('updated_at')->label('آخر تعديل')->dateTime()->sortable(),
            ])
            ->recordActions([
                EditAction::make()->label('تعديل'),
                DeleteAction::make()->label('حذف'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExchangeRates::route('/'),
            'create' => Pages\CreateExchangeRate::route('/create'),
            'edit' => Pages\EditExchangeRate::route('/{record}/edit'),
        ];
    }
}
