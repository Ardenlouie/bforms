<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Models\AllForm;
use App\Models\EmployeeCostCenter;
use App\Models\CustomerCostCenter;
use App\Models\StockCostCenter;
use App\Models\PettyCash;
use App\Models\Product;

trait PcaXmlTrait
{
    public function generateXmlPca($pca) {
        $parts = $this->convertDataRca($pca);

        foreach($parts as $key => $data) {
            $part = $key + 1;

            $xml = $this->arrayToXmlPca($data);

            $filename = $rca->control_number.'-'.$part.'.xml';
            $ftp = Storage::disk('DFM');

            $companyPath = match($rca->company->name) {
                'BEVI'  => 'BEVI-Test',
                'BEVA'  => 'BEVA-Test',
                'BIG I' => 'BIGI-Test',
                default => 'Unknown-Company'
            };

            $ftp->put($companyPath . '/Incoming/PCA/' . $filename, $xml);
        }

        return count($parts) . " XML files created for " . $pca->control_number;
    }

    public function downloadXmlPca($pca) 
    {
        $parts = $this->convertDataPca($pca);
        
        $data = is_array($parts) ? reset($parts) : $parts;

        $xmlContent = $this->arrayToXmlPca($data);

        $filename = $pca->control_number . '.xml';

        return response($xmlContent)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    private function arrayToXmlPca($data) {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;  // Enable formatting and indentation

        $pettyCash = $dom->createElement('PostApInvoice');
        $pettyCash->setAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema-instance');
        $pettyCash->setAttribute('xsd:noNamespaceSchemaLocation', 'APSTINDOC.XSD');
        $dom->appendChild($pettyCash);

        $this->arrayToXmlHelperPca($data, $dom, $pettyCash);

        return $dom->saveXML();
    }

    private function arrayToXmlHelperPca($data, $dom, &$parent) {
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

    private function is_numeric_array_pca($array) {
        return is_array($array) && count(array_filter(array_keys($array), 'is_numeric')) > 0;
    }

    private function convertDataPca($pca) {
        $pca_code = $pca->cost_center;
        $pca_company = $pca->company_id;

        $customer_cost_center = CustomerCostCenter::where('gl_code', '130010')->where('company_id', $pca_company)->where('customer', $pca_code)->first();

        $pca_details = $pca->pca_form_item;
        $pca_number = $pca->control_number;
        $activity_name = $pca->activity_name;

        $tax_value = ($pca->total_amount * 0.12);
        
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
                    'Invoice'                     => $pca_number,
                    'TransactionValue'                     => $pca->total_amount,
                    'JournalNotation'                     => '',
                    'InvoiceDate'                     => $pca->date_submitted,
                    'DueDate'                     => $pca->date_submitted,
                    'TaxCode'                     => 'G',
                    'TaxValue'                     => $tax_value,
                ]
            ]
        ];

        foreach ($pca_details as $detail) {
            $entry_amount = $detail->amount;

            $data['Items']['Item']['LedgerDistribution'][] = [
                'LedgerCode'         => '130010',
                'LedgerTaxCode'         => 'G',
                'LedgerWithholdingTaxCode'       => 'G',
                'AnalysisLineEntry' => [
                    'AnalysisCode1' => '',
                    'AnalysisCode2' => '',
                    'AnalysisCode3' => '',
                    'AnalysisCode4' => $analysis4,
                    'AnalysisCode5' => '',
                    'EntryAmount'   => number_format($entry_amount, 2),
                ],
            ];
           
            
        }

        
        // dd($data);

        return $data; 

   
    }
}
