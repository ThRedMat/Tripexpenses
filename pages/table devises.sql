-- Création de la table currencies
CREATE TABLE IF NOT EXISTS currencies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(3) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    symbol VARCHAR(10),
    country VARCHAR(100),
    union_flag VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci

-- Insertion des devises mondiales
INSERT INTO currencies (code, name, symbol, country, union_flag) VALUES
/*** Amériques ***/

('USD', 'Dollar américain', '$', 'États-Unis', '/Tripexpenses/images/drapeaux/amériques/us.png'),
('CAD', 'Dollar canadien', 'C$', 'Canada', '/Tripexpenses/images/drapeaux/amériques/cad.png'),
('MXN', 'Peso mexicain', '$', 'Mexique', '/Tripexpenses/images/drapeaux/amériques/mx.png'),
('BRL', 'Real brésilien', 'R$', 'Brésil', '/Tripexpenses/images/drapeaux/amériques/br.png'),
('ARS', 'Peso argentin', '$', 'Argentine', '/Tripexpenses/images/drapeaux/amériques/ar.png'),
('CLP', 'Peso chilien', '$', 'Chili', '/Tripexpenses/images/drapeaux/amériques/cl.png'),
('COP', 'Peso colombien', '$', 'Colombie', '/Tripexpenses/images/drapeaux/amériques/co.png'),
('PEN', 'Sol péruvien', 'S/', 'Pérou', '/Tripexpenses/images/drapeaux/amériques/pe.png'),
('UYU', 'Peso uruguayen', '$U', 'Uruguay', '/Tripexpenses/images/drapeaux/amériques/uy.png'),
('BOB', 'Boliviano', 'Bs.', 'Bolivie', '/Tripexpenses/images/drapeaux/amériques/bo.png'),    
('PYG', 'Guaraní paraguayen', '₲', 'Paraguay', '/Tripexpenses/images/drapeaux/amériques/py.png'),
('VES', 'Bolívar vénézuélien', 'Bs.', 'Venezuela', '/Tripexpenses/images/drapeaux/amériques/ve.png'),
('DOP', 'Peso dominicain', 'RD$', 'République dominicaine', '/Tripexpenses/images/drapeaux/amériques/do.png'),
('CRC', 'Colón costaricain', '₡', 'Costa Rica', '/Tripexpenses/images/drapeaux/amériques/cr.png'),
('GTQ', 'Quetzal guatémaltèque', 'Q', 'Guatemala', '/Tripexpenses/images/drapeaux/amériques/gt.png'),
('HNL', 'Lempira hondurien', 'L', 'Honduras', '/Tripexpenses/images/drapeaux/amériques/hn.png'),
('NIO', 'Córdoba nicaraguayen', 'C$', 'Nicaragua', '/Tripexpenses/images/drapeaux/amériques/ni.png'),
('PAB', 'Balboa panaméen', 'B/.', 'Panama', '/Tripexpenses/images/drapeaux/amériques/pa.png'),
('CUP', 'Peso cubain', '$', 'Cuba', '/Tripexpenses/images/drapeaux/amériques/cu.png'),
('JMD', 'Dollar jamaïcain', 'J$', 'Jamaïque', '/Tripexpenses/images/drapeaux/amériques/jm.png'),
('TTD', 'Dollar de Trinité-et-Tobago', 'TT$', 'Trinité-et-Tobago', '/Tripexpenses/images/drapeaux/amériques/tt.png'),
('BBD', 'Dollar barbadien', 'Bds$', 'Barbade', '/Tripexpenses/images/drapeaux/amériques/bb.png'),
('BSD', 'Dollar bahaméen', 'B$', 'Bahamas', '/Tripexpenses/images/drapeaux/amériques/bs.png'),
('HTG', 'Gourde haïtienne', 'G', 'Haïti', '/Tripexpenses/images/drapeaux/amériques/ht.png'),
 

