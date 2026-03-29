@extends('layouts.app')

{{-- Customize layout sections --}}
@section('subtitle', __('adminlte::org-structures.org_structure_details'))
@section('content_header_title', __('adminlte::org-structures.org_structures'))
@section('content_header_subtitle', __('adminlte::org-structures.org_structure_details'))

{{-- Content body: main page content --}}
@section('content_body')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Organizational Chart</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <h3>{{$org_structure->type}}</h3>
                    </div>
                </div>
                <div id="chart-container"></div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <livewire:org-structures.maintenance :org_structure="$org_structure" />
    </div>
</div>
@stop

{{-- Push extra CSS --}}
@push('css')
    <link rel="stylesheet" href="{{asset('/vendor/orgchart/src/css/jquery.orgchart.css')}}">
    <style>
        #chart-container {
            position: relative;
            height: 420px;
            border: 1px solid #aaa;
            margin: 0.5rem;
            overflow: auto;
            text-align: center;
        }
        #chart-container .title {
            width: 100% !important;
            min-width: 160px;
        }
        #chart-container .content {
            width: 100% !important;
            min-width: 160px;
        }
    </style>
@endpush

{{-- Push extra scripts --}}
@push('js')
    <script src="{{asset('/vendor/orgchart/src/js/jquery.orgchart.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        $(function() {
            var datasource = @php echo json_encode($chart_data); @endphp;

            $('#chart-container').orgchart({
                'data' : datasource,
                'depth': 2,
                'nodeTitle': 'name',
                'nodeContent': 'title',
                'exportButton': true,
                'exportFileExtension': 'pdf',
                'exportFilename': 'OrgChart-{{$org_structure->type}}',
                'pan': true,
                'zoom': true
            });
        });
    </script>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('refresh-org-chart', (event) => {
                const { new_data } = event;

                // Destroy the old chart
                $('#chart-container').empty();

                // Initialize the new chart with updated data
                $('#chart-container').orgchart({
                    'data': new_data,
                    'depth': 2,
                    'nodeTitle': 'name',
                    'nodeContent': 'title',
                    'exportButton': true,
                    'exportFileExtension': 'pdf',
                    'exportFilename': 'OrgChart-{{$org_structure->type}}',
                    'pan': true,
                    'zoom': true
                });
            });
        });
    </script>
@endpush