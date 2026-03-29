<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Product Sample Request Form (PSRF)</title>
    <link rel="shortcut icon" href="{{ public_path('/images/logonobg.png')}}" />

<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;

        color: #000;
    }

    .header {
        text-align: center;
        border-bottom: 3px solid #ffffff;

        margin-bottom: 20px;
        background-color: #ffffff;
    }

    .header img {
        height: 45px;
        float: left;
    }

    .header h1 {
        font-size: 24px;
        text-transform: uppercase;
        margin: 0;
        line-height: 45px;
        color: black;
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .info-table td {
        padding: 5px 8px;
        vertical-align: top;
        font-size: 14px;
    }

    .info-table .label {
        font-weight: bold;
        text-transform: uppercase;
        width: 25%;
        white-space: nowrap;
    }

    table.data {
        width: 100%;
        border-collapse: collapse;
    }

    table.data th, table.data td {
        border: 1px solid #000;
        padding: 6px;
        text-align: center;
        font-size: 14px;
    }

    table.data th {
        background-color: #f1f1f1;
        text-transform: uppercase;
        font-weight: bold;
    }

    .total-row td {
        font-weight: bold;
        background-color: #f9f9f9;
    }
    

    .footer-buttons {
        text-align: right;
        margin-top: 20px;
    }

    .btn {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 4px;
        font-size: 11px;
        text-transform: uppercase;
        margin-left: 6px;
    }

    .btn-draft {
        border: 1px solid #999;
        background-color: #ddd;
    }

    .btn-review {
        border: none;
        background-color: #007bff;
        color: #fff;
    }

    .container {
        border: 2px solid black;
        margin: 0 auto;
        text-align: center;
    }

    .headers {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 30px;
    }

    .acknowledgment {
        font-size: 14px;
        margin-bottom: 60px;
        color: #333;
    }

    /* The Table Layout for Alignment */
    .signature-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed; /* Forces equal column widths */
        margin-top: 40px;
    }

    .signature-table td {
        text-align: center;
        vertical-align: bottom;
    }

    /* Space between the two columns */
    .spacer {
        width: 10%;
    }

    .sign-box {
        width: 40%;
        text-align: center;
    }

    .line {
        border-top: 1px solid black;
        margin-bottom: 5px;
    }

    .labels {
        font-size: 14px;
        font-weight: normal;
    }

    .recipient-name {
        font-size: 14px;
        margin-bottom: 2px;
        min-height: 18px; /* Keeps spacing if empty */
    }

    @page {
        /* Create 1.5 inch space at the bottom for signatures */
        margin: 20px 30px 120px 30px; 
    }

    .signature-wrapper {
        position: fixed;
        bottom: 50px; /* Adjusted to sit within the margin */
        left: 0;
        right: 0;
        width: 100%;
    }

    .sig-column {
        width: 30%;
        float: left;
        margin: 0 1.5%;
        text-align: center;
    }

    .sig-image {
        display: block;
        margin: 0 auto;
        height: 60px; /* Force a consistent height for signatures */
        width: auto;
    }

    .sig-name {
        margin-top: 5px;
        padding-top: 5px;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 14px;
    }

    .sig-line {
        border-top: 1px solid #000;
        margin-top: 5px;
        padding-top: 5px;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 11px;
    }

</style>
</head>
<body>

