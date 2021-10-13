<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once 'src/mf/utils/AbstractClassLoader.php';
require_once 'src/mf/utils/ClassLoader.php';

require_once 'vendor/autoload.php';

$loader = new \mf\utils\ClassLoader('src');
$loader->register();

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

// $requeteAriane = Carte::select('nom_proprietaire','montant')
//                       ->where()
//                         // $like$ + orderBy Asc
//                       //finir 1 et 2