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



$paramsServer = parse_ini_file("conf/conf.ini");

/* une instance de connexion  */
$db = new Illuminate\Database\Capsule\Manager();

$db->addConnection( $paramsServer ); /* configuration avec nos paramètres */
$db->setAsGlobal();            /* rendre la connexion visible dans tout le projet */
$db->bootEloquent();           /* établir la connexion */


/////////////////////////////////////////////////:
//Item_commande.php n'est pas encore écrit

// Test de fonctionnement des models sauf Item_commande
echo 'carte ' . Carte::where('id','=','1')->first() . PHP_EOL;
echo 'commande ' . Commande::where('montant','=','4.50')->first() . PHP_EOL;
echo 'Item ' . Item::where('id','=','1')->first() . PHP_EOL;
echo 'Paiement ' . Paiement::where('montant_paiement','=','32.20')->first() . PHP_EOL;


