# Vzájemná komunikace Periskop a Colibri SHOP v2
Při komunikaci dochází k aktualizaci zboží (včetně obrázků, vlastností, cen...), partnerů, stavu na skladě a stavu objednávek na straně eshopu, a k přidávání partnerů a objednávek na straně Periskopu. Výměnu dat zprostředkovávají ACI programy ES_EXPORT, ES_EXPORT_OBJEDNAVKY, ES_EXPORT_STAVY, ES_IMPORT_OBJEDNAVKA a standardní knihovna ES_STD.ACI. Vygenerované soubory se umístí na podadresář eshop a po úspěšném přenosu na FTP server (parametry v tabulce ES_OBCHODY) jsou odstraněny. Součástí názvu všech XML souborů je aktuální datum a čas, na FTP se tak nemohou přepsat. Naopak názvy souborů s obrázky se jmenují stejně a případné přehrání existujícího souboru je korektní operace.
Komunikace je v kódování windows-1250 (přičemž data v eshopu jsou uložená v utf-8). Formát datumu je RRRR-MM-DD. U čísel se používá desetinná tečka.

## 1. Aktualizace dat v eshopu
### 1.1 Aktualizace číselníků
Zajišťuje ES_EXPORT.ACI, které generuje XML soubory se zbožím a partnery, které jsou označené jako určené pro export do eshopu. Výsledný XML soubor se v rámci ACI nahrává na FTP server do adresáře in. Dochází vždy ke kompletní aktualizaci kartoték, s výjimkou obrázků, které se na FTP nahrávají pouze v případě, že byly od posledního exportu založeny či změněny. Obrázky se umisťují do adresáře in/images. Zboží, partneři a obrázky jsou opatřeny unikátním identifikátorem v rámci Periskopu, podle kterého se provážou s existujícími záznamy na straně eshopu. Název XML souboru má tvar "ESyyyymmddhhnnss.xml". Názvy obrázků tvoří jejich systémové ID. ACI by se mělo spouštět odhadem jednou za den.

