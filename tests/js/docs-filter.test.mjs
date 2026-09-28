// Run with: npm run test:js
import test from 'node:test';
import assert from 'node:assert/strict';
import { normalize, findMatches } from '../../resources/js/docs-filter.js';

test('normalize lower-cases and strips diacritics', () => {
    assert.equal(normalize('Rôutes ÉTÉ'), 'routes ete');
});

test('findMatches is case- and diacritic-insensitive and maps back to the source', () => {
    const text = 'Caché Routes and route groups';
    assert.deepEqual(findMatches(text, normalize('ROUTE')), [[6, 11], [17, 22]]);
    assert.deepEqual(findMatches(text, normalize('cache')), [[0, 5]]);
    assert.equal(text.slice(0, 5), 'Caché');
});

test('findMatches handles decomposed input and limits', () => {
    const decomposed = 'Re\u0301sume\u0301 resume';
    assert.deepEqual(findMatches(decomposed, 'resume', 1), [[0, 8]]);
    assert.deepEqual(findMatches('abc', ''), []);
});
