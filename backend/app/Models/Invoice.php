<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentTerms;

class Invoice extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'clientName',
        'clientEmail',
        'paymentTerms',
        'createdAt',
        'paymentDue',
        'items',
        'status',
        'total',
        'senderAddress',
        'clientAddress',
        'description',
    ];

    protected $casts = [
        'items' => 'array',
        'createdAt' => 'datetime',
        'paymentDue' => 'datetime',
        'senderAddress' => 'array',
        'clientAddress' => 'array',
        'status' => InvoiceStatus::class,
        'paymentTerms' => PaymentTerms::class,
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($invoice) {
            if (empty($invoice->id)) {
                do {
                    $letters = chr(random_int(65, 90)) . chr(random_int(65, 90)); // A-Z
                    $digits = str_pad((string)random_int(0, 9999), 4, '0', STR_PAD_LEFT);
                    $id = $letters . $digits;
                } while (self::where('id', $id)->exists());
                $invoice->id = $id;
            }
        });
    }
}
