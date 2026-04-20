<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Ensure Eloquent uses your custom column names
    const CREATED_AT = 'createdat';
    const UPDATED_AT = 'updatedat';

    // protected $table = 'products'; // Ensure this matches your DB table name
 
    protected $fillable = [
        'category', 'category_id', 'descriptions', 'qty', 'unit', 
        'costprice', 'sellprice', 'saleprice', 'productpicture', 
        'alertstocks', 'criticalstocks'
    ];

    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }
}


// class Product extends Model
// {
//     use HasFactory;
//     /**
//      * The attributes that are mass assignable.
//      *
//      * @var list<string>
//      */
//     protected $fillable = [
//         'category',
//         'category_id',
//         'descriptions',        
//         'qty',
//         'unit',
//         'costprice',
//         'sellprice',
//         'saleprice',
//         'productpicture',
//         'alertstocks',
//         'criticalstocks',
//         'createdat',
//         'updatedat'        
//     ];

//     public function categories() {
//         return $this->belongsToMany(Category::class);
//     }    

// }
