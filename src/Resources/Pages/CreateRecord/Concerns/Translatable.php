<?php

namespace Filament\Resources\Pages\CreateRecord\Concerns;

use Filament\Facades\Filament;
use Filament\Resources\Concerns\HasActiveLocaleSwitcher;
use Filament\Resources\Pages\Concerns\HasTranslatableValidation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;

trait Translatable
{
    use HasActiveLocaleSwitcher;
    use HasTranslatableValidation;

    protected ?string $oldActiveLocale = null;

    #[Locked]
    public $otherLocaleData = [];

    public function mountTranslatable(): void
    {
        $this->activeLocale = static::getResource()::getDefaultTranslatableLocale();
    }

    public function getTranslatableLocales(): array
    {
        return static::getResource()::getTranslatableLocales();
    }

    public function beforeFill()
    {
        /**
         * For the rules assigned to the locales to work properly,
         * an empty form state must be created for each local.
         */
        if (empty($this->otherLocaleData)) {
            $components = $this->form->getComponents();
            $formData = collect($components)
                ->mapWithKeys(
                    fn ($component) => [
                        $component->getName() => null,
                    ])
                ->toArray();
            foreach ($this->getTranslatableLocales() as $locale) {
                $this->otherLocaleData[$locale] = [];

                $this->otherLocaleData[$locale] = $formData;
            }
        }
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = app(static::getModel());

        $translatableAttributes = static::getResource()::getTranslatableAttributes();

        $record->fill(Arr::except($data, $translatableAttributes));

        foreach (Arr::only($data, $translatableAttributes) as $key => $value) {
            $record->setTranslation($key, $this->activeLocale, $value);
        }

        $originalData = $this->data;

        /**
         * Set the data for the active locale.
         */
        $this->otherLocaleData[$this->activeLocale] = Arr::only($this->data, $translatableAttributes);

        foreach ($this->otherLocaleData as $locale => $localeData) {
            /**
             * Set the validation rules for the active locale.
             */
            $this->setLocaleByRules($locale);

            $this->data = [
                ...$this->data,
                ...$localeData,
            ];

            try {
                $this->form->validate();
            } catch (ValidationException $exception) {
                /**
                 * If the validation fails for the active locale, set the active locale
                 */
                $this->activeLocale = $locale;

                throw $exception;
            }

            $localeData = $this->mutateFormDataBeforeCreate($localeData);

            foreach (Arr::only($localeData, $translatableAttributes) as $key => $value) {
                $record->setTranslation($key, $locale, $value);
            }
        }

        $this->data = $originalData;

        if (
            static::getResource()::isScopedToTenant() &&
            ($tenant = Filament::getTenant())
        ) {
            return $this->associateRecordWithTenant($record, $tenant);
        }

        $record->save();

        return $record;
    }

    public function updatingActiveLocale(): void
    {
        $this->oldActiveLocale = $this->activeLocale;
    }

    public function updatedActiveLocale(string $newActiveLocale): void
    {
        if (blank($this->oldActiveLocale)) {
            return;
        }

        $this->resetValidation();

        $translatableAttributes = static::getResource()::getTranslatableAttributes();

        $this->otherLocaleData[$this->oldActiveLocale] = Arr::only($this->data, $translatableAttributes);

        $this->data = [
            ...Arr::except($this->data, $translatableAttributes),
            ...$this->otherLocaleData[$this->activeLocale] ?? [],
        ];

        unset($this->otherLocaleData[$this->activeLocale]);
    }
}
