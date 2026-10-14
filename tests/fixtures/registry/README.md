# Versions registry evidence

Immutable Composer manifests for the versions page regression. Each filename records the upstream repository and full commit ID cited in docs/registry.json. Files are unmodified composer.json snapshots from https://raw.githubusercontent.com/laravel-streams/{repository}/{commit}/composer.json.

The regression reads these local snapshots and forbids outgoing HTTP requests. It does not query the latest Packagist release: publishing a new release must not break tests for an unchanged documentation snapshot. Update fixtures and registry proof together when intentionally revising the support matrix.
