<?php

namespace Filament\Tables\Actions;

use Filament\Forms\ComponentContainer;
use Filament\Tables\Actions\EditAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

class ManageAction extends EditAction
{
    public static function getDefaultName(): ?string
    {
        return 'manage';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->mountUsing(function (ComponentContainer $form, Model $record): void {
            $resource = $form->getLivewire();
            $record->setLocale($resource->activeLocale);

            $data = $this->getRecordAttributes($record);

            if ($this->mutateRecordDataUsing) {
                $data = $this->evaluate($this->mutateRecordDataUsing, ['data' => $data]);
            }

            $form->fill($data);
        });

        $this->action(function (): void {
            $this->process(function (array $data, Model $record) {
                $resource = $this->getLivewire();
                $record->setLocale($resource->activeLocale);

                $relationship = $this->getRelationship();
                if ($relationship instanceof BelongsToMany) {
                    $pivotColumns = $relationship->getPivotColumns();
                    $pivotData = Arr::only($data, $pivotColumns);

                    if (count($pivotColumns)) {
                        $record->{$relationship->getPivotAccessor()}->update($pivotData);
                    }

                    $data = Arr::except($data, $pivotColumns);
                }

                $record->update($data);
            });

            $this->success();
        });
    }

    /**
     * Get localized attributes from record
     *
     * @param  Model $record
     * @return array
     */
    protected function getRecordAttributes(Model $record): array
    {
        $attributes = collect($record->attributesToArray());

        $attributes = $attributes->map(function ($value, $key) use ($record) {
            return $record->{$key};
        });

        return $attributes->toArray();
    }
}
