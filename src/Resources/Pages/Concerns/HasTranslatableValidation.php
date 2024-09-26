<?php

namespace Filament\Resources\Pages\Concerns;

trait HasTranslatableValidation
{
    /**
     * Set the validation rules for the active locale.
     */
    public function setLocaleByRules(string $locale): void
    {
        $components = $this->form->getComponents();

        foreach ($components as $component) {
            $rules = $component->getValidationRules();
            if (! empty($rules[$locale])) {
                $component->rule($rules[$locale]);
                break;
            }
        }
    }
}
