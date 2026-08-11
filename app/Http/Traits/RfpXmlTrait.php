<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Models\AllForm;
use App\Models\EmployeeCostCenter;
use App\Models\CustomerCostCenter;
use App\Models\StockCostCenter;
use App\Models\RequestPayment;
use App\Models\Product;

trait RfpXmlTrait
{
    public function generateXmlRfp($rfp) {
        $parts = $this->convertDataRfp($rfp);

        foreach($parts as $key => $data) {
            $part = $key + 1;

            $xml = $this->arrayToXmlRfp($data);

            $filename = $rfp->control_number.'-'.$part.'.xml';
            $ftp = Storage::disk('DFM');

            $companyPath = match($rfp->company->name) {
                'BEVI'  => 'BEVI-Test',
                'BEVA'  => 'BEVA-Test',
                'BIG I' => 'BIGI-Test',
                default => 'Unknown-Company'
            };

            $ftp->put($companyPath . '/Incoming/RFP/' . $filename, $xml);
        }

        return count($parts) . " XML files created for " . $rfp->control_number;
    }

    public function downloadXmlRfp($rfp) 
    {
        $parts = $this->convertDataRfp($rfp);
        
        $data = is_array($parts) ? reset($parts) : $parts;

        $xmlContent = $this->arrayToXmlRfp($data);

        $filename = $rfp->control_number . '.xml';

        return response($xmlContent)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    private function arrayToXmlRfp($data) {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;  // Enable formatting and indentation

        $requestPayment = $dom->createElement('PostApInvoice');
        $requestPayment->setAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema-instance');
        $requestPayment->setAttribute('xsd:noNamespaceSchemaLocation', 'APSTINDOC.XSD');
        $dom->appendChild($requestPayment);

        $this->arrayToXmlHelperRfp($data, $dom, $requestPayment);

        return $dom->saveXML();
    }

    private function arrayToXmlHelperRfp($data, $dom, &$parent) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if ($this->is_numeric_array($value)) {
                    foreach ($value as $item) {
                        $subNode = $dom->createElement($key);
                        $parent->appendChild($subNode);
                        $this->arrayToXmlHelperRfp($item, $dom, $subNode);
                    }
                } else {
                    $subNode = $dom->createElement($key);
                    $parent->appendChild($subNode);
                    $this->arrayToXmlHelperRfp($value, $dom, $subNode);
                }
            } else {
                $cleanValue = htmlspecialchars($value ?? '');
                $child = $dom->createElement($key, $cleanValue);
                $parent->appendChild($child);
            }
        }
    }

    private function is_numeric_array_rfp($array) {
        return is_array($array) && count(array_filter(array_keys($array), 'is_numeric')) > 0;
    }

    private function convertDataRfp($rfp) {

        $rfp_code = $rfp->cost_center;
        $rfp_company = $rfp->company_id;

        $customer_cost_center = CustomerCostCenter::where('gl_code', '130010')->where('company_id', $rfp_company)->where('customer', $rfp_code)->first();

        $rfp_details = $rfp->rfp_form_item;
        $rfp_number = $rfp->control_number;
        $activity_name = $rfp->activity_name;

        $tax_value = ($rfp->total_amount * 0.12);
        
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
                    'Invoice'                     => $rfp_number,
                    'TransactionValue'                     => $rfp->amount,
                    'JournalNotation'                     => $rfp->purpose,
                    'InvoiceDate'                     => $rfp->date_submitted,
                    'DueDate'                     => $rfp->rca_date,
                    'TaxCode'                     => 'G',
                    'TaxValue'                     => $tax_value,
                ]
            ]
        ];

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
                'EntryAmount'   => number_format($rfp->amount, 2),
            ],
        ];
        
            // dd($data);

        return $data; 

   
    }
}
