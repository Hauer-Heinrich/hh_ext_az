# hh_ext_az – A–Z Verzeichnis für TYPO3

Extension für alphabetische Verzeichnisse wie „Satzungen und Verordnungen":
eine Tabelle, drei Record-Typen, drei Frontend-Plugins.

---

## 1. Datenmodell

Eine Tabelle `tx_hhextaz_domain_model_entry`, drei Models via **Single Table Inheritance**
(Feld `record_type`, gemappt in `Configuration/Extbase/Persistence/Classes.php`):

| record_type | Model           | Felder                                          | Verhalten im Frontend                 |
|-------------|-----------------|-------------------------------------------------|---------------------------------------|
| `default`   | `DefaultEntry`  | title, slug, teaser, description (RTE), image   | verlinkt auf eigene **Detailseite**    |
| `internal`  | `InternalEntry` | title, teaser, internal_link (Seite)            | verlinkt direkt auf die interne Seite  |
| `external`  | `ExternalEntry` | title, teaser, external_link (URL)              | verlinkt auf die externe URL (Neues Fenster) |

Das Backend-Formular blendet je nach Typ nur die relevanten Felder ein.

## 2. Installation

**Composer:**
```bash
composer require hauerheinrich/hh-ext-az
```
(lokal z. B. als `path`-Repository einbinden oder den Ordner nach `packages/hh_ext_az` legen)

**Classic Mode:** Ordner nach `typo3conf/ext/hh_ext_az` kopieren und im Extension Manager aktivieren.

Danach:
1. Datenbank-Schema aktualisieren (Admin Tools → Maintenance → Analyze Database Structure).
2. TypoScript einbinden – **entweder** Site Set „Hauer-Heinrich - A-Z" in der Site-Konfiguration aktivieren
   (TYPO3 v13) **oder** das statische Template „Hauer-Heinrich - A-Z" im Root-Template inkludieren.
3. Einen Sysfolder für die Einträge anlegen

## 3. Plugins

| Plugin           | Zweck                                                                    |
|------------------|--------------------------------------------------------------------------|
| **A-Z: Liste**   | Alphabetisch gruppierte Liste aller Einträge + Detailansicht (`show`)    |
| **A-Z: Suche**   | Volltextsuche mit Highlighting, blendet nicht passende Einträge aus      |
| **A-Z: Sprungmenü** | Buchstabenleiste gemäß `menuSorting`, verlinkt auf die Gruppen-Anker |

Typischer Seitenaufbau (wie im Screenshot): Sprungmenü + Suche + Liste auf **einer** Seite.
Liegen Suche und Liste auf derselben Seite, filtert JavaScript **live**
(Einträge ausblenden, Treffer mit `<mark>` highlighten, leere Buchstabengruppen ausblenden,
Buchstaben im Sprungmenü ausgrauen). Ohne JavaScript greift der serverseitige
Such-Fallback (GET-Formular, uncached) inklusive Highlighting.

## 4. Sortierung / Reihenfolge per TypoScript

```typoscript
plugin.tx_hhextaz.settings {
    # Standard: a-z, danach 0-9
    menuSorting = a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,0-9

    # Beispiel: Zahlen/Sonstiges zuerst
    # menuSorting = 0-9,a,b,c,d,e,f,g
}
```

Regeln:
- Gruppiert wird nach dem **ersten Buchstaben des Titels** (Umlaute werden transliteriert: Ä→A, Ö→O, Ü→U, ß→S).
- Alles, was keinem gelisteten Buchstaben zugeordnet werden kann (Ziffern, Sonderzeichen,
  nicht gelistete Buchstaben), landet in der Gruppe **`0-9`**.
- Fehlt `0-9` in `menuSorting`, wird die Gruppe automatisch **am Ende** angehängt.
- Innerhalb einer Gruppe wird per `Collator('de_DE')` sortiert (Fallback: `strcasecmp`).

## 5. Schöne URLs für die Detailseite

Die Einträge vom Typ `default` haben ein Slug-Feld (`slug`, generiert aus dem Titel,
`uniqueInSite`). In der **Site-Konfiguration** (`config/sites/<identifier>/config.yaml`)
ergänzen – Vorlage liegt in `Configuration/Routing/RouteEnhancers.example.yaml`:

```yaml
routeEnhancers:
  HhExtAzDetail:
    type: Extbase
    extension: HhExtAz
    plugin: List
    routes:
      - routePath: '/{entry_slug}'
        _controller: 'Entry::show'
        _arguments:
          entry_slug: entry
    defaultController: 'Entry::list'
    aspects:
      entry_slug:
        type: PersistedAliasMapper
        tableName: tx_hhextaz_domain_model_entry
        routeFieldName: slug
```

Ergebnis z. B.: `https://example.org/satzungen/hundesteuersatzung`

Hinweis: Die Detailansicht wird vom **List-Plugin** gerendert – die Seite muss also ein List-Plugin enthalten.

## 6. Templates anpassen

Über die Konstanten/Site-Settings `templateRootPath`, `partialRootPath`, `layoutRootPath`
eigene Pfade setzen und nur die Dateien überschreiben, die abweichen sollen
(`Resources/Private/Templates/...`, `Partials/Entry/Item.html` etc.).

Das mitgelieferte CSS (`Resources/Public/Css/layout.css`) ist bewusst schlank
und kann komplett ersetzt werden (`plugin.tx_hhextaz.settings.includeCSS = 0`).
Auch das mitgelieferte JavaScript (`Resources/Public/JavaScript/search.js`) kann komplett ersetzt werden (`plugin.tx_hhextaz.settings.includeJavaScript = 0`).

## 7. Hinweise

- Die Live-Suche filtert über `data-hhextaz-text` (Titel + Teaser + Beschreibung ohne HTML).
- Die Server-Suche durchsucht `title`, `teaser`, `description` (AND-Verknüpfung der Wörter).
- Das Sprungmenü zeigt Buchstaben ohne Einträge inaktiv an (heller, nicht verlinkt) –
  wie im Beispiel-Screenshot.
