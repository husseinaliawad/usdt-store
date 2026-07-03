<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;
    protected static ?string $navigationLabel = 'إدارة العمليات';
    protected static ?string $modelLabel = 'عملية';
    protected static ?string $pluralModelLabel = 'العمليات';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')->label('المستخدم')->relationship('user', 'phone')->required(),
            Select::make('network_id')->label('الشبكة')->relationship('network', 'name'),
            Select::make('type')->label('النوع')->options(['send' => 'إرسال', 'receive' => 'استلام', 'deposit' => 'إيداع', 'withdraw' => 'سحب'])->required(),
            Select::make('status')->label('الحالة')->options(['pending' => 'pending', 'approved' => 'approved', 'rejected' => 'rejected', 'completed' => 'completed', 'failed' => 'failed'])->required(),
            TextInput::make('amount')->label('المبلغ')->numeric()->required(),
            TextInput::make('fee')->label('الرسوم')->numeric(),
            TextInput::make('txid')->label('TxID'),
            Textarea::make('note')->label('ملاحظة'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->label('#')->sortable(),
            TextColumn::make('user.phone')->label('المستخدم')->searchable(),
            TextColumn::make('type')->label('النوع')->badge(),
            TextColumn::make('amount')->label('المبلغ')->suffix(' USDT')->sortable(),
            TextColumn::make('fee')->label('الرسوم')->suffix(' USDT'),
            TextColumn::make('status')->label('الحالة')->badge(),
            TextColumn::make('created_at')->label('التاريخ')->dateTime(),
        ])->filters([
            SelectFilter::make('status')->options(['pending' => 'pending', 'approved' => 'approved', 'rejected' => 'rejected', 'completed' => 'completed', 'failed' => 'failed']),
            SelectFilter::make('type')->options(['send' => 'send', 'receive' => 'receive', 'deposit' => 'deposit', 'withdraw' => 'withdraw']),
        ])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListTransactions::route('/'), 'edit' => Pages\EditTransaction::route('/{record}/edit')];
    }
}