```xml
<data>
    <item_groups partial="true pro částečný export, nepovinné"> //číselník kategorií zboží
        <item_group id="ID v Periskopu, INT">
            <code>Kód kategorie zboží, CHAR(5)</code> <parent>Kód nadřazené kategorie, CHAR(5)</parent> //spíš jen jako příprava na možný požadavek na řízení kategorií z Periskopu <names>
            <name lang="ISO jazyk">Název kategorie zboží, VARCHAR(80)</name>
            </names> 
              <texts> //příprava na možný požadavek na řízení kategorií z Periskopu
                <text lang="ISO jazyk">Text kategorie zboží, BLOB</text>
            </texts>   
            <images> //obrázky kategorií. Příprava na možný požadavek na řízení kategorií z Periskopu
                <image id="ID v Periskopu, INT">
                    <code>Kód obrázku, CHAR(3), nepovinné</code>
                </image>
            </images>
        </item_group>
    </item_groups>
    
      <item_availabilities> //číselník dostupnosti zboží
        <item_availability id="ID v Periskopu, INT">
            <code>Kód dostupnosti zboží, CHAR(5)</code> <name>Název dostupnosti zboží, VARCHAR(25)</name>
        </item_availability>
    </item_availabilities>  
    
    <item_attributes> //číselník vlastností zboží
        <item_attribute id="ID v Periskopu, INT">
            <code>Kód atributu zboží</code> <selection>Indikátor výběru, 0/1</selection> <names>
            <name lang="ISO jazyk">Název vlastnosti, BLOB</name>
            </names>
        </item_attribute>
    </item_attributes>
    
      <items partial="true pro částečný export, nepovinné"> //seznam definic zboží - přesunuto ze začátku souboru až za definice kategorií, parametrů a typů dostupnosti
        <item id="ID v Periskopu, INT" last_change="datum poslední změny záznamu"> //definice zboží
            <code>Kód zboží, 20 znaků</code> <name>Zkrácený název zboží, VARCHAR(25)</name> <analysis>Analýza zboží, CHAR(10), nepovinné</analysis> <ean_code>EAN kód zboží, CHAR(14), nepovinné</ean_code> <key_word>Klíčové slovo, VARCHAR(16), nepovinné</key_word> <reception_date>Datum příjmu, DATE, nepovinné</reception_date> <visible>Zboží se má zobrazovat, 0/1</visible> <ind_action>Zboží je v akci, 0/1</ind_action> <ind_new>Zboží je novinka, 0/1</ind_new> <ind_topselling>Zboží je nejprodávanější, 0/1</ind_topselling>  <names> //názvy zboží
            <name lang="ISO jazyk">Název zboží, VARCHAR(80)</name>
            </names>   
            <texts>
                <text lang="ISO jazyk">Popis zboží, BLOB</text>
            </texts>
            <images> //obrázky zboží
                <image id="ID v Periskopu, INT">
                    <code>Kód obrázku, CHAR(3), nepovinné</code>
                </image>
            </images>
            <groups> //kategorie zboží
                <group id="ID v Periskopu, INT" code="kód z číselníku"></group>
            </groups>
            <attributes> //vlastnosti zboží
                <attribute id="ID v Periskopu, INT" code="kód z číselníku">
                    <selection_value>Hodnota pro výběr, VARCHAR(20)</selection_value> 
                    <boolean_value>Pro dvouhodnotové parametry, 0/1</boolean_value>
                     <order>Pořadí vlastnosti</order> <values>
                    <value lang="ISO jazyk">Text vlastnosti, BLOB</value>
                    </values>
                </attribute>
            </attributes>
            <relations> //související zboží
                <relation>
                    <item_id>ID souvisejícího zboží</item_id> 
                    <order>Pořadí souvisejícího zboží</order>
                </relation>
            </relations>
             <variants> //varianty zboží
                <variant id="ID v Periskopu, INT" code="kód z číselníku">
                    <name lang="ISO jazyk">Název varianty zboží, VARCHAR(250)</name>
                </variant>
            </variants>
            <stock_price>Skladová cena, NUMBER(14,2), nepovinné</stock_price> 
            <default_price>Aktuální cena, NUMBER(14,2), nepovinné</default_price>
            <prices> //prodejní ceny
                <price currency="ISO měna" group="cenová skupina">
                    <vat_base>Prodejní cena jednotková bez DPH, NUMBER(14,4)</vat_base> 
                    <vat>Procento DPH</vat> 
                    <unit_price>Prodejní cena jednotková včetně DPH, NUMBER(14,4)</unit_price>
                </price>
            </prices>
        </item>
    </items>
    
    <customers partial="true pro částečný export, nepovinné"> //partneři
        <customer id="ID v Periskopu, INT" eshop_id="ID v Colibri, INT" last_change="datum poslední změny záznamu"> //definice partnera
            <code>Kód partnera, CHAR(26)</code>
             <name>Název partnera, VARCHAR(80)</name>
             <firstname>Jméno partnera, VARCHAR(80)</firstname>
             <lastname>Příjmení partnera, VARCHAR(80)</lastname> 
            <addressing>Oslovení partnera, VARCHAR(60)</addressing>
             <discount_percent>Procenta slevy, NUMBER(7,2), nepovinné</discount_percent>
             <price_group>Cenová skupina zboží, CHAR(2), nepovinné</price_group>
             <currency>ISO kód měny, CHAR(3)</currency>
             <language>ISO jazyk, CHAR(2)</language>
             <is_vat>Partner je plátcem DPH, 0/1</is_vat>
             <address_part1>Adresa část 1, VARCHAR(35)</address_part1>
             <address_part2>Adresa část 2, VARCHAR(35)</address_part2>
             <place>Adresa místo, VARCHAR(35)</place> 
            <street>Adresa ulice, VARCHAR(35)</street> 
            <city>Adresa město, VARCHAR(35)</city>
             <postcode>Adresa PSČ, CHAR(6)</postcode> 
            <country>Kód země, CHAR(2)</country> 
            <ico>IČO, CHAR(10)</ico>
             <dic>DIČ, CHAR(16)</dic> 
            <email>email, VARCHAR(50)</email>
             <phone>telefon, VARCHAR(20)</phone>
             <fax>fax, VARCHAR(20)</fax>
             <www>www adresa, VARCHAR(50)</www>
             <birthdayyear>Rok narození, CHAR(4)</birthdayyear>
             <birthdaymonth>Měsíc narozenin, CHAR(2)</birthdaymonth>
             <birthdayday>Den narozenin, CHAR(2)</birthdayday>
             <birthdaycoupon>Nárok na narozeninový kupon, A/N</birthdaycoupon> 
            <newsletter>Zasílat nabídky/newsletter, A/N</newsletter> 
            <sex>Pohlaví, M/Z nebo nic</sex> 
            <note>Poznámka, BLOB</note>
        </customer>
    </customers>
</data>
```

