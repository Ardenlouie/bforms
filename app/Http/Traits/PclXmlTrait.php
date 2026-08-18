<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Models\AllForm;
use App\Models\EmployeeCostCenter;
use App\Models\CustomerCostCenter;
use App\Models\StockCostCenter;
use App\Models\PettyLiquid;
use App\Models\Product;
use App\Models\ExpenseAccount;

trait PclXmlTrait
{
    public function generateXmlPcl($pcl) {
        $parts = $this->convertDataPcl($pcl);

        foreach($parts as $key => $data) {
            $part = $key + 1;

            $xml = $this->arrayToXmlPcl($data);

            $filename = $pcl->control_number.'-'.$part.'.xml';
            $ftp = Storage::disk('DFM');

            $companyPath = match($pcl->company->name) {
                'BEVI'  => 'BEVI-Test',
                'BEVA'  => 'BEVA-Test',
                'BIG I' => 'BIGI-Test',
                default => 'Unknown-Company'
            };

            $ftp->put($companyPath . '/Incoming/PCL/' . $filename, $xml);
        }

        return count($parts) . " XML files created for " . $pcl->control_number;
    }

    public function downloadXmlPcl($pcl) 
    {
        $parts = $this->convertDataPcl($pcl);
        
        $data = is_array($parts) ? reset($parts) : $parts;

        $xmlContent = $this->arrayToXmlPcl($data);

        $filename = $pcl->control_number . '.xml';

        return response($xmlContent)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    private function arrayToXmlPcl($data) {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;  // Enable formatting and indentation

        $liquidCash = $dom->createElement('PostApInvoice');
        $liquidCash->setAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema-instance');
        $liquidCash->setAttribute('xsd:noNamespaceSchemaLocation', 'APSTINDOC.XSD');
        $dom->appendChild($liquidCash);

        $this->arrayToXmlHelperPcl($data, $dom, $liquidCash);

        return $dom->saveXML();
    }

    private function arrayToXmlHelperPcl($data, $dom, &$parent) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if ($this->is_numeric_array($value)) {
                    foreach ($value as $item) {
                        $subNode = $dom->createElement($key);
                        $parent->appendChild($subNode);
                        $this->arrayToXmlHelperPcl($item, $dom, $subNode);
                    }
                } else {
                    $subNode = $dom->createElement($key);
                    $parent->appendChild($subNode);
                    $this->arrayToXmlHelperPcl($value, $dom, $subNode);
                }
            } else {
                $cleanValue = htmlspecialchars($value ?? '');
                $child = $dom->createElement($key, $cleanValue);
                $parent->appendChild($child);
            }
        }
    }

    private function is_numeric_array_pcl($array) {
        return is_array($array) && count(array_filter(array_keys($array), 'is_numeric')) > 0;
    }

    private function convertDataPcl($pcl) {
        $pcl_code = $pcl->pca_form->cost_center;
        $pcl_company = $pcl->company_id;

        $customer_cost_center = CustomerCostCenter::where('gl_code', '130010')->where('company_id', $pcl_company)->where('customer', $pcl_code)->first();

        $pcl_details = $pcl->pcl_form_item;
        $pcl_number = $pcl->control_number;
        $activity_name = $pcl->activity_name;

        $tax_value = ($pcl->total_amount * 0.12);
        
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
                    'Invoice'                     => $pcl_number,
                    'TransactionValue'                     => $pcl->total_amount,
                    'JournalNotation'                     => $pcl->purpose,
                    'InvoiceDate'                     => $pcl->date_submitted,
                    'DueDate'                     => $pcl->pcl_date,
                    'Bank'                     => '02',
                    'TaxCode'                     => 'Z',
                    'TaxValue'                     => $tax_value,
                ]
            ]
        ];

        foreach ($pcl_details as $detail) {
            $entry_amount = $detail->amount;
            $expense_account = ExpenseAccount::where('name', $detail->item_description)->first();

            $data['Items']['Item']['LedgerDistribution'][] = [
                'LedgerCode'         => $expense_account->ledger_code,
                'LedgerTaxCode'         => 'Z',
                'LedgerWithholdingTaxCode'       => 'Z',
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
