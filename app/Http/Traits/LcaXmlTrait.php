<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Models\AllForm;
use App\Models\EmployeeCostCenter;
use App\Models\CustomerCostCenter;
use App\Models\StockCostCenter;
use App\Models\LiquidCash;
use App\Models\Product;
use App\Models\ExpenseAccount;

trait LcaXmlTrait
{
    public function generateXmlLca($lca) {
        $parts = $this->convertDataLca($lca);

        foreach($parts as $key => $data) {
            $part = $key + 1;

            $xml = $this->arrayToXmlLca($data);

            $filename = $lca->control_number.'-'.$part.'.xml';
            $ftp = Storage::disk('DFM');

            $companyPath = match($lca->company->name) {
                'BEVI'  => 'BEVI-Test',
                'BEVA'  => 'BEVA-Test',
                'BIG I' => 'BIGI-Test',
                default => 'Unknown-Company'
            };

            $ftp->put($companyPath . '/Incoming/LCA/' . $filename, $xml);
        }

        return count($parts) . " XML files created for " . $lca->control_number;
    }

    public function downloadXmlLca($lca) 
    {
        $parts = $this->convertDataLca($lca);
        
        $data = is_array($parts) ? reset($parts) : $parts;

        $xmlContent = $this->arrayToXmlLca($data);

        $filename = $lca->control_number . '.xml';

        return response($xmlContent)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    private function arrayToXmlLca($data) {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;  // Enable formatting and indentation

        $liquidCash = $dom->createElement('PostApInvoice');
        $liquidCash->setAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema-instance');
        $liquidCash->setAttribute('xsd:noNamespaceSchemaLocation', 'APSTINDOC.XSD');
        $dom->appendChild($liquidCash);

        $this->arrayToXmlHelperLca($data, $dom, $liquidCash);

        return $dom->saveXML();
    }

    private function arrayToXmlHelperLca($data, $dom, &$parent) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if ($this->is_numeric_array($value)) {
                    foreach ($value as $item) {
                        $subNode = $dom->createElement($key);
                        $parent->appendChild($subNode);
                        $this->arrayToXmlHelperLca($item, $dom, $subNode);
                    }
                } else {
                    $subNode = $dom->createElement($key);
                    $parent->appendChild($subNode);
                    $this->arrayToXmlHelperLca($value, $dom, $subNode);
                }
            } else {
                $cleanValue = htmlspecialchars($value ?? '');
                $child = $dom->createElement($key, $cleanValue);
                $parent->appendChild($child);
            }
        }
    }

    private function is_numeric_array_lca($array) {
        return is_array($array) && count(array_filter(array_keys($array), 'is_numeric')) > 0;
    }

    private function convertDataLca($lca) {
        $lca_code = $lca->rca_form->cost_center;
        $lca_company = $lca->company_id;

        $customer_cost_center = CustomerCostCenter::where('gl_code', '130010')->where('company_id', $lca_company)->where('customer', $lca_code)->first();

        $lca_details = $lca->lca_form_item;
        $lca_number = $lca->control_number;
        $activity_name = $lca->activity_name;

        $tax_value = ($lca->total_amount * 0.12);
        
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
                    'Invoice'                     => $lca_number,
                    'TransactionValue'                     => $lca->total_amount,
                    'JournalNotation'                     => $lca->purpose,
                    'InvoiceDate'                     => $lca->date_submitted,
                    'DueDate'                     => $lca->lca_date,
                    'Bank'                     => '02',
                    'TaxCode'                     => 'Z',
                    'TaxValue'                     => $tax_value,
                ]
            ]
        ];

        foreach ($lca_details as $detail) {
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
