<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use App\Services\AdminTransactionService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use RuntimeException;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationLabel = 'إدارة العمليات';

    protected static ?string $modelLabel = 'عملية';

    protected static ?string $pluralModelLabel = 'العمليات';

    protected static string|\UnitEnum|null $navigationGroup = 'العمليات المالية';

    protected static ?int $navigationSort = 1;

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
                ->preload(),
            Select::make('type')
                ->label('النوع')
                ->options([
                    'send' => 'إرسال',
                    'receive' => 'استلام',
                    'deposit' => 'إيداع',
                    'withdraw' => 'سحب',
                ])
                ->required(),
            Select::make('status')
                ->label('الحالة')
                ->options([
                    'pending' => 'قيد المراجعة',
                    'approved' => 'موافق عليه',
                    'rejected' => 'مرفوض',
                    'completed' => 'مكتمل',
                    'failed' => 'فشل',
                ])
                ->required(),
            TextInput::make('amount')->label('المبلغ')->numeric()->required(),
            TextInput::make('fee')->label('الرسوم')->numeric(),
            TextInput::make('wallet_address')->label('عنوان المحفظة'),
            TextInput::make('withdraw_method')->label('طريقة السحب'),
            TextInput::make('txid')->label('TxID'),
            Textarea::make('recipient_payload')
                ->label('بيانات المستلم')
                ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : $state)
                ->dehydrateStateUsing(fn ($state) => filled($state) ? json_decode($state, true) : null),
            Textarea::make('note')->label('ملاحظة الإدارة'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('user.email')->label('البريد')->searchable()->sortable(),
                TextColumn::make('user.phone')->label('الهاتف')->searchable()->toggleable(),
                TextColumn::make('network.code')->label('الشبكة')->badge(),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'send' => 'إرسال',
                        'receive' => 'استلام',
                        'deposit' => 'إيداع',
                        'withdraw' => 'سحب',
                        default => $state,
                    }),
                TextColumn::make('amount')->label('المبلغ')->suffix(' USDT')->sortable(),
                TextColumn::make('fee')->label('الرسوم')->suffix(' USDT')->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'قيد المراجعة',
                        'approved' => 'موافق عليه',
                        'rejected' => 'مرفوض',
                        'completed' => 'مكتمل',
                        'failed' => 'فشل',
                        default => $state,
                    }),
                TextColumn::make('txid')->label('TxID')->searchable()->toggleable(),
                TextColumn::make('created_at')->label('التاريخ')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'pending' => 'قيد المراجعة',
                        'completed' => 'مكتمل',
                        'rejected' => 'مرفوض',
                        'failed' => 'فشل',
                    ]),
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
                Action::make('approve')
                    ->label('قبول')
                    ->color('success')
                    ->icon('heroicon-m-check-circle')
                    ->visible(fn (Transaction $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Transaction $record): void {
                        try {
                            app(AdminTransactionService::class)->approve($record);
                            Notification::make()->title('تم قبول العملية')->success()->send();
                        } catch (RuntimeException $exception) {
                            Notification::make()->title($exception->getMessage())->danger()->send();
                        }
                    }),
                Action::make('reject')
                    ->label('رفض')
                    ->color('danger')
                    ->icon('heroicon-m-x-circle')
                    ->visible(fn (Transaction $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('note')->label('سبب الرفض')->required(),
                    ])
                    ->action(function (Transaction $record, array $data): void {
                        app(AdminTransactionService::class)->reject($record, $data['note'] ?? null);
                        Notification::make()->title('تم رفض العملية')->success()->send();
                    }),
                EditAction::make()->label('تعديل'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
