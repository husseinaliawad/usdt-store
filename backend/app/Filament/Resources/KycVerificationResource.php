<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KycVerificationResource\Pages;
use App\Models\KycVerification;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class KycVerificationResource extends Resource
{
    protected static ?string $model = KycVerification::class;

    protected static ?string $navigationLabel = 'توثيق الهوية KYC';

    protected static ?string $modelLabel = 'طلب توثيق';

    protected static ?string $pluralModelLabel = 'طلبات التوثيق';

    protected static string|\UnitEnum|null $navigationGroup = 'إدارة المستخدمين';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('status')
                ->label('الحالة')
                ->options([
                    'pending' => 'قيد المراجعة',
                    'approved' => 'مقبول',
                    'rejected' => 'مرفوض',
                ])
                ->required(),
            Textarea::make('admin_note')->label('ملاحظة الإدارة'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.email')->label('البريد')->searchable(),
                TextColumn::make('user.phone')->label('هاتف الحساب')->searchable()->toggleable(),
                TextColumn::make('full_name')->label('الاسم')->searchable(),
                TextColumn::make('phone')->label('الهاتف')->searchable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'قيد المراجعة',
                        'approved' => 'مقبول',
                        'rejected' => 'مرفوض',
                        default => $state,
                    }),
                TextColumn::make('created_at')->dateTime()->label('التاريخ')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'pending' => 'قيد المراجعة',
                        'approved' => 'مقبول',
                        'rejected' => 'مرفوض',
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('قبول')
                    ->color('success')
                    ->icon('heroicon-m-check-circle')
                    ->visible(fn (KycVerification $record): bool => $record->status !== 'approved')
                    ->requiresConfirmation()
                    ->action(function (KycVerification $record): void {
                        $record->update([
                            'status' => 'approved',
                            'reviewed_at' => now(),
                        ]);
                        $record->user?->update(['kyc_status' => 'approved']);
                        Notification::make()->title('تم قبول التوثيق')->success()->send();
                    }),
                Action::make('reject')
                    ->label('رفض')
                    ->color('danger')
                    ->icon('heroicon-m-x-circle')
                    ->visible(fn (KycVerification $record): bool => $record->status !== 'rejected')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('admin_note')->label('سبب الرفض')->required(),
                    ])
                    ->action(function (KycVerification $record, array $data): void {
                        $record->update([
                            'status' => 'rejected',
                            'admin_note' => $data['admin_note'] ?? null,
                            'reviewed_at' => now(),
                        ]);
                        $record->user?->update(['kyc_status' => 'rejected']);
                        Notification::make()->title('تم رفض التوثيق')->success()->send();
                    }),
                EditAction::make()->label('تعديل'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKycVerifications::route('/'),
            'edit' => Pages\EditKycVerification::route('/{record}/edit'),
        ];
    }
}
