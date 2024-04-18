<?php

namespace App\Actions\Parsers;

use App\DTO\FnsXmlNotam;
use Throwable;

class FNSXmlParser
{
    public static function parse($xml): FnsXmlNotam
    {
        try {
            $root = simplexml_load_string($xml);
            $namespaces = collect($root->getNamespaces(true));

            foreach ($namespaces as $prefix => $url) {
                $root->registerXPathNamespace($prefix, $url);
            }

            //We only need some of the namespaces, but each xml string from the FNS
            //has different prefixes for each url, so let's normalise them first and
            //then use those for extracting the data.
            $ns1 = $namespaces->search('http://www.aixm.aero/schema/5.1/event');
            $ns2 = $namespaces->search('http://www.aixm.aero/schema/5.1');
            $ns3 = $namespaces->search('http://www.aixm.aero/schema/5.1/extensions/FAA/FNSE');
            $ns4 = $namespaces->search('http://www.opengis.net/gml/3.2');
            $ns5 = $namespaces->search('http://www.w3.org/1999/xhtml');

            $data = [
                'beginPosition'   => self::xpathResult($root, "//$ns4:TimePeriod/$ns4:beginPosition"),
                'endPosition'     => self::xpathResult($root, "//$ns4:TimePeriod/$ns4:endPosition"),
                'interpretation'  => self::xpathResult($root, "//$ns2:interpretation"),
                'scenario'        => self::xpathResult($root, "//$ns1:EventTimeSlice/$ns1:scenario"),
                'series'          => self::xpathResult($root, "//$ns1:NOTAM/$ns1:series"),
                'number'          => self::xpathResult($root, "//$ns1:NOTAM/$ns1:number"),
                'year'            => self::xpathResult($root, "//$ns1:NOTAM/$ns1:year"),
                'type'            => self::xpathResult($root, "//$ns1:NOTAM/$ns1:type"),
                'issued'          => self::xpathResult($root, "//$ns1:NOTAM/$ns1:issued"),
                'affectedFIR'     => self::xpathResult($root, "//$ns1:NOTAM/$ns1:affectedFIR"),
                'selectionCode'   => self::xpathResult($root, "//$ns1:NOTAM/$ns1:selectionCode"),
                'traffic'         => self::xpathResult($root, "//$ns1:NOTAM/$ns1:traffic"),
                'purpose'         => self::xpathResult($root, "//$ns1:NOTAM/$ns1:purpose"),
                'scope'           => self::xpathResult($root, "//$ns1:NOTAM/$ns1:scope"),
                'minimumFL'       => self::xpathResult($root, "//$ns1:NOTAM/$ns1:minimumFL"),
                'maximumFL'       => self::xpathResult($root, "//$ns1:NOTAM/$ns1:maximumFL"),
                'coordinates'     => self::xpathResult($root, "//$ns1:NOTAM/$ns1:coordinates"),
                'radius'          => self::xpathResult($root, "//$ns1:NOTAM/$ns1:radius"),
                'location'        => self::xpathResult($root, "//$ns1:NOTAM/$ns1:location"),
                'effectiveStart'  => self::xpathResult($root, "//$ns1:NOTAM/$ns1:effectiveStart"),
                'effectiveEnd'    => self::xpathResult($root, "//$ns1:NOTAM/$ns1:effectiveEnd"),
                'text'            => self::xpathResult($root, "//$ns1:NOTAM/$ns1:text"),
                'translationType' => self::xpathResult($root, "//$ns1:NOTAMTranslation/$ns1:type"),
                'simpleText'      => self::xpathResult($root, "//$ns1:NOTAMTranslation/$ns1:simpleText"),
                'formattedText'   => $ns5 ? self::xpathResult($root, "//$ns1:NOTAMTranslation/$ns1:formattedText/$ns5:div") : null,
                'classification'  => self::xpathResult($root, "//$ns3:EventExtension/$ns3:classification"),
                'accountId'       => self::xpathResult($root, "//$ns3:EventExtension/$ns3:accountId"),
                'airportname'     => self::xpathResult($root, "//$ns3:EventExtension/$ns3:airportname"),
                'originID'        => self::xpathResult($root, "//$ns3:EventExtension/$ns3:originID"),
                'lastUpdated'     => self::xpathResult($root, "//$ns3:EventExtension/$ns3:lastUpdated"),
                'icaoLocation'    => self::xpathResult($root, "//$ns3:EventExtension/$ns3:icaoLocation"),
            ];

            return FnsXmlNotam::from($data);
        } catch (Throwable $exception) {
            report($exception);

            return new FnsXmlNotam();
        }
    }

    private static function xpathResult($root, string $path)
    {
        $result = $root->xpath($path);

        return ! empty($result) ? $result[0] : null;
    }
}
