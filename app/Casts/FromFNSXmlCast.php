<?php

namespace App\Casts;

use App\Actions\Parsers\FNSXmlParser;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * The notam received from FNS has an "aixmnotammessage" field that contains an xml string with
 * all the notam details.
 *
 * Laravel allows us to create a custom cast so that when we call this field from the model,
 * we can have the entire xml parsed into an object automatically making it super easy to work
 * with.
 */
class FromFNSXmlCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return FNSXmlParser::parse($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        //throw new Exception('This app must not try and write to the postgres database');
        return $value;
    }
}
