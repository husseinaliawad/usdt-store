<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'المستخدمون';

    protected static ?string $modelLabel = 'مستخدم';

    protected static ?string $pluralModelLabel = 'المستخدمون';

    protected static string|\UnitEnum|null $navigationGroup = 'إدارة المستخدمين';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('الاسم')->required()->maxLength(120),
            TextInput::make('email')->label('البريد')->email()->required()->maxLength(255),
            TextInput::make('phone')->label('الهاتف')->required()->maxLength(30),
            TextInput::make('password')
                ->label('كلمة المرور')
                ->password()
                ->revealable()
                ->minLength(8)
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->helperText('اتركها فارغة عند التعديل إذا لم ترد تغييرها.'),
            Select::make('role')
                ->label('الصلاحية')
                ->options(['admin' => 'مدير', 'user' => 'مستخدم'])
                ->required(),
            Select::make('kyc_status')
                ->label('حالة KYC')
                ->options([
                    'not_submitted' => 'غير مقدم',
                    'pending' => 'قيد المراجعة',
                    'approved' => 'مقبول',
                    'rejected' => 'مرفوض',
                ])
                ->required(),
            Toggle::make('is_active')->label('الحساب فعال')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('الاسم')->searchable()->sortable(),
                TextColumn::make('email')->label('البريد')->searchable()->sortable(),
                TextColumn::make('phone')->label('الهاتف')->searchable(),
                TextColumn::make('role')
                    ->label('الصلاحية')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'admin' ? 'مدير' : 'مستخدم'),
                TextColumn::make('kyc_status')
                    ->label('KYC')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'not_submitted' => 'غير مقدم',
                        'pending' => 'قيد المراجعة',
                        'approved' => 'مقبول',
                        'rejected' => 'مرفوض',
                        default => $state,
                    }),
                IconColumn::make('is_active')->label('فعال')->boolean(),
                TextColumn::make('created_at')->label('التاريخ')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('الصلاحية')
                    ->options(['admin' => 'مدير', 'user' => 'مستخدم']),
                SelectFilter::make('kyc_status')
                    ->label('KYC')
                    ->options([
                        'not_submitted' => 'غير مقدم',
                        'pending' => 'قيد المراجعة',
                        'approved' => 'مقبول',
                        'rejected' => 'مرفوض',
                    ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
