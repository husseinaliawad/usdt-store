<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppNotificationResource\Pages;
use App\Models\AppNotification;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AppNotificationResource extends Resource
{
    protected static ?string $model = AppNotification::class;

    protected static ?string $navigationLabel = 'الإشعارات';

    protected static ?string $modelLabel = 'إشعار';

    protected static ?string $pluralModelLabel = 'الإشعارات';

    protected static string|\UnitEnum|null $navigationGroup = 'إدارة المستخدمين';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')
                ->label('المستخدم')
                ->relationship('user', 'email')
                ->searchable()
                ->preload()
                ->nullable()
                ->helperText('اتركه فارغًا ليظهر الإشعار للجميع.'),
            TextInput::make('title')->label('العنوان')->required()->maxLength(255),
            Textarea::make('body')->label('النص')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('العنوان')->searchable(),
                TextColumn::make('user.email')->label('المستخدم')->placeholder('عام')->searchable(),
                TextColumn::make('body')->label('النص')->limit(50)->searchable(),
                TextColumn::make('read_at')->label('تاريخ القراءة')->dateTime()->placeholder('غير مقروء'),
                TextColumn::make('created_at')->dateTime()->label('التاريخ')->sortable(),
            ])
            ->filters([
                TernaryFilter::make('read_at')->label('مقروء')->nullable(),
            ])
            ->recordActions([
                EditAction::make()->label('تعديل'),
                DeleteAction::make()->label('حذف'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppNotifications::route('/'),
            'create' => Pages\CreateAppNotification::route('/create'),
            'edit' => Pages\EditAppNotification::route('/{record}/edit'),
        ];
    }
}
