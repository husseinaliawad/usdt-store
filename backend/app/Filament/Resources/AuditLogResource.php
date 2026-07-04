<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationLabel = 'سجل التدقيق';

    protected static ?string $modelLabel = 'سجل';

    protected static ?string $pluralModelLabel = 'سجل التدقيق';

    protected static string|\UnitEnum|null $navigationGroup = 'النظام';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.email')->label('الأدمن')->placeholder('النظام')->searchable(),
                TextColumn::make('action')->label('الإجراء')->searchable()->badge(),
                TextColumn::make('auditable_type')->label('النوع')->toggleable(),
                TextColumn::make('auditable_id')->label('ID')->toggleable(),
                TextColumn::make('ip')->label('IP')->toggleable(),
                TextColumn::make('created_at')->dateTime()->label('التاريخ')->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
        ];
    }
}
