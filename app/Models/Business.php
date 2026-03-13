<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function strlen;

class Business extends Model
{
    use HasFactory;

    protected $table = 'business';

    protected $fillable = [
        'nit',
        'nombre',
        'plan_id',
        'dui',
        'registrofe_id',
        'telefono',
        'correo_responsable',
        'nombre_responsable',
        'posmode',
        'show_special_prices',
        'price_variants_enabled',
        'has_customer_branches',
        'pos_inventory_enabled',
        'invoice_bag_enabled',
        'has_api_access',
        'api_key_hash',
        'api_key_last4',
        'api_key_created_at',
        'active',
        'sac_report'
    ];

    protected $casts = [
        'posmode' => 'boolean',
        'show_special_prices' => 'boolean',
        'price_variants_enabled' => 'boolean',
        'has_customer_branches' => 'boolean',
        'pos_inventory_enabled' => 'boolean',
        'invoice_bag_enabled' => 'boolean',
        'has_api_access' => 'boolean',
        'api_key_created_at' => 'datetime',
        'active' => 'boolean',
        'sac_report' => 'boolean',
    ];

    public function priceVariants()
    {
        return $this->hasMany(BusinessPriceVariant::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function users()
    {
        return $this->hasMany(BusinessUser::class);
    }

    public function sucursales()
    {
        return $this->hasMany(Sucursal::class, 'business_id');
    }

    public function products()
    {
        return $this->hasMany(BusinessProduct::class, 'business_id');
    }

    public function invoiceBags()
    {
        return $this->hasMany(InvoiceBag::class, 'business_id');
    }

    public function getFormattedNitAttribute()
    {
        $nit = $this->attributes['nit'];

        if (strlen($nit) === 14) {
            return substr($nit, 0, 4) . '-' . substr($nit, 4, 6) . '-' . substr($nit, 10, 3) . '-' . substr($nit, 13, 1);
        } else if (strlen($nit) === 9) {
            return substr($nit, 0, 8) . '-' . substr($nit, 7, 1);
        }

        return $nit;
    }

    public function getFormattedDuiAttribute()
    {
        $dui = $this->attributes['dui'];

        if (strlen($dui) === 9) {
            return substr($dui, 0, 8) . '-' . substr($dui, 8, 1);
        }

        return $dui;
    }
}
