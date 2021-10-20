<?php

namespace commandapp\model;


class Commande extends \Illuminate\Database\Eloquent\Model {

       protected $table      = 'commande';  /* le nom de la table */
       protected $primaryKey = 'id';     /* le nom de la clé primaire */
       public    $timestamps = true;    /* si vrai la table doit contenir
                                            les deux colonnes updated_at,
                                            created_at */

        public $incrementing = false;
        public $keyType='string';

        public function carte() {
                return $this->belongsTo('commandapp\model\Carte','carte_id');
        }

        public function items() {
                return $this->belongsToMany('commandapp\model\Item',
                                             'item_commande',
                                             'commande_id',
                                             'item_id')
                        ->withPivot('quantite') // mettre dans [] ?
                        ->as('item_commande');
        }

        // NOTÉ !!!!!!!!!
        // REGARDER FIN SLIDE : MODEL DIFFEREBT D ACTIVE RECORD (dotrine, hibernate)
        // TD 2 A RENDRE ASSUI -> A RENDRE FIN DU MODULE
        // REGARDER VIDEO GENERER AUTOLOADER AVEC COMPOSER
        // ON UTILISERA L AUTOLOADER DE COMPOSER POUR LE MICRO ORM
        
}