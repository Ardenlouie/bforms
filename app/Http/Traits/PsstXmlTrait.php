<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Models\AllForm;

use App\Models\ProductTransfer;

trait PsstXmlTrait
{
    public function generateXml($psst) {
        $parts = $this->convertData($psst);
        foreach($parts as $key => $data) {
            $part = $key + 1;
            $xml = $this->arrayToXml($data);

            $filename = $psst->control_number.'-'.$part.'.xml';

            $ftp = Storage::disk('DFM');

            switch($psst->company->name) {
                case 'BEVI':
                    $ftp->put('BEVI-Test/Incoming/PSST/'.$filename, $xml);
                    break;
                case 'BEVA':
                    $ftp->put('BEVA-Test/Incoming/PSST/'.$filename, $xml);
                    break;
                case 'BIG I':
                    $ftp->put('BIGI-Test/Incoming/PSST/'.$filename, $xml);
                    break;
            }
        }

        return $psst->control_number.'.xml file created successfully.';
    }

    public function downloadXml($psst) 
    {
        $parts = $this->convertData($psst);
        
        $data = is_array($parts) ? reset($parts) : $parts;

        $xmlContent = $this->arrayToXml($data);

        $filename = $psst->control_number . '.xml';

        return response($xmlContent)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    private function arrayToXml($data) {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;  // Enable formatting and indentation

        $productTransfers = $dom->createElement('PostSalesOrdersSCT');
        $productTransfers->setAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema-instance');
        $productTransfers->setAttribute('xsd:noNamespaceSchemaLocation', 'SORTTRDOC.XSD');
        $dom->appendChild($productTransfers);

        $this->arrayToXmlHelper($data, $dom, $productTransfers);

        return $dom->saveXML();
    }

    private function arrayToXmlHelper($data, $dom, &$parent) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if ($key === 'StockLine') {
                    // Special handling for repeated 'StockLine' key within 'OrderDetails'
                    foreach ($value as $item) {
                        $subNode = $dom->createElement($key);
                        $parent->appendChild($subNode);
                        $this->arrayToXmlHelper($item, $dom, $subNode);
                    }
                } else {
                    $subNode = $dom->createElement($key);
                    $parent->appendChild($subNode);
                    $this->arrayToXmlHelper($value, $dom, $subNode);
                }
            } else {
                $child = $dom->createElement("$key", htmlspecialchars("$value"));
                $parent->appendChild($child);
            }
        }
    }

    private function convertData($psst) {

        $company = $psst->company->name;

        $psst_details = $psst->psst_form_item;

        $data = [
            'Orders' => [
                'OrderHeader' => [
                    'CustomerPoNumber'              => $psst->control_number,
                    'SourceWarehouse'                     => $psst->point_origin,
                    'TargetWarehouse'                     => 'OFFICE WHS',
                    'OrderDate'                     => $psst->delivery_date,
                    'ShipDate'                     => $psst->delivery_date,
                    'ShippingInstrs'                     => $psst->delivery_instructions,

                ],
            ]
        ];

        foreach($psst_details as $detail) {
            $data['Orders']['OrderDetails']['StockLine'][] = [

                'StockCode'         => $detail->item_code,
                'OrderQty'          => $detail->quantity,
                'OrderUom'          => $detail->uom,
            ];
            
        }

        $psst_parts[] = $data;
        

        return $psst_parts;
    }
}