/*** Europe ***/
/** Manque téléchargement des drapeaux **/
('EUR', 'Euro', '€', 'Zone Euro', '/Tripexpenses/images/drapeaux/europe/eu.png'),
('GBP', 'Livre sterling', '£', 'Royaume-Uni', '/Tripexpenses/images/drapeaux/europe/uk.png'),
('CHF', 'Franc suisse', 'CHF', 'Suisse', '/Tripexpenses/images/drapeaux/europe/ch.png'),
('NOK', 'Couronne norvégienne', 'kr', 'Norvège', '/Tripexpenses/images/drapeaux/europe/no.png'),
('SEK', 'Couronne suédoise', 'kr', 'Suède', '/Tripexpenses/images/drapeaux/europe/se.png'),
('DKK', 'Couronne danoise', 'kr', 'Danemark', '/Tripexpenses/images/drapeaux/europe/dk.png'),
('PLN', 'Zloty polonais', 'zł', 'Pologne', '/Tripexpenses/images/drapeaux/europe/pl.png'),
('CZK', 'Couronne tchèque', 'Kč', 'République tchèque', '/Tripexpenses/images/drapeaux/europe/cz.png'),
('HUF', 'Forint hongrois', 'Ft', 'Hongrie', '/Tripexpenses/images/drapeaux/europe/hu.png'),
('RON', 'Leu roumain', 'lei', 'Roumanie', '/Tripexpenses/images/drapeaux/europe/ro.png'),
('BGN', 'Lev bulgare', 'лв', 'Bulgarie', '/Tripexpenses/images/drapeaux/europe/bg.png'),
('HRK', 'Kuna croate', 'kn', 'Croatie', '/Tripexpenses/images/drapeaux/europe/hr.png'),
('RUB', 'Rouble russe', '₽', 'Russie', '/Tripexpenses/images/drapeaux/europe/ru.png'),
('UAH', 'Hryvnia ukrainienne', '₴', 'Ukraine', '/Tripexpenses/images/drapeaux/europe/ua.png'),
('TRY', 'Livre turque', '₺', 'Turquie', '/Tripexpenses/images/drapeaux/europe/tr.png'),
('ISK', 'Couronne islandaise', 'kr', 'Islande', '/Tripexpenses/images/drapeaux/europe/is.png'),
('RSD', 'Dinar serbe', 'дин.', 'Serbie', '/Tripexpenses/images/drapeaux/europe/rs.png'),
('MKD', 'Denar macédonien', 'ден', 'Macédoine du Nord', '/Tripexpenses/images/drapeaux/europe/mk.png'),
('ALL', 'Lek albanais', 'L', 'Albanie', '/Tripexpenses/images/drapeaux/europe/al.png'),
('BAM', 'Mark convertible', 'KM', 'Bosnie-Herzégovine', '/Tripexpenses/images/drapeaux/europe/ba.png'),
('MDL', 'Leu moldave', 'L', 'Moldavie', '/Tripexpenses/images/drapeaux/europe/md.png'),
('GEL', 'Lari géorgien', '₾', 'Géorgie', '/Tripexpenses/images/drapeaux/europe/ge.png'),
('BYN', 'Rouble biélorusse', 'Br', 'Biélorussie', '/Tripexpenses/images/drapeaux/europe/by.png'),

