#!/bin/bash

declare -A assArray1
declare -A assArray2

assArray1[andele]=15;
assArray1[diva]=20;
assArray1[denni]=18;
assArray1[kvetiny]=22;
assArray1[laskaviranadeje]=23;
assArray1[motyli]=24;
assArray1[odvaznaakrasna]=25;
assArray1[panska]=49;
assArray1[tahitskekralovny]=34;
assArray1[venezia]=50;
assArray1[voda]=54;
assArray1[tolerance-zasnubni]=35;
assArray1[primavera]=28;

assArray2[andele]=andel;
assArray2[diva]=diva;
assArray2[denni]=denni;
assArray2[kvetiny]=kvetiny;
assArray2[laskaviranadeje]=laskaviranadeje;
assArray2[motyli]=motyli;
assArray2[odvaznaakrasna]=odvazna-krasna;
assArray2[panska]=panska;
assArray2[tahitskekralovny]=tahitske;
assArray2[venezia]=venezia;
assArray2[voda]=voda;
assArray2[tolerance-zasnubni]=zasnubni;
assArray2[primavera]=primavera;


for key in "${!assArray1[@]}"; do
  echo "$key => ${assArray1[$key]}";
rm ../www/upload/category/${assArray1[$key]}/*;
cp ../www/assets/front/user_content/images/collection/$key/${assArray2[$key]}-bg-kolekce-detail-280x260.jpg  ../www/upload/category/${assArray1[$key]};
cp ../www/assets/front/user_content/images/collection/$key/${assArray2[$key]}-bg-kolekce-detail-1920x470.jpg  ../www/upload/category/${assArray1[$key]};
done

chmod -R 777 ../www/upload/category