### 1.2 Aktualizace stavů zboží - struktura XML souboru
Zajišťuje ES_EXPORT_STAVY.ACI, které generuje XML soubory se stavem zboží na eshopovém skladě. Výsledný XML soubor se v rámci ACI nahrává na FTP server do adresáře in. Název XML souboru má tvar "ESyyyymmddhhnnssS.xml". ACI by se mělo spouštět odhadem jednou za hodinu.
```xml
<stock>
    <item>
        <item_id>ID zboží v PERISKOPU, INT</item_id>
         <amount>Množství, NUMBER(12,3)</amount>
         <availability>Kód dostupnosti, CHAR(5), z číselníku, nepovinné</availability>
    </item>
</stock>
```

### 1.3 Aktualizace stavů objednávek
Zajišťuje ES_EXPORT_OBJEDNAVKY.ACI, které generuje XML soubory se stavem přijatých objednávek. Výsledný XML soubor se v rámci ACI nahrává na FTP server do adresáře in. Dokud se na straně eshopu nenačte stav objednávky přijaté Periskopem, je objednávka součástí exportu objednávek. Název XML souboru má tvar "ESyyyymmddhhnnssO.xml". ACI by se mělo spouštět odhadem jednou za hodinu.
```xml
<orders>
    <order id="ID v Colibri, INT">
        <state>Stav objednávky, CHAR(1), viz dále</state>
    </order>
</orders>
```

### Stavy objednávek
1. Nová - Nově přijatá objednávka. Do 60 minut od provedení ji zákazník může stornovat. PERISKOP: řešeno na straně eshopu (objednávku odešle do Periskopu až za hodinu)
2. Přijatá - Po hodině vstoupí v platnost PERISKOP: řešeno na straně eshopu (objednávku označí až za hodinu), eshop posílá objednávky pouze, pokud je má označeny jako přijaté.
3. Kompletace - Objednávka je v expedici a připravuje se k odeslání. PERISKOP: posílá se, když stav je O, C, nebo P, v případě P se překlopí na O. Objednáky už eshop dále neposílá.
4. Expedice - Objednávka je kompletní a bude předána k doručení. PERISKOP: posílá se, když stav je E, A
5. Odběrné místo - Objednávka byla expedována na odběrné místo. PERISKOP: neřeší se
6. Odesláno - Objednávka byla odeslána, v případě doručení kurýrem předána kurýrní službě. PERISKOP: neřeší se
7. Vyřízená - Objednávka byla odeslána a uhrazena. PERISKOP: posílá se, když je stav F, stav se překlopí na U až po obdržení potvrzení uzavření v exportu objednávek (state=7)
8. Storno - Objednávku stornoval sám zákazník. PERISKOP: neřeší se
9. Vrácená - Zákazník objednávku nepřevzal a byla vrácena zpět. PERISKOP: neřeší se
10. Zrušená - Objednávka byla zrušena a nebude vyřízena. PERISKOP: posílá se, pokud se objednávka najde v tabulce ES_OBJ_VYRAZENE. Z eshopu se očekává potvrzení vyřazení v exportu objednávek (state=10)

