<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('view')
                ->url(fn($record) => OrderResource::getUrl('view', ['record' => $record]))
                ->color('info'),
        ];
    }

    /**
     * The "status" Select on this form allows picking "cancelled" (it's a
     * valid transition per Order::STATUS_TRANSITIONS), but a plain
     * $record->update($data) would skip stock restoration entirely. Route
     * a cancellation through cancelAndRestoreStock(), the single source of
     * truth also used by the "Cancel Order" button on the View page, so
     * this form can't cancel an order without putting its stock back.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Order $record */
        $becomingCancelled = ($data['status'] ?? $record->status) === 'cancelled'
            && $record->status !== 'cancelled';

        if ($becomingCancelled) {
            unset($data['status']);
        }

        DB::transaction(function () use ($record, $data, $becomingCancelled) {
            $record->update($data);

            if ($becomingCancelled) {
                $record->cancelAndRestoreStock();
            }
        });

        return $record;
    }
}
