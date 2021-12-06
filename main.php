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

// Item::find('2')->delete();
$commande_a36 = Commande::find('000b2a0b-d055-4499-9c1b-84441a254a36');
$items_coma36 = $commande_a36->items()->withTrashed()->get();
echo "Item de la commande ***a36 : " . PHP_EOL;
foreach ($items_coma36 as $item_com36) {
    echo $item_com36->libelle . PHP_EOL;
}

displayQuest('3.2');

$items = Item::withTrashed()->get();
foreach ($items as $item) {
    echo "Item : $item->libelle , commandes associées (par nom de client) : " . PHP_EOL;
    $commandes = $item->commandes()->get();
    foreach ($commandes as $comm) {
        echo "-> $comm->id" . ', client: ' . $comm->nom_client . PHP_EOL; 
    }
}

displayQuest('3.3');

$coms_from_Aaron = Commande::where('nom_client','=','Aaron McGlynn')->get();
echo PHP_EOL . "Commandes passé par Aaron MacGlynn :" . PHP_EOL;
foreach ($coms_from_Aaron as $com_from_Aaron) {
    echo $com_from_Aaron->id . PHP_EOL;
    echo "-> Items associés à la commande :" . PHP_EOL;
    foreach ($com_from_Aaron->items()->withTrashed()->get() as $item_for_Aaron) {
        echo "--> " . $item_for_Aaron->libelle . " x" . $item_for_Aaron->item_commande->quantite;
    }
    echo PHP_EOL;
}

// displayQuest('3.4');

// $newComm1 = Commande::find('newCommande_1');
// $newComm1->items()->detach([2,6]); // Only to avoid repetition when executing this script many times
// $newComm1->items()->attach( [
//     2=>['quantite'=>3],
//     6=>['quantite'=>4]
// ]);
// echo "Item 2 et 6 rajoutés en quantité 3 et 4 pour $newComm1->id";
// echo PHP_EOL;
// foreach ($newComm1->items as $it) {
//     echo '-> ' . $it->libelle . ', quantite: ' . $it->item_commande->quantite . PHP_EOL;
// }


// //// 4. Requêtes sur des associations

// displayQuest('4.1');

// $carteAaron = Carte::where('nom_proprietaire','like','Aaron McGlynn')->first();
// $comsAaronZero = $carteAaron->commandes()->where('etat','>','0')->get();
// echo "Commande de Aaron McGlynn, où l'état est > à 0 : " . PHP_EOL;
// foreach ($comsAaronZero as $comAarZero) {
//     echo $comAarZero->id . PHP_EOL;
// }

// displayQuest('4.2');

// $carte28 = Carte::find("28");
// $comsCarte28 = $carte28->commandes()->where('etat','>=','0')->where('montant','>','20')->get();
// echo "Commandes associées à la carte 28, avec état >=0 et montant > 0 :" .PHP_EOL;
// foreach ($comsCarte28 as $comCarte28) {
//     echo $comCarte28->id . ', état : ' . $comCarte28->etat . ', prix : ' . $comCarte28->montant . PHP_EOL;
// }

// displayQuest('4.3');

// $com9f1 = Commande::find("9f1c3241-958a-4d35-a8c9-19eef6a4fab3");
// $items_9f1 = $com9f1->items()->where('tarif','<','5')->get();
// echo 'Items de la commande "9f1c3241-958a-4d35-a8c9-19eef6a4fab3" dont le tarif est < 5.0';
// foreach ($items_9f1 as $item_9f1) {
//     echo $item_9f1->libelle . ', tarif ; ' . $item_9f1->tarif . PHP_EOL;
// }

// displayQuest('4.4');

// $cartes_8 = Carte::has('commandes','>','8')->get();
// echo "Cartes ayant été utilisées pour plus de 8 commandes (id + nom propriétaire) :" . PHP_EOL;
// foreach ($cartes_8 as $carte_8) {
//     echo 'Carte n°' . $carte_8->id . ' appartenant à ' . $carte_8->nom_proprietaire . PHP_EOL;
// }

// displayQuest('4.5');

// // $cartes_comm_3items = Carte::whereHas('commandes', function($c) {
// //     $c->has('items','>','3');
// // })->get();
// // echo "Cartes ayant des commandes de plus de 3 items (id + nom propriétaire) :" . PHP_EOL;
// // foreach ($cartes_comm_3items as $carte_comm_3items) {
// //     echo 'Carte n°' . $carte_comm_3items->id . ' appartenant à ' . $carte_comm_3items->nom_proprietaire . PHP_EOL;
// // }

// displayQuest('4.6');

// $comms_item2 = Commande::whereHas('items', function($i) {
//     $i->where('id','=','2');
// })->get();
// echo "Commandes contenant l'item n°2 (id + nom client) :" . PHP_EOL;
// foreach ($comms_item2 as $comm_item2) {
//     echo $comm_item2->id . ', nom client : ' . $comm_item2->nom_client . PHP_EOL;
// }

// displayQuest('4.7');

// $cartes_com30 = Carte::whereHas('commandes', function($com) {
//     $com->where('montant','>','30.0');
// })->get();
// echo "Cartes ayant des commandes ont un montant > à 30.0 (id + nom propriétaire) :" . PHP_EOL;
// foreach ($cartes_com30 as $carte_com30) {
//     echo 'Carte n°' . $carte_com30->id . ' appartenant à ' . $carte_com30->nom_proprietaire . PHP_EOL;
// }

// displayQuest('4.8');

// $coms_card_3items = Commande::whereNotNull('carte_id')->has('items','>','3')->orderBy('carte_id')->get();
// echo "Commmandes associées à une carte et ayant + 3 items : " . PHP_EOL;
// foreach ($coms_card_3items as $com_card_3items) {
//     echo $com_card_3items->id . ', nom client : ' . $com_card_3items->nom_client . ', carte n°' . $com_card_3items->carte_id . PHP_EOL;

// }


//// 6. Soft Deletes
// Item::find('2')->delete();
// Item::find('7')->delete();


