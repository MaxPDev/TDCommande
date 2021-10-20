<?php

namespace commandapp\model;


class Item extends \Illuminate\Database\Eloquent\Model {

       protected $table      = 'item';  /* le nom de la table */
       protected $primaryKey = 'id';     /* le nom de la clé primaire */
       public    $timestamps = false;    /* si vrai la table doit contenir
                                            les deux colonnes updated_at,
                                            created_at */

       
       public function items() {
       return $this->belongsToMany('commandapp\model\Commande',
                                          'item_commande',
                                          'idem_id',
                                          'commande_id');
       }
       

}