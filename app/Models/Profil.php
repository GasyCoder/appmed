<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profil extends Model
{

   use SoftDeletes;
   protected $fillable = [
       'user_id',
       'sexe',
       'grade',
       'telephone',
       'adresse',
       'ville',
       'departement'
   ];

   protected $dates = ['deleted_at'];

   protected $casts = [
       'sexe' => 'string'
   ];

   public function user()
   {
       return $this->belongsTo(User::class);
   }

   public static function getGrades()
   {
       return [
           'Prof',
           'Dr',
           'Mr',
           'Mme',
           'Mlle'
       ];
   }
}