/*** Asie ***/
('CNY', 'Yuan chinois', '¥', 'Chine', '/Tripexpenses/images/drapeaux/asie/cn.png'),
('JPY', 'Yen japonais', '¥', 'Japon', '/Tripexpenses/images/drapeaux/asie/jp.png'),
('THB', 'Baht thaïlandais', '฿', 'Thaïlande', '/Tripexpenses/images/drapeaux/asie/th.png'),
('KRW', 'Won sud-coréen', '₩', 'Corée du Sud', '/Tripexpenses/images/drapeaux/asie/kr.png'),
('INR', 'Roupie indienne', '₹', 'Inde', '/Tripexpenses/images/drapeaux/asie/in.png'),
('IDR', 'Roupie indonésienne', 'Rp', 'Indonésie', '/Tripexpenses/images/drapeaux/asie/id.png'),
('MYR', 'Ringgit malaisien', 'RM', 'Malaisie', '/Tripexpenses/images/drapeaux/asie/my.png'),
('SGD', 'Dollar de Singapour', 'S$', 'Singapour', '/Tripexpenses/images/drapeaux/asie/sg.png'),
('PHP', 'Peso philippin', '₱', 'Philippines', '/Tripexpenses/images/drapeaux/asie/ph.png'),
('VND', 'Dong vietnamien', '₫', 'Vietnam', '/Tripexpenses/images/drapeaux/asie/vn.png'),
('PKR', 'Roupie pakistanaise', '₨', 'Pakistan', '/Tripexpenses/images/drapeaux/asie/pk.png'),
('BDT', 'Taka bangladais', '৳', 'Bangladesh', '/Tripexpenses/images/drapeaux/asie/bd.png'),
('LKR', 'Roupie srilankaise', 'Rs', 'Sri Lanka', '/Tripexpenses/images/drapeaux/asie/lk.png'),
('MMK', 'Kyat birman', 'K', 'Myanmar', '/Tripexpenses/images/drapeaux/asie/mm.png'),
('KHR', 'Riel cambodgien', '៛', 'Cambodge', '/Tripexpenses/images/drapeaux/asie/kh.png'),
('LAK', 'Kip laotien', '₭', 'Laos', '/Tripexpenses/images/drapeaux/asie/la.png'),
('BND', 'Dollar de Brunei', 'B$', 'Brunei', '/Tripexpenses/images/drapeaux/asie/bn.png'),
('TWD', 'Dollar taïwanais', 'NT$', 'Taïwan', '/Tripexpenses/images/drapeaux/asie/tw.png'),
('HKD', 'Dollar de Hong Kong', 'HK$', 'Hong Kong', '/Tripexpenses/images/drapeaux/asie/hk.png'),
('MOP', 'Pataca macanaise', 'MOP$', 'Macao', '/Tripexpenses/images/drapeaux/asie/mo.png'),
('KPW', 'Won nord-coréen', '₩', 'Corée du Nord', '/Tripexpenses/images/drapeaux/asie/kp.png'),
('MNT', 'Tugrik mongol', '₮', 'Mongolie', '/Tripexpenses/images/drapeaux/asie/mn.png'),
('NPR', 'Roupie népalaise', 'Rs', 'Népal', '/Tripexpenses/images/drapeaux/asie/np.png'),
('BTN', 'Ngultrum bhoutanais', 'Nu.', 'Bhoutan', '/Tripexpenses/images/drapeaux/asie/bt.png'),
('MVR', 'Rufiyaa maldivienne', 'Rf', 'Maldives', '/Tripexpenses/images/drapeaux/asie/mv.png'),
('AFN', 'Afghani afghan', '؋', 'Afghanistan', '/Tripexpenses/images/drapeaux/asie/af.png'),

/*** Moyen-Orient ***/

('AED', 'Dirham des Émirats', 'د.إ', 'Émirats arabes unis', '/Tripexpenses/images/drapeaux/moyen-orient/uae.png'),
('SAR', 'Riyal saoudien', '﷼', 'Arabie saoudite', '/Tripexpenses/images/drapeaux/moyen-orient/sa.png'),
('ILS', 'Shekel israélien', '₪', 'Israël', '/Tripexpenses/images/drapeaux/moyen-orient/il.png'),
('IRR', 'Rial iranien', '﷼', 'Iran', '/Tripexpenses/images/drapeaux/moyen-orient/ir.png'),
('IQD', 'Dinar irakien', 'ع.د', 'Irak', '/Tripexpenses/images/drapeaux/moyen-orient/iq.png'),
('JOD', 'Dinar jordanien', 'د.ا', 'Jordanie', '/Tripexpenses/images/drapeaux/moyen-orient/jo.png'),
('KWD', 'Dinar koweïtien', 'د.ك', 'Koweït', '/Tripexpenses/images/drapeaux/moyen-orient/kw.png'),
('LBP', 'Livre libanaise', 'ل.ل', 'Liban', '/Tripexpenses/images/drapeaux/moyen-orient/lb.png'),
('OMR', 'Rial omanais', 'ر.ع.', 'Oman', '/Tripexpenses/images/drapeaux/moyen-orient/om.png'),
('QAR', 'Riyal qatari', 'ر.ق', 'Qatar', '/Tripexpenses/images/drapeaux/moyen-orient/qa.png'),
('SYP', 'Livre syrienne', '£S', 'Syrie', '/Tripexpenses/images/drapeaux/moyen-orient/sy.png'),
('YER', 'Rial yéménite', '﷼', 'Yémen', '/Tripexpenses/images/drapeaux/moyen-orient/ye.png'),
('BHD', 'Dinar bahreïni', 'ب.د', 'Bahreïn', '/Tripexpenses/images/drapeaux/moyen-orient/bh.png'),


