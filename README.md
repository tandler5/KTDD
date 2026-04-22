# KTDD - Systém pro půjčování knih

Tento projekt je semestrální prací zaměřenou na demonstraci TDD (Test-Driven Development) a DevOps principů v prostředí frameworku Laravel.

## Doména: Půjčovna knih
Aplikace slouží ke správě výpůjček knih v knihovně.

### Doménové entity
1. **User** (Uživatel): Čtenář, který si půjčuje knihy.
2. **Book** (Kniha): Předmět výpůjčky.
3. **Rental** (Výpůjčka): Vztah mezi uživatelem a knihou s časovým omezením.

### Business pravidla (povinná funkcionalita)
1. **Limit výpůjček**: Uživatel může mít v jeden okamžik půjčeny maximálně 3 knihy.
2. **Dostupnost**: Nelze si půjčit knihu, která je již půjčená jiným uživatelem.
3. **Blokace při prodlení**: Uživatel, který má alespoň jednu knihu po termínu vrácení, si nemůže půjčit další knihu.
4. **Validace stavu**: Knihu lze "vrátit" pouze v případě, že je ve stavu "půjčeno".
5. **Idempotence**: Opakovaný požadavek na zapůjčení stejné knihy stejným uživatelem (pokud již proběhlo) nevede k duplicitnímu záznamu.

---

## Technické požadavky a kvalita

### Testovací strategie
- **Unit testy**: Testování business pravidel v entitách a službách (TDD cyklus Red-Green-Refactor).
- **Integrační testy**: Ověření REST API endpointů a propojení s databází.
- **Mockování**: Použití pro simulaci času (ověření exspirace výpůjček) a případných externích služeb.

### CI/CD Pipeline (GitHub Actions)
- Automatický build a instalace závislostí.
- Spouštění testů při každém pushi/PR.
- Měření Code Coverage (JaCoCo ekvivalent pro PHP - Xdebug/PCOV).
- Sestavení Docker image.

### Infrastruktura
- **Docker**: Kontejnerizace aplikace a MySQL databáze.
- **Kubernetes**: Manifesty pro Deployment, Service, ConfigMap a Secret.

---

## Jak spustit projekt lokálně

### Požadavky
- Docker a Docker Compose

### Instalace
1. Klonování repozitáře: `git clone ...`
2. Spuštění prostředí: `docker compose up -d`
3. Instalace závislostí: `docker compose exec app composer install`
4. Spuštění migrací: `docker compose exec app php artisan migrate`

### Spuštění testů
`docker compose exec app php artisan test --coverage`
