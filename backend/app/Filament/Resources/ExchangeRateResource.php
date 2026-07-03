<?php
namespace App\Filament\Resources;
use App\Filament\Resources\ExchangeRateResource\Pages;
use App\Models\ExchangeRate;
use Filament\Actions\DeleteAction; use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput; use Filament\Resources\Resource; use Filament\Schemas\Schema; use Filament\Tables\Columns\TextColumn; use Filament\Tables\Table;
class ExchangeRateResource extends Resource { protected static ?string $model = ExchangeRate::class; protected static ?string $navigationLabel = 'أسعار الصرف'; public static function form(Schema $schema): Schema { return $schema->components([TextInput::make('base')->required(), TextInput::make('quote')->required(), TextInput::make('rate')->numeric()->required()]); } public static function table(Table $table): Table { return $table->columns([TextColumn::make('base'), TextColumn::make('quote'), TextColumn::make('rate')])->recordActions([EditAction::make(), DeleteAction::make()]); } public static function getPages(): array { return ['index'=>Pages\ListExchangeRates::route('/'), 'create'=>Pages\CreateExchangeRate::route('/create'), 'edit'=>Pages\EditExchangeRate::route('/{record}/edit')]; } }