## 2. Aktualizace dat v Periskopu
### 2.1 Import objednávek
Zajišťuje ES_IMPORT_OBJEDNAVKA.ACI, které načte XML soubor z FTP (podle názvu získaného skrze http protokol) s objednávkami včetně informací o partnerovi. Je nutné kontrolovat, zda už objednávka nebyla jednou založena (z eshopu se přenáší, dokud z Periskopu nedojde potvrzení o jejím převzetí). Existující partner se aktualizuje (odlišná adresa se zakládá jako záznam s novou platností). Neregistrovaný partner se pozná podle prázdného pole "username", je založen v kartotéce partnerů, ale není zpětně přenášen do eshopu. ACI by se mělo spouštět odhadem jednou za půl hodiny, podle provozu eshopu.
```xml
<orders>
    <order id="ID v Colibri, INT" state="stav">
        <date>Datum objednávky</date> 
        <currency>ISO kód měny, CHAR(3)</currency>
        <note>Poznámka, BLOB</note> 
        <payment>ID typu platby v Colibri, INT</payment> 
        <delivery>ID typu dopravy v Colibri, INT</delivery>
         <deliveryplace>ID místa vyzvednutí v Colibri, INT</deliveryplace>
         <customer id="ID v Colibri, INT" fid="ID v Periskopu, INT">
            <name>Název partnera, VARCHAR(80)</name>
            <username>Uživatelské jméno, VARCHAR(20)</username>
             <firstname>Jméno partnera, VARCHAR(80)</firstname>
             <lastname>Příjmení partnera, VARCHAR(80)</lastname>
             <ico>IČO, CHAR(10)</ico> 
            <dic>DIČ, CHAR(16)</dic> 
            <email>email, VARCHAR(50)</email>
             <phone>telefon, VARCHAR(20)</phone> 
            <fax>fax, VARCHAR(20)</fax>
             <www>www adresa, VARCHAR(50)</www> //v Colibri neevidujeme
             <address_part1>Adresa část 1, VARCHAR(35)</address_part1> //nazevfirmy či jméno a příjmení 
            <address_part2>Adresa část 2, VARCHAR(35)</address_part2> //v Colibri neevidujeme 
            <street>Adresa - ulice, VARCHAR(35)</street>
             <city>Adresa - město, VARCHAR(35)</city> 
            <place>Adresa - místo, VARCHAR(35), nepovinné</place> //v Colibri neevidujeme 
            <postcode>Adresa - PSČ, CHAR(6)</postcode>
             <country>Kód země, CHAR(2)</country> 
            <note>Poznámka, BLOB</note> //v Colibri neevidujeme
        </customer>
        <dname>Adresa dodání - jméno, VARCHAR(80)</dname>
         <dstreet>Adresa dodání - ulice, VARCHAR(35)</dstreet> 
        <dcity>Adresa dodání - město, VARCHAR(35)</dcity>
         <dplace>Adresa dodání - místo, VARCHAR(35), nepovinné</dplace> //vColibri neevidujeme
         <dpostcode>Adresa dodání - PSČ, CHAR(6)</dpostcode>
         <dcountry>Adresa dodání - kód země, CHAR(2)</dcountry>
         <dphone>Adresa dodání - telefon, VARCHAR(20)</dphone> 
        <birthdaycoupon>Uživatel aplikoval slevový kupon, A/N</birthdaycoupon>
         <items> //položky objednávky
            <item id="ID v Periskopu, INT">
                <quantity>Množství, číslo</quantity> 
                <vat>Procento DPH</vat>
                 <vat_base>Prodejní cena jednotková bez DPH, number(14,4)</vat_base>
                 <unit_price>Prodejní cena jednotková včetně DPH, number(14,4)</unit_price>
                 <total_vat_base>Celková cena bez DPH, number(14,4)</total_vat_base>
                 <total_price>Celková cena včetně DPH, number(14,4)</total_price>
                 <discount>Sleva v procentech, number(12,4)</discount>
                 <variant_code>Kód varianty, z číselníku, nepovinné</variant_code>
            </item>
        </items>
    </order>
</orders>
```

