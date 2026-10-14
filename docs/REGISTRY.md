# Testing implementation evidence

This segment tracks the testing gaps closed by Operations task 263. Other pages are outside this segment.

| Page | Status | Tracking task | Implementation evidence | Regression tests |
| --- | --- | --- | --- | --- |
| `docs/packages/testing/test-data.md` | implemented | 263 | streams-core `02a168eb1`: validated `Stream::create` using the existing criteria persistence path; custom streams use `Streams::register` | `StreamTest::test_it_creates_custom_fixtures_and_restores_committed_data` |
| `docs/packages/testing/troubleshooting.md` | implemented | 263 | streams-testing `69e3cd5`: `clearData` and selective `loadFixtures`; streams-core `736c59ebe`: cooperative query timeout | `FixtureHelpersTest::test_clear_data_removes_entries_and_preserves_definitions_and_backups`, `test_load_fixtures_replaces_data_with_only_requested_streams`; `CriteriaTimeoutTest::test_timeout_rejects_results_after_the_query_budget_expires` |
| `docs/packages/testing/writing-tests.md` | implemented | 263 | streams-core `02a168eb1`: creation validates required fields before persistence | `StreamTest::test_it_validates_required_fields_before_creating_an_entry` |

Each named behavior regression failed before its implementation and passed afterward. `DocsContentTest::test_testing_registry_gaps_are_implemented_and_tracked` checks all three page statuses and task pointers.

Query timeouts check elapsed time around synchronous query execution and during hydration. Native I/O timeouts remain necessary for interrupting blocked adapter calls.
