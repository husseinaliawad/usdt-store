<?php
namespace App\Filament\Resources\AppNotificationResource\Pages;
use App\Filament\Resources\AppNotificationResource; use Filament\Actions\CreateAction; use Filament\Resources\Pages\ListRecords;
class ListAppNotifications extends ListRecords { protected static string $resource = AppNotificationResource::class; protected function getHeaderActions(): array { return [CreateAction::make()]; } }
