"""Apply reviewed registry rows; never infer implementation from editorial status."""
import json
import re
from pathlib import Path

registry = Path('docs/registry.json')
rows = json.loads(registry.read_text())
for row in rows:
    path = Path(row['page'])
    text = path.read_text()
    header, body = text.split('\n---\n', 1)
    old = re.search(r'^status: (.+)$', header, re.M).group(1)
    if old in ('draft', 'review', 'ready', 'deprecated'):
        header = re.sub(r'^status: .+$', 'editorial_status: ' + old, header, flags=re.M)
    else:
        header = re.sub(r'^status: .+\n?', '', header, flags=re.M)
    header = re.sub(r'^tracking_task: .+\n?', '', header, flags=re.M)
    header = re.sub(r'^package: .+$', 'package: ' + row['package'], header, flags=re.M)
    tracking = 'null' if row['status'] == 'implemented' else 'pending'
    path.write_text(header.rstrip() + '\nstatus: ' + row['status'] + '\ntracking_task: ' + tracking + '\n---\n' + body)

lines = [
    '# Documentation implementation registry',
    '',
    'Evidence audit dated 2026-10-13. Package paths are relative to the named repository under `~/Sites/_packages/`; site paths are relative to this repository. Commits pin the inspected snapshot, not a claim that package tests ran. `implemented` describes the documented scope; `partial` means at least one advertised API differs or is unavailable. Explicitly excluded features do not make an otherwise implemented page partial.',
    '',
    '`pending` is a tracking-task placeholder, never an invented Operations id. Editorial readiness is retained in `editorial_status`. Implementation `status` is `implemented`, `partial`, or `planned`, which is the schema stored on the docs streams (`package`, `status`, `tracking_task`). Record #189 was not readable from this run, so this file is the schema those fields enforce.',
    '',
    'Planned pages are only for a concrete unimplemented shape: Core and API READMEs both list stream-config authorization gates, and `streams:tap` is an empty unregistered command. `Criteria::when` already exists, so that README checkbox is not a planned page. The API README roadmap (versioning, rate limits, API keys, webhooks, GraphQL, subscriptions, batch, analytics) still describes Core 1.0 and does not specify those features, so they are not pages.',
    '',
    '| Page | Package | Type | Status | Proof (repository / commit / path) | Finding |',
    '| --- | --- | --- | --- | --- | --- |',
]
for row in sorted(rows, key=lambda r: (['core', 'ui', 'api', 'sdk', 'testing', 'client', 'site', 'all'].index(r['package']), r['type'], r['page'])):
    proof = '; '.join(row['proof'])
    lines.append('| ' + ' | '.join([row['page'], row['package'], row['type'], row['status'], proof, row['finding']]) + ' |')
Path('docs/REGISTRY.md').write_text('\n'.join(lines) + '\n')
