<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationLabel = 'إدارة المستخدمين';
    protected static ?string $modelLabel = 'مستخدم';
    protected static ?string $pluralModelLabel = 'المستخدمون';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('الاسم')->required(),
            TextInput::make('phone')->label('الهاتف')->required(),
            TextInput::make('email')->label('البريد'),
            Select::make('role')->label('الصلاحية')->options(['admin' => 'Admin', 'user' => 'User'])->required(),
            Select::make('kyc_status')->label('حالة KYC')->options(['not_submitted' => 'غير مقدم', 'pending' => 'قيد المراجعة', 'approved' => 'مقبول', 'rejected' => 'مرفوض'])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('الاسم')->searchable(),
            TextColumn::make('phone')->label('الهاتف')->searchable(),
            TextColumn::make('role')->label('الصلاحية'),
            TextColumn::make('kyc_status')->label('KYC')->badge(),
            TextColumn::make('created_at')->label('التاريخ')->dateTime(),
        ])->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListUsers::route('/'), 'create' => Pages\CreateUser::route('/create'), 'edit' => Pages\EditUser::route('/{record}/edit')];
    }
}
