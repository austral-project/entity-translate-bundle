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

### `getTranslateClassname()` required on translated entities

`TranslateMasterInterface` (austral/contracts) declares `getTranslateClassname(): string`, and `EntityTranslateMasterTrait` declares it abstract. Every translated entity (master) must implement it, otherwise PHP throws a fatal error when the class is loaded.

```php
/**
 * @return class-string<PageTranslate>
 */
public function getTranslateClassname(): string
{
  return PageTranslate::class;
}
```

It returns the class of the translation entity, the one that `createNewTranslateByLanguage()` creates. `__isset()` uses it to know whether a field exists on the translation without creating one. The `relationClass` of the `#[Translate]` attribute cannot replace it: it may name an interface resolved by Doctrine (`resolve_target_entities`).

maker-bundle generates the method for new entities. For an existing project, add it to each class that implements `TranslateMasterInterface`:

```bash
grep -rl 'implements.*TranslateMasterInterface' src/Entity
```

### Magic methods of `EntityTranslateMasterTrait`

**Reading never creates a translation any more.** In 3.1, `__get()` and `__isset()` called `createNewTranslateByLanguage()` when the master had no current translation. That translation was added to the `translates` collection, so with the `cascade: ['persist']` that maker generates, a read in a template (`{{ page.title }}`) followed by a `flush()` inserted an empty translation in the database.

| Call, for a master without current translation | 3.1 | 4.0 |
|-----|-----|-----|
| `__get('title')` | creates an empty translation, returns `null` | returns `null`, creates nothing |
| `__isset('title')` | creates an empty translation | answers from `getTranslateClassname()`, creates nothing |
| `__set('title', …)` | creates the translation and writes | unchanged |

**Fields are found with the PropertyAccess rules**, through `Austral\EntityBundle\PropertyAccess\FieldAccessor`: getters `get…()`, `is…()`, `has…()` and `can…()`, setters, adders and public properties. A field reachable only by `isEnabled()` was not found in 3.1.

**A field that neither the master nor its translation has:**

| Call | 3.1 | 4.0 |
|------|-----|-----|
| `__get()`, `getTranslateValueByKey()` | fatal `\Error` | `null` |
| `__isset()` | `false` | `false` (unchanged) |
| `__set()` | creates the translation, then fatal `\Error` | `\LogicException`, and no translation is created |
| `setTranslateValueByKey()` | fatal `\Error` | `Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException` |

Code that caught `\Error` around these calls must catch the new exceptions.

### Services: listener aliases removed

The services of the bundle are no longer autowired nor autoconfigured. The three listeners are called by the dispatchers and are not meant to be fetched, so their 3.1 aliases are removed:

| 3.1 alias (removed) | 4.0 service id |
|---------------------|----------------|
| `austral.entity_translate.doctrine.listener` | `Austral\EntityTranslateBundle\Listener\DoctrineListener` |
| `austral.entity_translate.entity_manager.listener` | `Austral\EntityTranslateBundle\Listener\EntityManagerListener` |
| `austral.entity_translate.entity_mapping.listener` | `Austral\EntityTranslateBundle\Listener\EntityMappingListener` |

> **Warning:** a service of your application that decorates one of these listeners by its 3.1 alias (`decorates: austral.entity_translate.doctrine.listener`) makes the container compilation fail. Decorate the 4.0 service id instead.

### Mapping of translated fields

The mapping no longer stores closures to read and write translated fields: `EntityMappingListener` gives them a `TranslatableValueAccessor` with `setValueAccessor()`. Code that called `addGetterObjectValueFonction()` or `addSetterObjectValueFonction()` must use a value accessor (see the `UPGRADE-4.0.md` of entity-bundle). `getObjectValue()` and `setObjectValue()` of the mapping are unchanged.

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
