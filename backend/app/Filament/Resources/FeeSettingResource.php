<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeSettingResource\Pages;
use App\Models\FeeSetting;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FeeSettingResource extends Resource
{
    protected static ?string $model = FeeSetting::class;

    protected static ?string $navigationLabel = 'العمولات';

    protected static ?string $modelLabel = 'عمولة';

    protected static ?string $pluralModelLabel = 'العمولات';

    protected static string|\UnitEnum|null $navigationGroup = 'الإعدادات';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('الاسم')->required()->maxLength(120),
            Select::make('type')
                ->label('النوع')
                ->options([
                    'send' => 'إرسال',
                    'receive' => 'استلام',
                    'deposit' => 'إيداع',
                    'withdraw' => 'سحب',
                ])
                ->required(),
            TextInput::make('fixed_fee')->label('رسوم ثابتة')->numeric()->required(),
            TextInput::make('percent_fee')->label('نسبة مئوية')->numeric()->required(),
            Toggle::make('is_active')->label('فعالة')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('الاسم')->searchable(),
                TextColumn::make('type')->label('النوع')->badge(),
                TextColumn::make('fixed_fee')->label('ثابت')->suffix(' USDT'),
                TextColumn::make('percent_fee')->label('نسبة')->suffix('%'),
                IconColumn::make('is_active')->label('فعالة')->boolean(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('النوع')
                    ->options([
                        'send' => 'إرسال',
                        'receive' => 'استلام',
                        'deposit' => 'إيداع',
                        'withdraw' => 'سحب',
                    ]),
            ])
            ->recordActions([
                EditAction::make()->label('تعديل'),
                DeleteAction::make()->label('حذف'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeeSettings::route('/'),
            'create' => Pages\CreateFeeSetting::route('/create'),
            'edit' => Pages\EditFeeSetting::route('/{record}/edit'),
        ];
    }
}