## 2.2 Import partnerů
Zajišťuje ES_IMPORT_PARTNERI.ACI(?), které načte XML soubor z FTP (podle názvu získaného skrze http protokol) s informacemi o partnerech, kteří byli založeni nebo změněni od posledního exportu. Bylo by vhodné tento soubor zpracovávat před importem objednávek, protože uživatel, který se zaregistruje na eshopu a hned provede objednávku bude v objednávce označen pouze pomocí ID v Colibri, ID v Periskopu ještě nebude známo.
```xml
<customers>
    <customer id="ID v Colibri, INT" fid="ID v Periskopu, INT">
        <name>Název partnera, VARCHAR(80)</name>
         <firstname>Jméno partnera, VARCHAR(80)</firstname>
         <lastname>Příjmení partnera, VARCHAR(80)</lastname> 
        <username>Uživatelské jméno, VARCHAR(20)</username> 
        <currency>ISO kód měny, CHAR(3)</currency>
         <language>ISO jazyk, CHAR(2)</language>
         <ico>IČO, CHAR(10)</ico> 
        <dic>DIČ, CHAR(16)</dic>
         <email>email, VARCHAR(50)</email>
         <phone>telefon, VARCHAR(20)</phone>
         <fax>fax, VARCHAR(20)</fax>
         <www>www adresa, VARCHAR(50)</www> //v Colibri neevidujeme
         <address_part1>Adresa část 1, VARCHAR(35)</address_part1> //nazevfirmy či jméno a příjmení 
        <address_part2>Adresa část 2, VARCHAR(35)</address_part2> //v Colibri neevidujeme
         <street>Adresa - ulice, VARCHAR(35)</street>
         <city>Adresa - město, VARCHAR(35)</city>
         <place>Adresa - místo, VARCHAR(35), nepovinné</place> //v Colibri neevidujeme 
        <postcode>Adresa - PSČ, CHAR(6)</postcode>
         <country>Kód země, CHAR(2)</country>
         <dstreet>Adresa dodání - ulice, VARCHAR(35)</dstreet>
         <dcity>Adresa dodání - město, VARCHAR(35)</dcity>
         <dplace>Adresa dodání - místo, VARCHAR(35), nepovinné</dplace>
         <dpostcode>Adresa dodání - PSČ, CHAR(6)</dpostcode>
         <dcountry>Adresa dodání - kód země, CHAR(2)</dcountry>
         <dphone>Adresa dodání - telefon, VARCHAR(20)</dphone>
         <birthdayyear>Rok narození, CHAR(4)</birthdayyear>
         <birthdaymonth>Měsíc narozenin, CHAR(2)</birthdaymonth>
         <birthdayday>Den narozenin, CHAR(2)</birthdayday>
         <newsletter>Zasílat nabídky/newsletter, A/N</newsletter>
         <sex>Pohlaví, M/Z nebo nic</sex>
         <note>Poznámka, BLOB</note> //v Colibri neevidujeme
    </customer>
</customers>
```

## 2.3 Import poptávek
Zajišťuje ES_IMPORT_DEMAND.ACI(?), které načte XML soubor z FTP (podle názvu získaného skrze http protokol) s informacemi o poptávkách, které byly založeny nebo změněny od posledního exportu. Potvrzovací mechanismus jako u objednávek asi není potřeba.
```xml
<demands>
    <demand id="ID v Colibri, INT">
        <name>Jméno a příjmení zákazníka, VARCHAR(80)</name> 
        <email>email, VARCHAR(50)</email>
         <phone>telefon, VARCHAR(20)</phone> 
        <item>ID v Periskopu, INT (nepovinné)</item> 
        <text>Text poptávky, BLOB</text>
    </demand>
</demands>
```

Poslední aktualizace 14.3.2016, Zicha