<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once 'vendor/autoload.php';

// require_once 'src/mf/utils/AbstractClassLoader.php';
// require_once 'src/mf/utils/ClassLoader.php';

// $loader = new \mf\utils\ClassLoader('src');
// $loader->register();


use commandapp\model\Carte as Carte;
use commandapp\model\Commande as Commande;
use commandapp\model\Item_commande as Item_commande;
use commandapp\model\Item as Item;
use commandapp\model\Paiement as Paiement;

use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;


$paramsServer = parse_ini_file("conf/conf.ini");

/* une instance de connexion  */
$db = new Illuminate\Database\Capsule\Manager();

$db->addConnection( $paramsServer ); /* configuration avec nos paramètres */
$db->setAsGlobal();            /* rendre la connexion visible dans tout le projet */
$db->bootEloquent();           /* établir la connexion */

// affichage confort lecture de l'execution du code
function displayQuest(string $num) {
    echo PHP_EOL . PHP_EOL . "Question $num" . PHP_EOL . PHP_EOL;
}
/////////////////////////////////////////////////:
//Item_commande.php n'est pas encore écrit

// Test du fonctionnement des models sauf Item_commande
echo 'carte ' . Carte::where('id','=','1')->first() . PHP_EOL;
echo 'commande ' . Commande::where('montant','=','4.50')->first() . PHP_EOL;
echo 'Item ' . Item::where('id','=','1')->first() . PHP_EOL;
echo 'Paiement ' . Paiement::where('montant_paiement','=','32.20')->first() . PHP_EOL;


//// 1. Requêtes simples

// 1. liste des cartes de fidélité, avec le nom, mail du propriétaire et le 
//    montant cumulé,
displayQuest('1.1');

$requeteCarte = Carte::select('nom_proprietaire','mail_proprietaire','cumul');
$lignesCarte = $requeteCarte->get();
foreach ($lignesCarte as $carte) {
    echo "Nom : $carte->nom_proprietaire \n mail : $carte->mail_proprietaire \n cumul : $carte->cumul";
}

// 2. la même liste, trie par ordre alphabétique décroissant du nom
displayQuest('1.2');

$requeteCarteOrd = Carte::select('nom_proprietaire','mail_proprietaire','cumul')
                        ->orderBy('nom_proprietaire','asc');
$lignesCarteOrd = $requeteCarteOrd->get();
foreach ($lignesCarteOrd as $carte) {
    echo "Nom : $carte->nom_proprietaire \n";
}

// 3. la carte n°7342 si elle existe, utiliser ModelNotFoundException 
//    pour gérer le cas où elle n'existe pas,
displayQuest('1.3');

// findOrFail($id) et seulement id vs firstOfFail()
// find raccourci pou where. Sinon alors where() puis firstOrFail (1 elmntd de la coll)


try {
    $c7342 = Carte::findOrFail(7342) ;
} catch (ModelNotFoundException $e) {
    echo "Carte n°7342 pas trouvée. \n";
}

// 4. les cartes dont le nom du propriétaire contient 'Ariane', 
//    triées par montant croissant,
displayQuest('1.4');

$requeteAriane = Carte::select('nom_proprietaire','cumul')
                      ->where('nom_proprietaire','like','%Ariane%')
                      ->orderBy('cumul','asc');
$lignesAriane = $requeteAriane->get();
foreach ($lignesAriane as $carte) {
    echo "$carte->nom_proprietaire, $carte->cumul \n";
}


// 5. Créer une nouvelle carte
displayQuest('1.5');

$carteDupond = new Carte();
$carteDupond->password = 'azerty';
$carteDupond->nom_proprietaire = 'Dupond Dupont';
$carteDupond->mail_proprietaire = 'dupond.dupont@dupond.d';
$carteDupond->cumul = 10;

// $carteDupond->save();

echo "Carte créée \n";


//// 2. Associations 1-n

// 1. afficher la carte n° 42 et ses commandes
displayQuest('2.1');

$carte42 = Carte::find(42);
$commandesCarte42 = $carte42->commandes()->get();

echo("Commande de la carte : $carte42->id $carte->nom_proprietaire \n \n");

