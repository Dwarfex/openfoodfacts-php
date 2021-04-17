<?php


namespace OpenFoodFacts;


use http\Exception\InvalidArgumentException;

class AdvancedSearch
{
protected string $searchTerm = ''; // Brot
private string $searchTermKey = 'search_terms2';
//multiple tags / 'properties'?


public const TAG_TYPES = [];
public const DOES_NOT_CONTAIN ='does_not_contain';
public const CONTAINS ='contains';
protected $tags = [];
tagtype_0: nova_groups
tag_contains_0: does_not_contain
tag_0: 0
tagtype_1: editors
tag_contains_1: contains
tag_1: 1
tagtype_2: search
tag_contains_2: contains
tag_2:


// indifferent // with// without
public const WITH = 'with';
public const WITHOUT = 'without';
public const INDIFFERENT = 'indifferent';
protected string $additives = 'indifferent';
protected string $ingredients_from_palm_oil = 'indifferent';
protected string $ingredients_that_may_be_from_palm_oil = 'indifferent';
protected string $ingredients_from_or_that_may_be_from_palm_oil = 'indifferent';


additives: with
ingredients_from_palm_oil: without
ingredients_that_may_be_from_palm_oil: with
ingredients_from_or_that_may_be_from_palm_oil: indifferent

    public const LOWER_THEN = 'lt';
    public const LOWER_THEN_EQUAL = 'lte';
    public const GREATER_THEN = 'gt';
    public const GREATER_THEN_EQUAL = 'gtq';
    public const EQUAL = 'eq';
protected array $nutriments= [];
nutriment_0: energy-kcal
nutriment_compare_0: lt
nutriment_value_0: 100
nutriment_1:
nutriment_compare_1: lt
nutriment_value_1:
//

action: process
sort_by: unique_scans_n
page_size: 20
search: Search




    # FIRST CRITERIA (TAG0)

tagtype_0
search_tag # choose a criterion...

# CRITERION
brands # brands
categories # categories
packaging # packaging
labels # labels
origins # origins of ingredients
manufacturing_places # manufacturing or processing places
emb_codes # packager codes
purchase_places # purchase places
stores # stores
countries # countries
additives # additives
allergens # allergens
traces # traces
nutrition_grades # Nutrition grades
states # states

# Contains or Not
tag_contains_0
contains
does_not_contain

# Value of Tag (free text)
tag_0

#additives
additives
without # without_additives
with # with_additives
indifferent # indifferent_additives

# Ingredients from palm oil
ingredients_from_palm_oil
without
with
indifferent

# Ingredients that may be from palm oil
ingredients_that_may_be_from_palm_oil
without
with
indifferent

#ingredients_from_or_that_may_be_from_palm_oi
ingredients_from_or_that_may_be_from_palm_oil
without
with
indifferent


# Nutrients

nutriment_0

search_nutriment # choose a nutriment...

energy # Energy
energy-from-fat # Energy from fat
fat # Fat
saturated-fat # Saturated fat
butyric-acid # Butyric acid (4:0)
caproic-acid # Caproic acid (6:0)
caprylic-acid # Caprylic acid (8:0)
capric-acid # Capric acid (10:0)
lauric-acid # Lauric acid (12:0)
myristic-acid # Myristic acid (14:0)
palmitic-acid # Palmitic acid (16:0)
stearic-acid # Stearic acid (18:0)
arachidic-acid # Arachidic acid (20:0)
behenic-acid # Behenic acid (22:0)
lignoceric-acid # Lignoceric acid (24:0)
cerotic-acid # Cerotic acid (26:0)
montanic-acid # Montanic acid (28:0)
melissic-acid # Melissic acid (30:0)
monounsaturated-fat # Monounsaturated fat
polyunsaturated-fat # Polyunsaturated fat
omega-3-fat # Omega 3 fatty acids
alpha-linolenic-acid # Alpha-linolenic acid / ALA (18:3 n-3)
eicosapentaenoic-acid # Eicosapentaenoic acid / EPA (20:5 n-3)
docosahexaenoic-acid # Docosahexaenoic acid / DHA (22:6 n-3)
omega-6-fat # Omega 6 fatty acids
linoleic-acid # Linoleic acid / LA (18:2 n-6)
arachidonic-acid # Arachidonic acid / AA / ARA (20:4 n-6)
gamma-linolenic-acid # Gamma-linolenic acid / GLA (18:3 n-6)
dihomo-gamma-linolenic-acid # Dihomo-gamma-linolenic acid / DGLA (20:3 n-6)
omega-9-fat # Omega 9 fatty acids
oleic-acid # Oleic acid (18:1 n-9)
elaidic-acid # Elaidic acid (18:1 n-9)
gondoic-acid # Gondoic acid (20:1 n-9)
mead-acid # Mead acid (20:3 n-9)
erucic-acid # Erucic acid (22:1 n-9)
nervonic-acid # Nervonic acid (24:1 n-9)
trans-fat # Trans fat
cholesterol # Cholesterol
carbohydrates # Carbohydrate
sugars # Sugars
sucrose # Sucrose
glucose # Glucose
fructose # Fructose
lactose # Lactose
maltose # Maltose
maltodextrins # Maltodextrins
starch # Starch
polyols # Sugar alcohols (Polyols)
fiber # Dietary fiber
proteins # Proteins
casein # casein
serum-proteins # Serum proteins
nucleotides # Nucleotides
salt # Salt
sodium # Sodium
alcohol # Alcohol
vitamin-a # Vitamin A
beta-carotene # Beta carotene
vitamin-d # Vitamin D
vitamin-e # Vitamin E
vitamin-k # Vitamin K
vitamin-c # Vitamin C (ascorbic acid)
vitamin-b1 # Vitamin B1 (Thiamin)
vitamin-b2 # Vitamin B2 (Riboflavin)
vitamin-pp # Vitamin B3 / Vitamin PP (Niacin)
vitamin-b6 # Vitamin B6 (Pyridoxin)
vitamin-b9 # Vitamin B9 (Folic acid / Folates)
vitamin-b12 # Vitamin B12 (cobalamin)
biotin # Biotin
pantothenic-acid # Pantothenic acid / Pantothenate (Vitamin B5)
silica # Silica
bicarbonate # Bicarbonate
potassium # Potassium
chloride # Chloride
calcium # Calcium
phosphorus # Phosphorus
iron # Iron
magnesium # Magnesium
zinc # Zinc
copper # Copper
manganese # Manganese
fluoride # Fluoride
selenium # Selenium
chromium # Chromium
molybdenum # Molybdenum
iodine # Iodine
caffeine # Caffeine
taurine # Taurine
ph # pH
fruits-vegetables-nuts # Fruits, vegetables and nuts (minimum)
collagen-meat-protein-ratio # Collagen/Meat protein ratio (maximum)
cocoa # Cocoa (minimum)
chlorophyl # Chlorophyl
carbon-footprint # Carbon footprint / CO2 emissions
nutrition-score-fr # Experimental nutrition score
nutrition-score-uk # Nutrition score - UK

# Comparison of nutrients
# Nutriment to compare
nutriment_compare_0

# Operator
lt # less than
lte # less than or equal
gt # greater than
gte # greater than or equal
eq # equal to

# Value to compare the nutrients to
nutriment_value_0

# Output
sort_by # sort_by
unique_scans_n # Popularity
product_name # Product name
created_t # Add date
last_modified_t # Edit date
}