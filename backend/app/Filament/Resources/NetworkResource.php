<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NetworkResource\Pages;
use App\Models\Network;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NetworkResource extends Resource
{
    protected static ?string $model = Network::class;
    protected static ?string $navigationLabel = 'الشبكات المدعومة';
    public static function form(Schema $schema): Schema { return $schema->components([TextInput::make('name')->label('الاسم')->required(), TextInput::make('code')->label('الكود')->required(), TextInput::make('withdraw_fee')->label('رسوم السحب')->numeric(), TextInput::make('send_fee_percent')->label('نسبة الإرسال')->numeric()]); }
    public static function table(Table $table): Table { return $table->columns([TextColumn::make('name')->label('الاسم'), TextColumn::make('code')->label('الكود'), TextColumn::make('withdraw_fee')->label('رسوم السحب'), TextColumn::make('send_fee_percent')->label('نسبة الإرسال')])->recordActions([EditAction::make(), DeleteAction::make()]); }
    public static function getPages(): array { return ['index' => Pages\ListNetworks::route('/'), 'create' => Pages\CreateNetwork::route('/create'), 'edit' => Pages\EditNetwork::route('/{record}/edit')]; }
}
