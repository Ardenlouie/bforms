<?php

use Livewire\Component;
use App\Models\Form;
use App\Models\AllForm;
use App\Models\ProductSample;
use App\Models\ProductSampleItem;
use App\Models\ProductTransfer;
use App\Models\ProductTransferItem;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

new class extends Component
{
    public $wh_detail_collect = []; 
    public $point_origin;
    public $selectedStockCode;
    public $selectedItem = null;


    public function selectPoint($value) 
    {
        $this->point_origin = $value;

        $response = Http::withToken('UaHxtws9LHZ47QG21lBXjQgka3Fe93H5xV1Y6HBQDN4=')
            ->get("http://192.168.11.240/refreshable/public/api/lotDetail/{$value}");


        $this->wh_detail_collect = collect($response->json());

        Session::put('point_origin', [
            'point_origin' => $value,
        ]);
    }
};
?>

<div>
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="form-group">
            <label class="mb-0">Point of Origin</label>
                <select class="form-control" 
                        wire:change="selectPoint($event.target.value)" >
                    <option value="">-- Select Warehouse --</option>
                    <option value="VIR">BEVMI Warehouse (VIR)</option>
                    <option value="CAL">Maersk Calamba Warehouse (CAL)</option>
                    <option value="CAL-SDI">Maersk Calamba Warehouse - SDI (CAL-SDI)</option>
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div wire:loading wire:target="selectPoint" class="text-primary small mb-2">
                <i class="fas fa-spinner fa-spin"></i> Fetching API data...
            </div>
            @if($wh_detail_collect)
            <div class="card card-outline card-info shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">Warehouse Information</h3>
                </div>
                <div class="card-body p-0" style="max-height: 350px; overflow: auto;">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Stock Code</th>
                                <th>Warehouse</th>
                                <th>Stock on Hand</th>

                            <tr>
                        </thead>
                        <tbody >
                        @foreach($wh_detail_collect as $item)
                            <tr>
                                <td>{{ $item['StockCode'] }}</td>
                                <td>{{ $item['Warehouse'] }}</td>
                                <td>
                                    <span class="badge badge-success px-2 py-1">
                                        {{ number_format($item['TotalQty'], 2) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody >
                    </table>
                </div>
            </div>
            @else
                <div class="alert alert-light border text-center text-muted" style="border-style: dashed !important;">
                    <i class="fas fa-info-circle mr-1"></i> 
                    Select a warehouse to view the available stock code and quantity details.
                </div>
            @endif
        </div>
    </div>
</div>
