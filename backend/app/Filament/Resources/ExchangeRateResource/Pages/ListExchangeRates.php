<?php
namespace App\Filament\Resources\ExchangeRateResource\Pages;
use App\Filament\Resources\ExchangeRateResource; use Filament\Actions\CreateAction; use Filament\Resources\Pages\ListRecords;
class ListExchangeRates extends ListRecords { protected static string $resource = ExchangeRateResource::class; protected function getHeaderActions(): array { return [CreateAction::make()]; } }
