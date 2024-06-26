<?php

use App\Actions\FlightPlanParser;
use App\DTO\AtcFlightPlan;

function plan1(): string
{
    return <<<'EOL'
FF EUCHZMFP EUCBZMFP EIDWEINU
       EIDWEINU
AD EGGXZOZX BIRDZPZZ BIRDCPRX CZQXZQZX CZULZQZX CZQMZQZX CZQMZQZR
(FPL-EIN1LW-IS
-A332/H-SDE2E3FGHIJ4J5M1P2RWXYZ/LB1D1
-EIDW1310
-N0446F300 SUROX3J SUROX/N0446F300 DCT REVNU/N0442F300 DCT
 SUNOT/M075F300 DCT 59N020W 62N030W 64N040W 63N050W DCT
 MAXAR/M081F380 DCT LAKES/N0469F400 DCT TAFFY DCT PQI J55 HTO J174
 ORF J121 CHS DCT IGARY Q85 LPERD SNFLD3
-KMCO0926 KTPA
-PBN/A1B1D1L1S1 NAV/RNP2 DAT/1FANS2PDC CPDLCX SUR/RSP180 260B
 DOF/230329 REG/EIDUO EET/EGGX0026 SUNOT0055 59N020W0121 BIRD0150
 62N030W0207 BGGL0243 64N040W0249 63N050W0330 CZQX0347 MAXAR0405
 CZUL0427 CZQX0443 CZUL0454 CZQX0504 CZUL0515 CZQM0605 KZBW0613
 KZDC0724 KZJX0826 SEL/FLJR CODE/4CA5C6 OPR/EIN PER/C TALT/EGAA
 RVR/075 RMK/TCAS AER LINGUS OPERATIONS 0035318862147)
EOL;
}
function plan2(): string
{
    return <<<'EOL'
FF EUCHZMFP EUCBZMFP EIDWEINU
  EIDWEINU
  AD EGGXZOZX GMACZQZX GMMMZQZX GMMMZFZX
  (FPL-EIN78L-IS
  -A320/M-SDE2E3FGHIJ1RWXYZ/HB1
  -EIDW1330
  -N0445F360 PELIG6A PELIG DCT MAPAG/M078F360 DCT LASNO T9
  BEGAS/N0445F350 DCT DEMOS DCT ORSOS DCT BEXAL UN866 KONBA
  -GCLP0415 GCFV GCRR
  -PBN/A1B1D1L1S2 NAV/RNP2 DOF/221107 REG/EIDEH EET/EGGX0032
  LASNO0056 LECM0130 LPPC0159 GMMM0253 GCCC0342 SEL/AEJR CODE/4CA216
  OPR/EIN PER/C RVR/075 RMK/TCAS AER LINGUS OPERATIONS
  0035318862147)
EOL;
}

function plan3(): string
{
    return <<<'EOL'
FF KZDCZQZX EUCHZMFP EUCBZMFP EIDWEINU CZQMZQZX CZQMZQZR CZQXZQZX
  EGGXZOZX
  EIDWEINU
  (FPL-EIN118-IS
  -A21N/M-SDE2E3FGHIJ1J4J7M3P2RWXYZ/LB1D1
  -KIAD0055
  -N0451F330 JCOBY4 SWANN DCT BROSS Q419 RBV DCT LLUND DCT BAYYS DCT
  PUT DCT TUSKY N201B NICSO/M078F330 DCT 48N050W 51N040W 52N030W
  53N020W DCT MALOT/N0453F330 DCT GISTI DCT OSGAR OSGAR3X
  -EIDW0617 EGAA
  -PBN/A1B1D1L1S2 NAV/RNP2 DAT/1FANS2PDC SUR/RSP180 260B DOF/230510
  REG/EILRG EET/KZNY0024 KZBW0032 CZQM0117 CZQX0209 NICSO0239
  48N050W0249 51N040W0338 EGGX0425 53N020W0510 EISN0532 SEL/AGEJ
  CODE/4CABD3 OPR/EIN PER/C RALT/CYYT BIKF EINN RVR/075 RMK/TCAS AER
  LINGUS OPERATIONS 0035318862147)
EOL;
}

