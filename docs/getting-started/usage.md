---
title: Usage
weight: 4
---

## Making resource pages translatable

After [preparing your resource class](#preparing-your-resource-class), you must make each of your resource's pages translatable too. You can find your resource's pages in the `Pages` directory of each resource folder. To prepare a page, you must apply the corresponding `Translatable` trait to it, and install a `LocaleSwitcher` header action:

```php
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBlogPosts extends ListRecords
{
    use ListRecords\Concerns\Translatable;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            // ...
        ];
    }
    
    // ...
}
```

```php
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogPost extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            // ...
        ];
    }
    
    // ...
}
```

```php
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlogPost extends EditRecord
{
    use EditRecord\Concerns\Translatable;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            // ...
        ];
    }
    
    // ...
}
```

And if you have a `ViewRecord` page for your resource:

```php
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBlogPost extends ViewRecord
{
    use ViewRecord\Concerns\Translatable;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            // ...
        ];
    }
    
    // ...
}
```

If you're using a simple resource, you can make the `ManageRecords` page translatable instead:

```php
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBlogPosts extends ListRecords
{
    use ManageRecords\Concerns\Translatable;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            // ...
        ];
    }
    
    // ...
}
```

### Setting the translatable locales for a particular resource

By default, the translatable locales can be [set globally for all resources in the plugin configuration](#setting-the-default-translatable-locales). Alternatively, you can customize the translatable locales for a particular resource by overriding the `getTranslatableLocales()` method in your resource class:

```php
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;

class BlogPostResource extends Resource
{
    use Translatable;
    
    // ...
    
    public static function getTranslatableLocales(): array
    {
        return ['en', 'fr'];
    }
}
```

## Translating relation managers

First, you must apply the `Filament\Resources\RelationManagers\Concerns\Translatable` trait to the relation manager class:

```php
use Filament\Resources\RelationManagers\Concerns\Translatable;
use Filament\Resources\RelationManagers\RelationManager;

class BlogPostsRelationManager extends RelationManager
{
    use Translatable;
    
    // ...
}
```

Now, you can add a new `LocaleSwitcher` action to the header of the relation manager's `table()`:

```php
use Filament\Tables;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->headerActions([
            // ...
            Tables\Actions\LocaleSwitcher::make(),
        ]);
}
```

### Inheriting the relation manager's active locale from the resource page

If you wish to reactively inherit the locale of the `Translatable` resource page that the relation manager is being displayed on, you can override the `$activeLocale` property and add Livewire's `Reactive` attribute to it:

```php
use Filament\Resources\RelationManagers\Concerns\Translatable;
use Filament\Resources\RelationManagers\RelationManager;
use Livewire\Attributes\Reactive;

class BlogPostsRelationManager extends RelationManager
{
    use Translatable;
    
    #[Reactive]
    public ?string $activeLocale = null;
    
    // ...
}
```

If you do this, you no longer need a `LocaleSwitcher` action in the `table()`.

### Setting the translatable locales for a particular relation manager

By default, the translatable locales can be [set globally for all relation managers in the plugin configuration](#setting-the-default-translatable-locales). Alternatively, you can customize the translatable locales for a particular relation manager by overriding the `getTranslatableLocales()` method in your relation manager class:

```php
use Filament\Resources\RelationManagers\Concerns\Translatable;
use Filament\Resources\RelationManagers\RelationManager;

class BlogPostsRelationManager extends RelationManager
{
    use Translatable;
    
    // ...
    
    public function getTranslatableLocales(): array
    {
        return ['en', 'fr'];
    }
}
```

## Publishing translations

If you wish to translate the package, you may publish the language files using:

```bash
php artisan vendor:publish --tag=filament-spatie-laravel-translatable-plugin-translations
```