/*** Afrique ***/
('MAD', 'Dirham marocain', 'د.م.', 'Maroc', '/Tripexpenses/images/drapeaux/afrique/maroc.png'),
('TND', 'Dinar tunisien', 'د.ت', 'Tunisie', '/Tripexpenses/images/drapeaux/afrique/tunisie.png'),
('ZAR', 'Rand sud-africain', 'R', 'Afrique du Sud', '/Tripexpenses/images/drapeaux/afrique/za.png'),
('EGP', 'Livre égyptienne', '£', 'Égypte', '/Tripexpenses/images/drapeaux/afrique/eg.png'),
('NGN', 'Naira nigérian', '₦', 'Nigeria', '/Tripexpenses/images/drapeaux/afrique/ng.png'),
('KES', 'Shilling kényan', 'KSh', 'Kenya', '/Tripexpenses/images/drapeaux/afrique/ke.png'),
('GHS', 'Cedi ghanéen', '₵', 'Ghana', '/Tripexpenses/images/drapeaux/afrique/gh.png'),
('TZS', 'Shilling tanzanien', 'TSh', 'Tanzanie', '/Tripexpenses/images/drapeaux/afrique/tz.png'),
('UGX', 'Shilling ougandais', 'USh', 'Ouganda', '/Tripexpenses/images/drapeaux/afrique/ug.png'),
('ETB', 'Birr éthiopien', 'Br', 'Éthiopie', '/Tripexpenses/images/drapeaux/afrique/et.png'),
('DZD', 'Dinar algérien', 'د.ج', 'Algérie', '/Tripexpenses/images/drapeaux/afrique/dz.png'),
('AOA', 'Kwanza angolais', 'Kz', 'Angola', '/Tripexpenses/images/drapeaux/afrique/ao.png'),
('XOF', 'Franc CFA (BCEAO)', 'CFA', 'Afrique de l''Ouest', '/Tripexpenses/images/drapeaux/afrique/xof.png'),
('XAF', 'Franc CFA (BEAC)', 'FCFA', 'Afrique centrale', '/Tripexpenses/images/drapeaux/afrique/xaf.png'),
('MUR', 'Roupie mauricienne', '₨', 'Maurice', '/Tripexpenses/images/drapeaux/afrique/mu.png'),
('SCR', 'Roupie seychelloise', '₨', 'Seychelles', '/Tripexpenses/images/drapeaux/afrique/sc.png'),
('MGA', 'Ariary malgache', 'Ar', 'Madagascar', '/Tripexpenses/images/drapeaux/afrique/mg.png'),
('ZMW', 'Kwacha zambien', 'ZK', 'Zambie', '/Tripexpenses/images/drapeaux/afrique/zm.png'),
('BWP', 'Pula botswanais', 'P', 'Botswana', '/Tripexpenses/images/drapeaux/afrique/bw.png'),
('MZN', 'Metical mozambicain', 'MT', 'Mozambique', '/Tripexpenses/images/drapeaux/afrique/mz.png'),
('NAD', 'Dollar namibien', 'N$', 'Namibie', '/Tripexpenses/images/drapeaux/afrique/na.png'),
('SZL', 'Lilangeni swazi', 'L', 'Eswatini', '/Tripexpenses/images/drapeaux/afrique/sz.png'),
('LSL', 'Loti lesothan', 'L', 'Lesotho', '/Tripexpenses/images/drapeaux/afrique/ls.png'),
('RWF', 'Franc rwandais', 'FRw', 'Rwanda', '/Tripexpenses/images/drapeaux/afrique/rw.png'),
('BIF', 'Franc burundais', 'FBu', 'Burundi', '/Tripexpenses/images/drapeaux/afrique/bi.png'),
('SOS', 'Shilling somalien', 'Sh', 'Somalie', '/Tripexpenses/images/drapeaux/afrique/so.png'),
('SDG', 'Livre soudanaise', '£', 'Soudan', '/Tripexpenses/images/drapeaux/afrique/sd.png'),
('SSP', 'Livre sud-soudanaise', '£', 'Soudan du Sud', '/Tripexpenses/images/drapeaux/afrique/ss.png'),
('LYD', 'Dinar libyen', 'ل.د', 'Libye', '/Tripexpenses/images/drapeaux/afrique/ly.png'),
('MWK', 'Kwacha malawite', 'MK', 'Malawi', '/Tripexpenses/images/drapeaux/afrique/mw.png'),
('GMD', 'Dalasi gambien', 'D', 'Gambie', '/Tripexpenses/images/drapeaux/afrique/gm.png'),
('SLL', 'Leone sierra-léonais', 'Le', 'Sierra Leone', '/Tripexpenses/images/drapeaux/afrique/sl.png'),
('LRD', 'Dollar libérien', 'L$', 'Liberia', '/Tripexpenses/images/drapeaux/afrique/lr.png'),
('GNF', 'Franc guinéen', 'FG', 'Guinée', '/Tripexpenses/images/drapeaux/afrique/gn.png'),
('CVE', 'Escudo cap-verdien', '$', 'Cap-Vert', '/Tripexpenses/images/drapeaux/afrique/cv.png'),
('STN', 'Dobra santoméen', 'Db', 'Sao Tomé-et-Principe', '/Tripexpenses/images/drapeaux/afrique/st.png'),
('DJF', 'Franc djiboutien', 'Fdj', 'Djibouti', '/Tripexpenses/images/drapeaux/afrique/dj.png'),
('ERN', 'Nakfa érythréen', 'Nfk', 'Érythrée', '/Tripexpenses/images/drapeaux/afrique/er.png'),
('MRU', 'Ouguiya mauritanien', 'UM', 'Mauritanie', '/Tripexpenses/images/drapeaux/afrique/mr.png'),
('KMF', 'Franc comorien', 'CF', 'Comores', '/Tripexpenses/images/drapeaux/afrique/km.png'),