function plan4(): string
{
    return <<<'EOL'
FF KZDCZQZX EUCHZMFP EUCBZMFP EIDWEINU CZQMZQZX CZQMZQZR CZQXZQZX
  EGGXZOZX
  EIDWEINU
  (FPL-EIN118-IS
  -A21N/M-SDE2E3FGHIJ1J4J7M3P2RWXYZ/LB1D1
  -EGAA0055
  -N0451F330 JCOBY4 SWANN DCT BROSS Q419 RBV DCT LLUND DCT BAYYS DCT
  PUT DCT TUSKY N201B NICSO/M078F330 DCT 48N050W 51N040W 52N030W
  53N020W DCT MALOT/N0453F330 DCT GISTI DCT OSGAR OSGAR3X
  -EICK0617 EINN
  -PBN/A1B1D1L1S2 NAV/RNP2 DAT/1FANS2PDC SUR/RSP180 260B DOF/230510
  REG/EILRG EET/EGGX0425 53N020W0510 EISN0532 EGTT0615 SEL/AGEJ
  CODE/4CABD3 OPR/EIN PER/C RALT/EIKN EGCC RVR/075 RMK/TCAS AER
  LINGUS OPERATIONS 0035318862147)
EOL;
}

it('parses an ATC flightplan 1', function () {
    $result = FlightPlanParser::process(plan1());

    expect($result['departureAirport'])->toMatchArray(['EIDW']);
    expect($result['destinationAirport'])->toMatchArray(['KMCO']);
    expect($result['destinationAlternates'])->toMatchArray(['KTPA']);
    //    expect($result['firs'])->toMatchArray(['EGGX', 'BIRD', 'BGGL', 'CZQX', 'CZUL', 'CZQM', 'KZBW', 'KZDC', 'KZJX']);
    expect($result['enrouteAlternates'])->toMatchArray([]);
    expect($result['takeoffAlternate'])->toMatchArray(['EGAA']);
})
    ->skip();

it('parses an ATC flightplan 2', function () {
    $result = FlightPlanParser::process(plan2());

    expect($result['departureAirport'])->toMatchArray(['EIDW']);
    expect($result['destinationAirport'])->toMatchArray(['GCLP']);
    expect($result['destinationAlternates'])->toMatchArray(['GCFV']);
    //    expect($result['firs'])->toMatchArray(['EGGX', 'LECM', 'LPPC', 'GMMM', 'GCCC']);
    expect($result['enrouteAlternates'])->toMatchArray([]);
    expect($result['takeoffAlternate'])->toMatchArray([]);
})
    ->skip();

it('parses an ATC flightplan 3', function () {
    $result = FlightPlanParser::process(plan3());

    expect($result['departureAirport'])->toMatchArray(['KIAD']);
    expect($result['destinationAirport'])->toMatchArray(['EIDW']);
    expect($result['destinationAlternates'])->toMatchArray(['EGAA']);
    //    expect($result['firs'])->toMatchArray(['KZNY', 'KZBW', 'CZQM', 'CZQX', 'EGGX', 'EISN']);
    expect($result['enrouteAlternates'])->toMatchArray(['CYYT', 'BIKF', 'EINN']);
    expect($result['takeoffAlternate'])->toMatchArray([]);
})
    ->skip();

it('parses an ATC flightplan 4', function () {
    $result = FlightPlanParser::process(plan4());

    expect($result)->toBeInstanceOf(AtcFlightPlan::class);
    expect($result->departureAirport)->toMatchArray(['EGAA']);
    expect($result->destinationAirport)->toMatchArray(['EICK']);
    expect($result->destinationAlternate)->toMatchArray(['EINN']);
    expect($result->enrouteAlternates)->toMatchArray(['EIKN', 'EGCC']);
    expect($result->takeoffAlternate)->toMatchArray([]);
    expect($result->firs)->toMatchArray(['EGGX', 'EISN', 'EGTT']);
});

it('can be instantiated as a normal class', function () {
    $parser = new FlightPlanParser();

    $result = $parser->parse(plan4());
    expect($result)->toBeInstanceOf(AtcFlightPlan::class);
    expect($result->departureAirport)->toMatchArray(['EGAA']);
    expect($result->destinationAirport)->toMatchArray(['EICK']);
    expect($result->destinationAlternate)->toMatchArray(['EINN']);
    expect($result->enrouteAlternates)->toMatchArray(['EIKN', 'EGCC']);
    expect($result->takeoffAlternate)->toMatchArray([]);
    expect($result->firs)->toMatchArray(['EGGX', 'EISN', 'EGTT']);
});

it('throws an exception if the flight plan is in an invalid format', function () {
    $result = FlightPlanParser::process('Nonsense text');
})->throws(Exception::class, 'Sorry unable to extract all the required details from the flight plan supplied. Please try again.');
