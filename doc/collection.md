Přidání nové kolekce
------
- připravit fotografie 1920x470 s vlnou, 1060x570, 280x260, 300x470 (reprezentativní produkt bez pozadí)
- přidat statickou šablonu do `app/FrontModule/presenters/templates/Category/static/image/` a přiřadit ji pomocí administrace v editace kategorie
- napárovat v databázové tabulce `product_parametr_group_lock_parameter` parametr kolekce na kategorii
- napárovat data ve třídě `App\FrontModule\Components\Product\Collection\Preview\Preview`
- napárovat produkt ve třídě `App\FrontModule\Components\Category\CategoryList\CategoryList`
- doplnit náhled kolekce skrze formulář pro editaci kategorie (280x260)