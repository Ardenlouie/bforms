<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Models\AllForm;
use App\Models\EmployeeCostCenter;
use App\Models\CustomerCostCenter;
use App\Models\StockCostCenter;
use App\Models\ProductSample;
use App\Models\Product;

trait PsrfXmlTrait
{
    public function generateXmlPsrf($psrf) {
        $parts = $this->convertDataPsrf($psrf);

        foreach($parts as $key => $data) {
            $part = $key + 1;

            $xml = $this->arrayToXmlPsrf($data);

            $filename = $psrf->control_number.'-'.$part.'.xml';
            $ftp = Storage::disk('DFM');

            $companyPath = match($psrf->company->name) {
                'BEVI'  => 'BEVI-Test',
                'BEVA'  => 'BEVA-Test',
                'BIG I' => 'BIGI-Test',
                default => 'Unknown-Company'
            };

            $ftp->put($companyPath . '/Incoming/PSRF/' . $filename, $xml);
        }

        return count($parts) . " XML files created for " . $psrf->control_number;
    }

    public function downloadXmlPsrf($psrf) 
    {
        $parts = $this->convertDataPsrf($psrf);
        
        $data = is_array($parts) ? reset($parts) : $parts;

        $xmlContent = $this->arrayToXmlPsrf($data);

        $filename = $psrf->control_number . '.xml';

        return response($xmlContent)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    private function arrayToXmlPsrf($data) {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;  // Enable formatting and indentation

        $productSamples = $dom->createElement('PostInvExpenseIssues');
        $productSamples->setAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema-instance');
        $productSamples->setAttribute('xsd:noNamespaceSchemaLocation', 'INVTMEDOC.XSD');
        $dom->appendChild($productSamples);

        $this->arrayToXmlHelperPsrf($data, $dom, $productSamples);

        return $dom->saveXML();
    }

    private function arrayToXmlHelperPsrf($data, $dom, &$parent) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if ($this->is_numeric_array($value)) {
                    foreach ($value as $item) {
                        $subNode = $dom->createElement($key);
                        $parent->appendChild($subNode);
                        $this->arrayToXmlHelperPsrf($item, $dom, $subNode);
                    }
                } else {
                    $subNode = $dom->createElement($key);
                    $parent->appendChild($subNode);
                    $this->arrayToXmlHelperPsrf($value, $dom, $subNode);
                }
            } else {
                $cleanValue = htmlspecialchars($value ?? '');
                $child = $dom->createElement($key, $cleanValue);
                $parent->appendChild($child);
            }
        }
    }

    private function is_numeric_array($array) {
        return is_array($array) && count(array_filter(array_keys($array), 'is_numeric')) > 0;
    }

    private function convertDataPsrf($psrf) {

        $employee_cost_center = EmployeeCostCenter::where('employee_code', $psrf->requested_by)->first();

        $psrf_details = $psrf->psrf_form_item;
        $psrf_company = $psrf->company_id;
        $psrf_number = $psrf->control_number;
        $activity_name = $psrf->activity_name;
        $analysis1 = $employee_cost_center->department_code;
        $analysis2 = $employee_cost_center->employee_code;
        $analysis4 = $psrf->customer ?? 'ACC';

        $itemsList = [];

        foreach ($psrf_details as $detail) {
            $sku_code = $detail->item_code;
            $quantity = number_format($detail->quantity, 0);
            $stock_cost_center = StockCostCenter::where('gl_code', '600160')->where('company_id', $psrf_company)->where('stock_code', $sku_code)->first();
            $analysis3 = $stock_cost_center->brand_code;
            $uom_convert = Product::where('stock_code', $sku_code)->first();

            $ps_api = Http::withToken('UaHxtws9LHZ47QG21lBXjQgka3Fe93H5xV1Y6HBQDN4=')
                ->get('http://192.168.11.240/refreshable/public/api/productSample/'.$sku_code.'/'.$quantity);

            $ps_bevi_collect = $ps_api->json();


            foreach($ps_bevi_collect as $key => $product_sample){
                $entry_amount = $product_sample['UnitCost'] * $product_sample['Allocated'];

                $itemsList[] = [
                    'Warehouse'         => $product_sample['Warehouse'],
                    'StockCode'         => $sku_code,
                    'Quantity'          => ($detail->uom == 'CS') ? $product_sample['Allocated'] * $uom_convert->order_uom_conversion : $product_sample['Allocated'],
                    'UnitOfMeasure'     => $product_sample['StockUom'],
                    'BinLocation'       => $product_sample['Bin'],
                    'Lot'               => $product_sample['Lot'],
                    'Reference'         => $psrf_number,
                    'Notation'          => $activity_name,
                    'LedgerCode'        => '600160',    
                    'AnalysisEntry'     => '',
                    'AnalysisLineEntry' => [
                        'AnalysisCode1' => $analysis1,
                        'AnalysisCode2' => $analysis2,
                        'AnalysisCode3' => $analysis3,
                        'AnalysisCode4' => $analysis4,
                        'AnalysisCode5' => $sku_code,
                        'StartDate'     => '',
                        'EndDate'       => '',
                        'EntryAmount'   => number_format($entry_amount, 2),
                    ],
                ];
            }
            
        }

        $data = [
            'Items' => [
                'Item' => $itemsList
            ]
        ];
            // dd($data);

        return $data; 

   
    }
}
