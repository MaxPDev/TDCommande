<?php

namespace commandapp\model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Carte extends \Illuminate\Database\Eloquent\Model {

       use SoftDeletes;

       protected $table      = 'carte';  /* le nom de la table */
       protected $primaryKey = 'id';     /* le nom de la clé primaire */
       public    $timestamps = true;    /* si vrai la table doit contenir
                                            les deux colonnes updated_at,
                                            created_at */

       protected $dates = ['deleted_at'];

       public function commandes() {
              return $this->hasMany('commandapp\model\Commande', 'carte_id');
       }

}