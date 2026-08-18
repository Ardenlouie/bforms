<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Models\AllForm;
use App\Models\EmployeeCostCenter;
use App\Models\CustomerCostCenter;
use App\Models\StockCostCenter;
use App\Models\RequestCash;
use App\Models\Product;

trait RcaXmlTrait
{
    public function generateXmlRca($rca) {
        $parts = $this->convertDataRca($rca);

        foreach($parts as $key => $data) {
            $part = $key + 1;

            $xml = $this->arrayToXmlRca($data);

            $filename = $rca->control_number.'-'.$part.'.xml';
            $ftp = Storage::disk('DFM');

            $companyPath = match($rca->company->name) {
                'BEVI'  => 'BEVI-Test',
                'BEVA'  => 'BEVA-Test',
                'BIG I' => 'BIGI-Test',
                default => 'Unknown-Company'
            };

            $ftp->put($companyPath . '/Incoming/RCA/' . $filename, $xml);
        }

        return count($parts) . " XML files created for " . $rca->control_number;
    }

    public function downloadXmlRca($rca) 
    {
        $parts = $this->convertDataRca($rca);
        
        $data = is_array($parts) ? reset($parts) : $parts;

        $xmlContent = $this->arrayToXmlRca($data);

        $filename = $rca->control_number . '.xml';

        return response($xmlContent)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    private function arrayToXmlRca($data) {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;  // Enable formatting and indentation

        $requestCash = $dom->createElement('PostApInvoice');
        $requestCash->setAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema-instance');
        $requestCash->setAttribute('xsd:noNamespaceSchemaLocation', 'APSTINDOC.XSD');
        $dom->appendChild($requestCash);

        $this->arrayToXmlHelperRca($data, $dom, $requestCash);

        return $dom->saveXML();
    }

    private function arrayToXmlHelperRca($data, $dom, &$parent) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if ($this->is_numeric_array($value)) {
                    foreach ($value as $item) {
                        $subNode = $dom->createElement($key);
                        $parent->appendChild($subNode);
                        $this->arrayToXmlHelperRca($item, $dom, $subNode);
                    }
                } else {
                    $subNode = $dom->createElement($key);
                    $parent->appendChild($subNode);
                    $this->arrayToXmlHelperRca($value, $dom, $subNode);
                }
            } else {
                $cleanValue = htmlspecialchars($value ?? '');
                $child = $dom->createElement($key, $cleanValue);
                $parent->appendChild($child);
            }
        }
    }

    private function is_numeric_array_rca($array) {
        return is_array($array) && count(array_filter(array_keys($array), 'is_numeric')) > 0;
    }

    private function convertDataRca($rca) {
        $rca_code = $rca->cost_center;
        $rca_company = $rca->company_id;

        $customer_cost_center = CustomerCostCenter::where('gl_code', '130010')->where('company_id', $rca_company)->where('customer', $rca_code)->first();

        $rca_details = $rca->rca_form_item;
        $rca_number = $rca->control_number;
        $activity_name = $rca->activity_name;

        $tax_value = ($rca->total_amount * 0.12);
        
        $analysis1 = $customer_cost_center->customer;
        $analysis2 = $customer_cost_center->customer;
        $analysis3 = $customer_cost_center->customer;
        $analysis4 = $customer_cost_center->customer;
        $analysis5 = 'ACC';

        $data = [
            'Items' => [
                'Item' => [
                    'Supplier'              => $customer_cost_center->customer,
                    'TransactionCode'                     => 'I',
                    'Invoice'                     => $rca_number,
                    'TransactionValue'                     => $rca->total_amount,
                    'JournalNotation'                     => $rca->purpose,
                    'InvoiceDate'                     => $rca->date_submitted,
                    'DueDate'                     => $rca->rca_date,
                    'Bank'                     => '02',
                    'TaxCode'                     => 'Z',
                    'TaxValue'                     => $tax_value,
                ]
            ]
        ];

 
        $data['Items']['Item']['LedgerDistribution'][] = [
            'LedgerCode'         => '130010',
            'LedgerTaxCode'         => 'Z',
            'LedgerWithholdingTaxCode'       => 'Z',
            'AnalysisLineEntry' => [
                'AnalysisCode1' => '',
                'AnalysisCode2' => '',
                'AnalysisCode3' => '',
                'AnalysisCode4' => $analysis4,
                'AnalysisCode5' => '',
                'EntryAmount'   => number_format($rca->total_amount, 2),
            ],
        ];
           
            
        

        

        // dd($data);

        return $data; 

   
    }
}
