## [1.3.3](https://github.com/byteshard/core/compare/v1.3.2...v1.3.3) (2024-02-14)


### Bug Fixes

* use correct exception handling during upload ([71a70f7](https://github.com/byteshard/core/commit/71a70f7d9df4651961720948a0a9df37e2ef3d04))

## [1.3.2](https://github.com/byteshard/core/compare/v1.3.1...v1.3.2) (2024-02-13)


### Bug Fixes

* upload used incorrect id and the client crashed ([5fb9ee8](https://github.com/byteshard/core/commit/5fb9ee8faa745c81f03dfa0e7ea0335d52003a15))

## [1.3.1](https://github.com/byteshard/core/compare/v1.3.0...v1.3.1) (2023-12-01)


### Bug Fixes

* remove preceding slashes since get_called_class returns it without it ([eb2a312](https://github.com/byteshard/core/commit/eb2a312a46c9027ddb3e710ee7f89cbd33b0163f))

# [1.3.0](https://github.com/byteshard/core/compare/v1.2.0...v1.3.0) (2023-11-25)


### Features

* add class maps to mysql ([9b0d9e5](https://github.com/byteshard/core/commit/9b0d9e568b953a8579bd57239ae012e7f54e5f94))

# [1.2.0](https://github.com/byteshard/core/compare/v1.1.0...v1.2.0) (2023-11-24)


### Bug Fixes

* add value interface to check if the value trait is implemented in form objects ([0625311](https://github.com/byteshard/core/commit/0625311b5fa7cacc5906d91dda95b3d466dd2247))


### Features

* add additional user data to session in public class ([feb8071](https://github.com/byteshard/core/commit/feb8071d08f95909bf629fef7d1bb64eee7b1fcf))

# [1.1.0](https://github.com/byteshard/core/compare/v1.0.10...v1.1.0) (2023-11-24)


### Features

* defineDataBinding can now be used in forms ([9535ad4](https://github.com/byteshard/core/commit/9535ad4da6ca80c08056c1bcb81167d11fa7dadc))
* defineDataBinding can now be used in forms ([bfe75ff](https://github.com/byteshard/core/commit/bfe75ff9189d811edf58be132b9562de1fc1ad21))

## [1.0.10](https://github.com/byteshard/core/compare/v1.0.9...v1.0.10) (2023-11-16)


### Bug Fixes

* if a confirmAction is returned as part of a callMethod or saveFormData action it can now work within the limits of the onClick event ([5fe9127](https://github.com/byteshard/core/commit/5fe9127a58421d05cd9ea386fb9961823ea86876))

## [1.0.9](https://github.com/byteshard/core/compare/v1.0.8...v1.0.9) (2023-10-31)


### Bug Fixes

* possible Host Header injection in Login page is now fixed. ([4691e6d](https://github.com/byteshard/core/commit/4691e6dabdd1768055a60f9d888cf005a5c8ebd3))

## [1.0.8](https://github.com/byteshard/core/compare/v1.0.7...v1.0.8) (2023-10-12)


### Bug Fixes

* replace utf8 encode/decode with mb_convert_encoding to remove deprecation warning ([4c3ab35](https://github.com/byteshard/core/commit/4c3ab354625f3181350f6ab3f5721179865758f4))

## [1.0.7](https://github.com/byteshard/core/compare/v1.0.6...v1.0.7) (2023-10-09)


### Bug Fixes

* use enum for ColumnType ([451b691](https://github.com/byteshard/core/commit/451b6911892caba2732e3a66aef079462b68dcd7))
* use enum for ColumnType ([816ea61](https://github.com/byteshard/core/commit/816ea61b162fa4769a92b746bc6c6c69c123c33c))

## [1.0.6](https://github.com/byteshard/core/compare/v1.0.5...v1.0.6) (2023-10-09)


### Bug Fixes

* mysql connection was trying to use string instead of enum ([c374cb4](https://github.com/byteshard/core/commit/c374cb4f3611a8e524506b58e37cf30088364337))

## [1.0.5](https://github.com/byteshard/core/compare/v1.0.4...v1.0.5) (2023-08-15)


### Bug Fixes

* catch export exceptions and show error message in client ([ab7bfa1](https://github.com/byteshard/core/commit/ab7bfa169bc41d6abe4d67d52a90f9cd334e5d2c))

## [1.0.4](https://github.com/byteshard/core/compare/v1.0.3...v1.0.4) (2023-07-19)


### Bug Fixes

* updated phpDoc parameter type ([3414ecc](https://github.com/byteshard/core/commit/3414ecc0f6ab69fa1b9b7f0a67165429f1918ad9))

## [1.0.3](https://github.com/byteshard/core/compare/v1.0.2...v1.0.3) (2023-06-06)


### Bug Fixes

* lasttab: column name changed to lowercase: LastTab => lasttab ([15b75a9](https://github.com/byteshard/core/commit/15b75a92906e95dfef5b644f9b7a1592ff3345be))

## [1.0.2](https://github.com/byteshard/core/compare/v1.0.1...v1.0.2) (2023-05-10)


### Bug Fixes

* add missing extensions to composer step as well ([1c4a74b](https://github.com/byteshard/core/commit/1c4a74b2d07101c44ad1b75072cef8fa4e48b66b))

## [1.0.1](https://github.com/byteshard/core/compare/v1.0.0...v1.0.1) (2023-05-10)


### Bug Fixes

* add missing extensions for workflows ([8802a2e](https://github.com/byteshard/core/commit/8802a2e255fea2099a43e13c005e4c1cd939b457))

# 1.0.0 (2023-05-10)


### Features

* initial commit for core ([d89dd26](https://github.com/byteshard/core/commit/d89dd260bd3fae6d6ffbcc275eeb68bfba55c132))
