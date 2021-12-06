<?php

namespace commandapp\model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends \Illuminate\Database\Eloquent\Model {

       use SoftDeletes;

       protected $table      = 'item';  /* le nom de la table */
       protected $primaryKey = 'id';     /* le nom de la clé primaire */
       public    $timestamps = false;    /* si vrai la table doit contenir
                                            les deux colonnes updated_at,
                                            created_at */

       protected $dates = ['deleted_at'];
       
       public function commandes() {
       return $this->belongsToMany('commandapp\model\Commande',
                                          'item_commande',
                                          'item_id',
                                          'commande_id')
                     ->withPivot('quantite')
                     ->as('item_commande');
       }
       

}