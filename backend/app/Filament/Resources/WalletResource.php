<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletResource\Pages;
use App\Models\Wallet;
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

class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    protected static ?string $navigationLabel = 'المحافظ';

    protected static ?string $modelLabel = 'محفظة';

    protected static ?string $pluralModelLabel = 'المحافظ';

    protected static string|\UnitEnum|null $navigationGroup = 'العمليات المالية';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')
                ->label('المستخدم')
                ->relationship('user', 'email')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('network_id')
                ->label('الشبكة')
                ->relationship('network', 'name')
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('address')->label('عنوان المحفظة')->required()->maxLength(255),
            TextInput::make('balance')->label('الرصيد')->numeric()->required()->default(0),
            Toggle::make('is_primary')->label('محفظة أساسية')->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.email')->label('البريد')->searchable()->sortable(),
                TextColumn::make('user.phone')->label('الهاتف')->searchable()->toggleable(),
                TextColumn::make('network.code')->label('الشبكة')->badge()->sortable(),
                TextColumn::make('address')->label('العنوان')->searchable()->limit(28)->copyable(),
                TextColumn::make('balance')->label('الرصيد')->suffix(' USDT')->sortable(),
                IconColumn::make('is_primary')->label('أساسية')->boolean(),
                TextColumn::make('updated_at')->label('آخر تعديل')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('network_id')
                    ->label('الشبكة')
                    ->relationship('network', 'name'),
            ])
            ->recordActions([
                EditAction::make()->label('تعديل'),
                DeleteAction::make()->label('حذف'),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWallets::route('/'),
            'create' => Pages\CreateWallet::route('/create'),
            'edit' => Pages\EditWallet::route('/{record}/edit'),
        ];
    }
}
