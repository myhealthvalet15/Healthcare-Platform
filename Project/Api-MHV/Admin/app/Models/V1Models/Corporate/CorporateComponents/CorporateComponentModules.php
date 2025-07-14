<?php

namespace App\Models\V1Models\Corporate\CorporateComponents;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporateComponentModules extends Model
{
    use HasFactory;
    protected $table = 'corporate_component_modules';
    protected $fillable=['module_id','module_name']; 
    public function subModules()
    {
        return $this->hasMany(CorporateComponentSubmodules::class, 'module_id', 'module_id');
    }
}
