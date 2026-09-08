/**
 * hh_ext_az - Live-Volltextsuche
 *
 * Aktiviert sich automatisch, wenn das Such-Plugin ([data-hhextaz-input])
 * und das List-Plugin ([data-hhextaz-list]) auf derselben Seite liegen:
 * - blendet alle nicht passenden Eintraege aus
 * - highlightet die gefundenen Woerter (<mark>)
 * - blendet leere Buchstabengruppen aus
 * - graut Buchstaben im Sprungmenue aus, deren Gruppe keine Treffer hat
 *
 * Ohne JavaScript greift der serverseitige Such-Fallback (GET-Formular).
 */
(function () {
    'use strict';

    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    function escapeRegExp(str) {
        return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function escapeHtml(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    ready(function () {
        var input = document.querySelector('[data-hhextaz-input]');
        var list = document.querySelector('[data-hhextaz-list]');
        if (!input || !list) {
            return;
        }

        // Live-Filter aktiv: Formular-Submit unterdruecken
        if (input.form) {
            input.form.addEventListener('submit', function (event) {
                event.preventDefault();
            });
        }

        var entries = Array.prototype.slice.call(list.querySelectorAll('[data-entry]'));

        // Originaltexte fuer das Highlighting sichern
        entries.forEach(function (entry) {
            Array.prototype.forEach.call(entry.querySelectorAll('[data-hhextaz-highlight]'), function (el) {
                if (!el.hasAttribute('data-hhextaz-original')) {
                    el.setAttribute('data-hhextaz-original', el.textContent);
                }
            });
        });

        var timeout = null;
        input.addEventListener('input', function () {
            window.clearTimeout(timeout);
            timeout = window.setTimeout(applyFilter, 120);
        });

        // Falls das Feld vorbefuellt ist (z. B. nach Server-Suche): direkt filtern
        if (input.value.trim() !== '') {
            applyFilter();
        }

        function applyFilter() {
            var query = input.value.trim().toLowerCase();
            var words = query === '' ? [] : query.split(/\s+/);
            var alternation = words.map(escapeRegExp).join('|');
            var splitPattern = words.length ? new RegExp('(' + alternation + ')', 'giu') : null;
            var testPattern = words.length ? new RegExp('^(' + alternation + ')$', 'iu') : null;

            entries.forEach(function (entry) {
                var text = (entry.getAttribute('data-hhextaz-text') || '').toLowerCase();
                var matches = words.every(function (word) {
                    return text.indexOf(word) !== -1;
                });

                entry.hidden = words.length > 0 && !matches;

                Array.prototype.forEach.call(entry.querySelectorAll('[data-hhextaz-highlight]'), function (el) {
                    var original = el.getAttribute('data-hhextaz-original') || '';
                    if (!words.length || !matches) {
                        el.textContent = original;
                        return;
                    }
                    var html = '';
                    original.split(splitPattern).forEach(function (part) {
                        if (part === '' || part === undefined) {
                            return;
                        }
                        html += testPattern.test(part)
                            ? '<mark class="mark">' + escapeHtml(part) + '</mark>'
                            : escapeHtml(part);
                    });
                    el.innerHTML = html;
                });
            });

            // Leere Buchstabengruppen ausblenden
            Array.prototype.forEach.call(list.querySelectorAll('[data-group]'), function (group) {
                group.hidden = group.querySelector('[data-entry]:not([hidden])') === null;
            });

            // Sprungmenue: Buchstaben ohne sichtbare Gruppe kennzeichnen
            Array.prototype.forEach.call(document.querySelectorAll('[data-hhextaz-menu-letter]'), function (el) {
                var token = el.getAttribute('data-hhextaz-menu-letter');
                var group = document.getElementById('letter-' + token);
                var visible = group !== null && !group.hidden;
                el.classList.toggle('menu-link--filtered', words.length > 0 && !visible);
            });
        }
    });
})();
