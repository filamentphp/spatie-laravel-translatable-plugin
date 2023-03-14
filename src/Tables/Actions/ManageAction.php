<?php

namespace Filament\Tables\Actions;

use Filament\Forms\ComponentContainer;
use Filament\Tables\Actions\EditAction;
use Illuminate\Database\Eloquent\Model;

class ManageAction extends EditAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mountUsing(function (ComponentContainer $form, Model $record): void {
            $livewire = $form->getLivewire();
            $record->setLocale($livewire->activeLocale);

            $data = $this->getLocalizedRecordAttributes($record);

            if ($this->mutateRecordDataUsing) {
                $data = $this->evaluate($this->mutateRecordDataUsing, ['data' => $data]);
            }

            $form->fill($data);
        });
    }

    /**
     * Get localized attributes from record
     *
     * @param  Model $record
     * @return array
     */
    protected function getLocalizedRecordAttributes(Model $record): array
    {
        $attributes = collect($record->attributesToArray());

        $attributes = $attributes->map(function ($value, $key) use ($record) {
            return $record->{$key};
        });

        return $attributes->toArray();
    }
}