foreach ($commandesCarte42 as $commande) {
    echo "Client : $commande->nom_client, montant commande : $commande->montant \n";
}

// 2. lister les cartes dont le montant est > 1000, 
//    et pour chaque carte, lister les commandes associées.
//    utiliser un chargement lié
displayQuest('2.2');

 // chargement lié avec with

$cartesSup1000_commandes = Carte::with('commandes')->where('cumul','>','1000')->get();
// $a = $cartesSup1000_commandes['commandes']; 
// var_dump($cartesSup1000_commandes[4]->commandes[2]); 

echo("Cartes : \n \n");
foreach ($cartesSup1000_commandes as $carte) {
    echo PHP_EOL;
    echo("Carte ===> \n");
    echo("$carte->nom_proprietaire, $carte->cumul \n");
    echo("Commande : \n");
    foreach($carte->commandes as $commande) {
        echo("Client : $commande->nom_client, montant commande : $commande->montant \n");
    }
}


// 3. lister les commandes qui sont associées à une carte, 
//    et pour chacune d'elle les informations concernant la carte,
displayQuest('2.3');

$commandes_associees_a_carte = Commande::whereNotNull('carte_id')->with('carte')->get();
foreach ($commandes_associees_a_carte as $commande) {
    echo PHP_EOL;
    echo("Commande ===> \n");
    echo("Commande : $commande->nom_client, montant commande : $commande->montant \n");
    echo("Client : \n");
    echo($commande->carte->nom_proprietaire . " " . $commande->carte->cumul . " \n");
}

// 4. créer 3 commandes associées à la carte n°10
displayQuest('2.4');

$carte10 = Carte::find(10);

$commande_a_associer_1 = new Commande();
$commande_a_associer_1->id = "newCommande_1";
$commande_a_associer_1->date_livraison = date("Y-m-d H:i:s");
$commande_a_associer_1->montant = 10;
$commande_a_associer_1->etat = 0;
$commande_a_associer_1->nom_client = "Marcel" ;

$commande_a_associer_2 = new Commande();
$commande_a_associer_2->id = "newCommande_2";
$commande_a_associer_2->date_livraison = date("Y-m-d H:i:s");
$commande_a_associer_2->montant = 20;
$commande_a_associer_2->etat = 0;
$commande_a_associer_2->nom_client = "SuperAcarien" ;

$commande_a_associer_3 = new Commande();
$commande_a_associer_3->id = "newCommande 3";
$commande_a_associer_3->date_livraison = date("Y-m-d H:i:s");
$commande_a_associer_3->montant = 30;
$commande_a_associer_3->etat = 0;
$commande_a_associer_3->nom_client = "Djonne Dos" ;

// $carte10->commandes()->save($commande_a_associer_1);
// $carte10->commandes()->save($commande_a_associer_2);
// $carte10->commandes()->save($commande_a_associer_3);

// 5. changer la carte associée à la 3ème commande pour l'associer à la carte 11
displayQuest('2.5');

$commande_new_3 = Commande::find("newCommande 3");
$carte11 = Carte::find(11);
// $commande_new_3->carte()->dissociate();
// $commande_new_3->save();
$commande_new_3->carte()->associate($carte11);
// $commande_new_3->save();

// $commande_a_associer_3->save();

echo "Association de commande 3 changer à carte 11";


//// 3. Associations N-N et attributs d'associations

// 1. lister les items de la commande "000b2a0b-d055-4499-9c1b-84441a254a36
displayQuest('3.1');

$commande_a36 = Commande::find('000b2a0b-d055-4499-9c1b-84441a254a36');
$items_coma36 = $commande_a36->items;
echo "Item de la commande ***a36 : " . PHP_EOL;
foreach ($items_coma36 as $item_com36) {
    echo $item_com36->libelle . PHP_EOL;
}

displayQuest('3.2');

$items = Item::all();
foreach ($items as $item) {
    echo "Item : $item->libelle , commandes associées (par nom de client) : " . PHP_EOL;
    $commandes = $item->commandes;
    foreach ($commandes as $comande) {
        echo "$commande->nom_client";
    }
}



