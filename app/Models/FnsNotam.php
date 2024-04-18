<?php

namespace App\Models;

use App\Casts\FromFNSXmlCast;
use App\Enum\Fns\FNSNotamClassification;
use App\Enum\Fns\FNSNotamStatus;
use Illuminate\Database\Eloquent\Model;

class FnsNotam extends Model
{
    public $timestamps = false;

    protected $connection = 'pgsql';

    protected $table = 'notams';

    protected $primaryKey = 'fnsid';

    protected $guarded = [];

    protected $visible = [
        'fnsid',
        'correlationid',
        'issuedtimestamp',
        'storedtimestamp',
        'updatedtimestamp',
        'validfromtimestamp',
        'validtotimestamp',
        'classification',
        'locationdesignator',
        'notamaccountability',
        'notamtext',
        'aixmnotammessage',
        'status',
    ];

    protected function casts()
    {
        return [
            'issuedtimestamp'    => 'datetime:Y-m-d H:i:s',
            'storedtimestamp'    => 'datetime:Y-m-d H:i:s.v',
            'updatedtimestamp'   => 'datetime:Y-m-d H:i:s',
            'validfromtimestamp' => 'datetime:Y-m-d H:i:s',
            'validtotimestamp'   => 'datetime:Y-m-d H:i:s',
            'aixmnotammessage'   => FromFNSXmlCast::class,
            'classification'     => FNSNotamClassification::class,
            'status'             => FNSNotamStatus::class,
        ];
    }

    public function jsonSerialize(): mixed
    {
        $data = $this->toArray();
        $data['aixmnotammessage'] = $this->getRawOriginal('aixmnotammessage');

        return $data;
    }
}