<main>
    <table class="info-table">
        <tr>
            <td class="label">
                @if($forms->model->company->id== 1)
                <img src="{{ public_path('/images/bevilogonobg.png')}}" alt="product photo" class="product-img" height="50" width="250">
                @elseif($forms->model->company->id == 2)
                <img src="{{ public_path('/images/bevanobg.png')}}" alt="product photo" class="product-img" height="80" width="120">
                @elseif($forms->model->company->id == 3)
                <img src="{{ public_path('/images/biginobg.png')}}" alt="product photo" class="product-img" height="80" width="150">
                @endif
            </td>
            <td></td>
            <td></td>
            <td>Ref. No.: <b class="label"> {{ $forms->model->control_number }}</b></td>
            <td></td>
        </tr>
    </table>
    <div class="header">
        <h1>Product Sample Request Form</h1>
    </div>

    <!-- Info Section -->
    <table class="info-table">
        <tr>
            <td class="label">Recipient:</td>
            <td>{{ $forms->model->recipient }}</td>
            <td class="label">Date Submitted:</td>
            <td>{{ $forms->model->date_submitted }}</td>
        </tr>
        <tr>
            <td class="label">Activity Name:</td>
            <td>{{ $forms->model->recipient }}</td>
        </tr>
        <tr>
            <td class="label">Program Date:</td>
            <td>{{ $forms->model->program_date }}</td>
        <tr>
        <tr>
            <td class="label">Objective:</td>
            <td>{{ $forms->model->objective }}</td>
        <tr>
        <tr>
            <td class="label">Special Instructions:</td>
            <td>{{ $forms->model->special_instructions }}</td>
        <tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>No.</th>
                <th style="width: 70px;">Item Code</th>
                <th >Item Description</th>
                <th>UOM</th>
                <th>QTY</th>
                <th style="width: 50px;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($forms->model->psrf_form_item()->get() as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item['item_code'] ?? '' }}</td>
                <td>{{ $item['item_description'] ?? '' }}</td>
                <td>{{ $item['uom'] ?? '' }}</td>
                <td>{{ number_format($item['quantity'] ?? 0, 0) }}</td>
                <td>{{ $item['remarks'] ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p></p>

    <!-- <table class="data">
        <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Signature</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody >
            <tr >
                <td style="height: 50px; font-weight: bold;">Prepared By:</td>
                <td>{{ $forms->user->name }}</td>
                <td>
                    @if( !empty($forms->model->date_submitted) )
                    <img src="{{ public_path($forms->user->signature)}}" height="50" width="150">
                    @endif
                </td>
                <td>
                    @if( !empty($forms->model->date_submitted) )
                        {{ $forms->model->date_submitted }}
                    @endif
                </td>
            </tr>
            <tr>
                <td style="height: 50px; font-weight: bold;">Endorsed By:</td>
                <td>{{ $forms->endorsed->name }}</td>
                <td>
                    @if( !empty($forms->date_endorsed) )
                    <img src="{{ public_path($forms->endorsed->signature)}}" height="50" width="150">
                    @endif
                </td>
                <td>
                    @if( !empty($forms->date_endorsed) )
                        {{ $forms->date_endorsed }}
                    @endif
                </td>
            </tr>
            <tr>
                <td style="height: 50px; font-weight: bold;">Approved By:</td>
                <td>{{ $forms->approved->name }}</td>
                <td>
                    @if( !empty($forms->date_approved) )
                    <img src="{{ public_path($forms->approved->signature)}}" height="50" width="150">
                    @endif
                </td>
                <td>
                    @if( !empty($forms->date_approved) )
                        {{ $forms->date_approved }}
                    @endif
                </td>
            </tr>
        </tbody>
    </table> -->
    <p></p>

    <div class="container">
    <p></p>

        <div class="headers">Receiving Copy</div>
        
        <div class="acknowledgment">
            I hereby acknowledge receipt of the above-mentioned item(s) in good condition and complete as indicated.
        </div>

        <table class="signature-table">
            <tr>
                <td>
                    <div class="data-text">
                        {{ $forms->model->recipient }}
                    </div>
                    <div class="line"></div>
                    <div class="labels">Signature</div>
                </td>

                <td class="spacer"></td>

                <td>
                    <div class="data-text">
                       {{ $forms->model->date_received }}
                    </div>
                    <div class="line"></div>
                    <div class="labels">Date</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="data-text">

                    </div>
                    <div class="labels"></div>
                    <div class="labels"></div>
                </td>

                <td class="spacer"></td>

                <td>
                    <div class="data-text">
                    </div>
                    <div class="labels"></div>
                    <div class="labels"></div>
                </td>
            </tr>
            <tr>
                <td class="spacer"></td>
                <td class="spacer"></td>
                <td class="spacer"></td>
            </tr>
            <tr>
                <td class="spacer"></td>
                <td class="spacer"></td>
                <td class="spacer"></td>
            </tr>
            <tr>
                <td class="spacer"></td>
                <td class="spacer"></td>
                <td class="spacer"></td>
            </tr>
            <tr>
                <td class="spacer"></td>
                <td class="spacer"></td>
                <td class="spacer"></td>
            </tr>
            <tr>
                <td class="spacer"></td>
                <td class="spacer"></td>
                <td class="spacer"></td>
            </tr>

            <tr>
                <td>
        
                </td>

                <td class="labels">Checked By:</td>

                <td>
                    <div class="data-text">
                       {{ $forms->date_received }}
                    </div>
                    <div class="line"></div>
                    <div class="labels">Security Guard / Date</div>
                </td>
            </tr>
        </table>

    </div>
    <div class="signature-wrapper">
        <div class="sig-column">
            <img src="{{ public_path($forms->user->signature)}}" height="75" width="150">
            <div >{{ $forms->model->date_submitted }}</div>
            <div class="sig-name">{{ $forms->user->name }}</div>
            <div class="sig-line">Prepared By</div>
        </div>

        <div class="sig-column">
            @if( !empty($forms->date_endorsed) && $forms != 'declined' )
            <img src="{{ public_path($forms->endorsed->signature)}}" height="75" width="150">
            <div >{{ $forms->date_endorsed }}</div>
            <div class="sig-name">{{ $forms->endorsed->name }}</div>
            @endif
            <div class="sig-line">Endorsed By</div>
        </div>

        <div class="sig-column">
            @if( !empty($forms->date_approved) && $forms != 'declined' )
            <img src="{{ public_path($forms->approved->signature)}}" height="75" width="150">
            <div >{{ $forms->date_approved }}</div>
            <div class="sig-name">{{ $forms->approved->name }}</div>
            @endif
            <div class="sig-line">Approved By</div>
        </div>
    </div>

</main>

</body>
</html>
