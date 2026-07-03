<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KycVerificationResource\Pages;
use App\Models\KycVerification;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KycVerificationResource extends Resource
{
    protected static ?string $model = KycVerification::class;
    protected static ?string $navigationLabel = 'طلبات توثيق الهوية';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('status')->label('الحالة')->options(['pending' => 'pending', 'approved' => 'approved', 'rejected' => 'rejected'])->required(),
            Textarea::make('admin_note')->label('ملاحظة الإدارة'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('user.phone')->label('المستخدم'),
            TextColumn::make('full_name')->label('الاسم'),
            TextColumn::make('phone')->label('الهاتف'),
            TextColumn::make('status')->label('الحالة')->badge(),
            TextColumn::make('created_at')->dateTime()->label('التاريخ'),
        ])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array { return ['index' => Pages\ListKycVerifications::route('/'), 'edit' => Pages\EditKycVerification::route('/{record}/edit')]; }
}