/*** Océanie ***/
('AUD', 'Dollar australien', 'A$', 'Australie', '/Tripexpenses/images/drapeaux/oceanie/au.png'),
('NZD', 'Dollar néo-zélandais', 'NZ$', 'Nouvelle-Zélande', '/Tripexpenses/images/drapeaux/oceanie/nz.png'),
('FJD', 'Dollar fidjien', 'FJ$', 'Fidji', '/Tripexpenses/images/drapeaux/oceanie/fj.png'),
('PGK', 'Kina papou', 'K', 'Papouasie-Nouvelle-Guinée', '/Tripexpenses/images/drapeaux/oceanie/pg.png'),
('WST', 'Tala samoan', 'WS$', 'Samoa', '/Tripexpenses/images/drapeaux/oceanie/ws.png'),
('TOP', 'Paʻanga tongien', 'T$', 'Tonga', '/Tripexpenses/images/drapeaux/oceanie/to.png'),
('VUV', 'Vatu vanuatuan', 'VT', 'Vanuatu', '/Tripexpenses/images/drapeaux/oceanie/vu.png'),
('SBD', 'Dollar des Salomon', 'SI$', 'Îles Salomon', '/Tripexpenses/images/drapeaux/oceanie/sb.png'),
('XPF', 'Franc Pacifique', '₣', 'Polynésie française', '/Tripexpenses/images/drapeaux/oceanie/pf.png'),


/*** Asie centrale ***/

('KZT', 'Tenge kazakh', '₸', 'Kazakhstan', '/Tripexpenses/images/drapeaux/asie_centrale/kz.png'),
('UZS', 'Sum ouzbek', 'so''m', 'Ouzbékistan', '/Tripexpenses/images/drapeaux/asie_centrale/uz.png'),
('TJS', 'Somoni tadjik', 'ЅМ', 'Tadjikistan', '/Tripexpenses/images/drapeaux/asie_centrale/tj.png'),
('TMT', 'Manat turkmène', 'T', 'Turkménistan', '/Tripexpenses/images/drapeaux/asie_centrale/tm.png'),
('KGS', 'Som kirghize', 'с', 'Kirghizistan', '/Tripexpenses/images/drapeaux/asie_centrale/kg.png'),
('AZN', 'Manat azerbaïdjanais', '₼', 'Azerbaïdjan', '/Tripexpenses/images/drapeaux/asie_centrale/az.png'),
('AMD', 'Dram arménien', '֏', 'Arménie', '/Tripexpenses/images/drapeaux/asie_centrale/am.png'),

/*** Index pour améliorer les performances ***/
CREATE INDEX idx_code ON currencies(code),
CREATE INDEX idx_country ON currencies(country),

ALTER TABLE currencies
ADD COLUMN is_main TINYINT(1) DEFAULT 0 AFTER country;

UPDATE currencies
SET is_main = 1
WHERE code IN ('USD','EUR','JPY','GBP','CNY','AUD','CAD','CHF','HKD','SGD');
