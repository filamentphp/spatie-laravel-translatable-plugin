<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Forms\Components\Contracts\HasValidationRules;

trait HasTranslatableValidation
{
    /**
     * Set the validation rules for the active locale.
     */
    public function setLocaleByRules(string $locale): void
    {
        $components = array_filter(
            $this->form->getFlatComponents(),
            fn ($component) => $component instanceof HasValidationRules
        );

        foreach ($components as $component) {
            $rules = $component->getValidationRules();
            if (! empty($rules[$locale])) {
                $component->rule($rules[$locale]);
                break;
            }
        }
    }
}
