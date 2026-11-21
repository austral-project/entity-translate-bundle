# Upgrade from 3.1 to 4.0

## Requirements

- PHP 8.4 minimum
- Symfony 7.4
- Doctrine ORM 3

## Update the dependencies

```diff
- "austral/entity-translate-bundle": "^3.1"
+ "austral/entity-translate-bundle": "^4.0"
```

All `austral/*` packages must be upgraded to `^4.0` at the same time.

## Breaking changes

### Translate interfaces moved to `austral/contracts`

The interfaces of `Austral\EntityTranslateBundle\Entity\Interfaces` are removed, without alias.

Before:

```php
use Austral\EntityTranslateBundle\Entity\Interfaces\TranslateMasterInterface;
use Austral\EntityTranslateBundle\Entity\Interfaces\TranslateChildInterface;
```

After:

```php
use Austral\Contracts\Translate\TranslateMasterInterface;
use Austral\Contracts\Translate\TranslateChildInterface;
```

To update a project:

```bash
grep -rl 'Austral\\EntityTranslateBundle\\Entity\\Interfaces\\' src | xargs sed -i 's/Austral\\EntityTranslateBundle\\Entity\\Interfaces\\/Austral\\Contracts\\Translate\\/g'
```

### Traits combining translation with another bundle

entity-translate-bundle no longer knows the other bundles. These classes are removed, without alias:

| 3.1 (`Austral\EntityTranslateBundle\…`) | 4.0 |
|-----|-----|
| `Entity\Traits\EntityTranslateMasterFileCropperTrait` | `Austral\EntityFileBundle\Bridge\EntityTranslate\Entity\Traits\EntityFileCropperTranslateMasterTrait` (see the `UPGRADE-4.0.md` of entity-file-bundle) |
| `Entity\Traits\EntityTranslateMasterComponentsTrait` | provided by content-block-bundle (see its `UPGRADE-4.0.md`) |
| `Entity\Traits\EntityTranslateMasterRobotTrait` | provided by seo-bundle (see its `UPGRADE-4.0.md`) |
| `Entity\Traits\EntityTranslateMasterSeoTrait` | provided by seo-bundle (see its `UPGRADE-4.0.md`) |
| `Listener\ElasticSearchListener` (service `austral.entity_translate.elastic_search.listener`) | handled by elastic-search-bundle |

A project that uses one of these traits gets a fatal error ("Trait not found") until the `use` is updated.

### `DoctrineListener`

- `postLoad()` takes a `Doctrine\ORM\Event\PostLoadEventArgs` instead of a `Doctrine\Persistence\Event\LifecycleEventArgs`.
- The `$request` property is replaced by `$requestStack`. The current request is read when an entity is loaded.

A class that extends `DoctrineListener` and overrides `postLoad()` or reads `$this->request` must be updated.

### `EntityTranslateMasterTrait::setTranslateValueByKey()`

The parameters are typed:

```php
// Before
public function setTranslateValueByKey(TranslateChildInterface $translate, $fieldKey, $value = null): self
// After
public function setTranslateValueByKey(TranslateChildInterface $translate, string $fieldKey, mixed $value = null): self
```

An entity that overrides this method must use the new signature, otherwise PHP throws a fatal error.

### `EntityTranslateMasterTrait::__set()`

`__set()` no longer returns the value of the setter. Code that used the return value of `$entity->__set(...)` must call the setter directly.
