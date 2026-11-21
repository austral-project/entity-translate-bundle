# Changelog

All notable changes to this bundle are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html). All Austral bundles share the same major and minor version.

## [Unreleased]

### Added

- Unit tests for `EntityTranslateMasterTrait`, `EntityTranslateChildTrait` and `DoctrineListener`.

### Changed

- **BREAKING** `TranslateMasterInterface` and `TranslateChildInterface` now come from `austral/contracts` (`Austral\Contracts\Translate\…`).
- **BREAKING** `DoctrineListener::postLoad()` takes a `Doctrine\ORM\Event\PostLoadEventArgs`.
- **BREAKING** `DoctrineListener` keeps the `RequestStack` (`$requestStack` property) instead of the `Request` (`$request` property).
- **BREAKING** `EntityTranslateMasterTrait::setTranslateValueByKey()` is typed: `string $fieldKey, mixed $value = null`.
- `EntityTranslateMasterTrait` and `EntityTranslateChildTrait` are generic (`@template T of object`) for static analysis.
- The dependencies used by the code are declared in `composer.json` (Doctrine Collections, ORM, Persistence, ramsey/uuid, Symfony DependencyInjection, HttpFoundation, HttpKernel, PropertyAccess).

### Fixed

- The current language is read from the current request when an entity is loaded. It was read once, when the listener was created, so it could be missing or come from a previous request.
- `EntityTranslateMasterTrait::__set()` no longer returns a value.
- `EntityTranslateChildTrait::getLanguage()` returns `null` instead of throwing an error when no language is set.

### Removed

- **BREAKING** `Austral\EntityTranslateBundle\Entity\Interfaces\TranslateMasterInterface` and `TranslateChildInterface`: use `Austral\Contracts\Translate\TranslateMasterInterface` and `TranslateChildInterface`.
- The `@ORM\Column` annotation left on `EntityTranslateChildTrait::$isReferent`: only the attribute is used.
- **BREAKING** The traits that combined translation with another bundle are removed from entity-translate-bundle, which no longer depends on those bundles:
  - `EntityTranslateMasterFileCropperTrait`: replaced by `Austral\EntityFileBundle\Bridge\EntityTranslate\Entity\Traits\EntityFileCropperTranslateMasterTrait` (entity-file-bundle);
  - `EntityTranslateMasterComponentsTrait` (content-block), `EntityTranslateMasterRobotTrait` and `EntityTranslateMasterSeoTrait` (seo): their replacement is documented in the `UPGRADE-4.0.md` of content-block-bundle and seo-bundle.
- **BREAKING** `Listener\ElasticSearchListener` and its service `austral.entity_translate.elastic_search.listener`: the indexing of translated entities is handled by elastic-search-bundle.

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
