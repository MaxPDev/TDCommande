<?php

namespace tweeterapp\model;

class User extends \Illuminate\Database\Eloquent\Model {

       protected $table      = 'user';  /* le nom de la table */
       protected $primaryKey = 'id';     /* le nom de la clé primaire */
       public    $timestamps = false;    /* si vrai la table doit contenir
                                            les deux colonnes updated_at,
                                            created_at */

       public function tweets() {
              return $this->hasMany('tweeterapp\model\Tweet', 'author');
              /* 'Tweet'     : le nom de la classe du modèle lié   */
              /* 'author' : la clé étrangère dans la table liée */
       }

       public function liked() {
              return $this->belongsToMany('tweeterapp\model\Tweet',
                                         'tweeterapp\model\Like',
                                         'user_id',
                                         'tweet_id');
              
       /* 'Tweet'          : le nom de la classe du model lié */
       /* 'Like ' : le nom de la table pivot */

       /* 'user_id'     : la clé étrangère de ce modèle dans la table pivot */
       /* 'tweet_id'        : la clé étrangère du modèle lié dans la table pivot */
       }

       public function followedBy() {
              return $this->belongsToMany('tweeterapp\model\User',
                                          'tweeterapp\model\Follow',
                                          'followee',
                                          'follower'
       );
       /* 'User'          : le nom de la classe du model lié */
       /* 'Follow' : le nom de la table pivot */

       /* 'followee'     : la clé étrangère de ce modèle dans la table pivot */
       /* 'follower'        : la clé étrangère du modèle lié dans la table pivot */
       }

       public function follows() {
              return $this->belongsToMany('tweeterapp\model\User',
                                          'tweeterapp\model\Follow',
                                          'follower',
                                          'followee'
       );
              /* 'User'          : le nom de la classe du model lié */
       /* 'Follow' : le nom de la table pivot */

       /* 'follower'     : la clé étrangère de ce modèle dans la table pivot */
       /* 'followee'        : la clé étrangère du modèle lié dans la table pivot */
       }
}