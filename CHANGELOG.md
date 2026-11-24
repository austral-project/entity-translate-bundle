# Changelog

All notable changes to this bundle are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html). All Austral bundles share the same major and minor version.

## [Unreleased]

### Added

- Unit tests for `EntityTranslateMasterTrait`, `EntityTranslateChildTrait` and `DoctrineListener`.
- `Austral\EntityTranslateBundle\Mapping\ValueAccessor\TranslatableValueAccessor`: the value accessor that the mapping uses for translated fields. It reads and writes the entity first, then, for a master, goes through its `__get()` / `__set()`.
- Unit tests for `TranslatableValueAccessor` and for the magic methods of `EntityTranslateMasterTrait` (`__get()`, `__isset()`, `__set()`, `getTranslateValueByKey()`, `setTranslateValueByKey()`).
- `tests/DependencyInjection/ContainerTest.php`: compiles the container, creates the three listeners and checks their tags, and that nothing is public.

### Changed

- **BREAKING** `TranslateMasterInterface` and `TranslateChildInterface` now come from `austral/contracts` (`Austral\Contracts\Translate\…`).
- **BREAKING** `DoctrineListener::postLoad()` takes a `Doctrine\ORM\Event\PostLoadEventArgs`.
- **BREAKING** `DoctrineListener` keeps the `RequestStack` (`$requestStack` property) instead of the `Request` (`$request` property).
- **BREAKING** `EntityTranslateMasterTrait::setTranslateValueByKey()` is typed: `string $fieldKey, mixed $value = null`.
- **BREAKING** A translated entity (master) must implement `getTranslateClassname(): string`, which returns the class of its translation. It is required by `TranslateMasterInterface` (austral/contracts) and declared abstract in `EntityTranslateMasterTrait`, which uses it to answer `__isset()` without creating a translation.
- **BREAKING** `EntityTranslateMasterTrait` reads and writes fields with `Austral\EntityBundle\PropertyAccess\FieldAccessor` (PropertyAccess rules, without magic methods): getters `get`/`is`/`has`/`can`, setters, adders and public properties are recognised. For a field that neither the master nor its translation has:
  - `__get()` and `getTranslateValueByKey()` return `null`;
  - `__set()` throws a `\LogicException`, without creating a translation;
  - `setTranslateValueByKey()` throws a `Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException`.

  In 3.1, each of these calls ended in a fatal `\Error` (call to an undefined method). `__isset()` still returns `false`.
- The translated fields of the mapping get a `TranslatableValueAccessor` (`setValueAccessor()`) instead of the getter and setter closures, which entity-bundle removes (see its `UPGRADE-4.0.md`).
- **BREAKING** Services are defined explicitly (no autowiring, no autoconfiguration). The three listeners keep their class name as service id and are private.
- `EntityTranslateMasterTrait` and `EntityTranslateChildTrait` are generic (`@template T of object`) for static analysis.
- The dependencies used by the code are declared in `composer.json` (Doctrine Collections, ORM, Persistence, ramsey/uuid, Symfony DependencyInjection, HttpFoundation, HttpKernel). `symfony/property-access` is no longer required directly: the field access goes through `FieldAccessor` of entity-bundle.

### Fixed

- The current language is read from the current request when an entity is loaded. It was read once, when the listener was created, so it could be missing or come from a previous request.
- `EntityTranslateMasterTrait::__set()` no longer returns a value.
- `EntityTranslateChildTrait::getLanguage()` returns `null` instead of throwing an error when no language is set.
- Reading a translated field no longer creates a translation. `__get()` and `__isset()` called `createNewTranslateByLanguage()` when the master had no current translation: the new, empty translation was added to `translates`, so a simple read in a template followed by a `flush()` could insert it in the database (`cascade: ['persist']`). A translation is now created only by a write (`__set()`).

### Removed

- **BREAKING** `Austral\EntityTranslateBundle\Entity\Interfaces\TranslateMasterInterface` and `TranslateChildInterface`: use `Austral\Contracts\Translate\TranslateMasterInterface` and `TranslateChildInterface`.
- The `@ORM\Column` annotation left on `EntityTranslateChildTrait::$isReferent`: only the attribute is used.
- **BREAKING** The traits that combined translation with another bundle are removed from entity-translate-bundle, which no longer depends on those bundles:
  - `EntityTranslateMasterFileCropperTrait`: replaced by `Austral\EntityFileBundle\Bridge\EntityTranslate\Entity\Traits\EntityFileCropperTranslateMasterTrait` (entity-file-bundle);
  - `EntityTranslateMasterComponentsTrait` (content-block), `EntityTranslateMasterRobotTrait` and `EntityTranslateMasterSeoTrait` (seo): their replacement is documented in the `UPGRADE-4.0.md` of content-block-bundle and seo-bundle.
- **BREAKING** `Listener\ElasticSearchListener` and its service `austral.entity_translate.elastic_search.listener`: the indexing of translated entities is handled by elastic-search-bundle.
- **BREAKING** The aliases of the listeners: `austral.entity_translate.doctrine.listener`, `austral.entity_translate.entity_manager.listener` and `austral.entity_translate.entity_mapping.listener`. Listeners are called by the dispatchers, not fetched from the container. Their service ids are their class names.

## [3.1.0] - 2022-09-17

- Updated bundles version to 3.1 for use the new Entities Interfaces

## [3.0.0] - 2021-02-10

- Created new repository and new branch in to Github
- Changed the project License to the MIT
- Writing and publish documentation
- Add to Packagist Service for installation with composer

## 2.x and 1.x

These versions were not open source and have no public documentation.

[Unreleased]: https://github.com/austral-project/entity-translate-bundle/compare/v3.1.2...4.x
[3.1.0]: https://github.com/austral-project/entity-translate-bundle/compare/v3.0.0...v3.1.0
[3.0.0]: https://github.com/austral-project/entity-translate-bundle/releases/tag/v3.0.0
