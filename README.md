# Dokumentace

## Spuštění aplikace

- Vytvoříme konfiguranční soubor v adresáři `app/config` pro konkrétní server
- Naklonujeme zdrojový kód z repozitáře na server
- Nainstalujeme závislosti projektu pomocí konzole: `composer install --no-dev`
- Vytvoříme autoloader composeru pomocí konzole: `composer dump-autoload -o`
- Spustíme databázové migrace pomocí konzole: `php www/index.php migrations:continue --production`

## Cron úlohy

- Jednou za minutu spustit příkaz `periskop:xml:import` - příkaz se kouká, jestli je potřeba importovat nějaký nový soubor z Periskopu
- Jednou za 5 minut spustit příkaz `customer:activation:request` - odešle žádost o dokončení registrace na eshopu těm, kteří se zaregistrovali na prodejně
- Jednou za 12 hodin spustit příkazy pro vygenerování sitemap: `product:sitemap:generate`, `page:sitemap:generate`, `category:sitemap:generate`
- Jednou za 12 hodiny spustit příkaz pro vygenerování produktových feedů `product:productfeed:generate`
- Jednou za 12 hodiny spustit příkaz pro doplnění kategorií pro produktové feedy `product:productfeed:generatedata`
- Jednou za 1 minutu spustit příkaz pro nastavení stavu `accepted` pro objednávky `order:state:set:accepted`
- Jednou za 24 hodin spustit příkaz `category:product:sort` - řadí produkty v kategoriích
- Jednou za 24 hodin spustit příkaz `customer:birthdaycoupon:set` - nastavuje zakaznikum slevovy narozeninovy kupon

## Konzole

Příkazy pouštíme např.: `php www/index.php googlemerchant:generate`

## Periskop

### Export dat

Pro správný přenos dat z Periskopu je třeba v adresáři cílového serveru mít vytvořené složky `in`, `in\images` a `out`. Do složky `in` se ukládají exportní soubory
z Periskopu, do složky `out` se generují exportní soubory z eshopu.

## Gulp

Source složka `resources`.
Destination složka `www/assets/front`.

### Build CSS

```
gulp build-css
```

### Watch for CSS changes

```
gulp watch
```

### Build JS

```
gulp build-js
```
