<table class="table table-striped table-hover mb-0 rounded">
    <thead class="text-center bg-gradient-navy">
        <tr class="text-center">
            <th>Reference No.</th>
            <th>Form Name</th>
            <th>Created By</th>
            <th>Date Submitted</th>
            <th>Status</th>
            <th>Next Approver</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($my_forms as $my_form)
            <tr>
                <td class="align-middle text-center">

                    @if($my_form->model->company->id == 1)
                    <img src="{{asset('/images/bevinobg.png')}}" alt="product photo" class="product-img" height="50" width="100">
                    @elseif($my_form->model->company->id == 2)
                    <img src="{{asset('/images/bevanobg.png')}}" alt="product photo" class="product-img" height="50" width="80">
                    @elseif($my_form->model->company->id == 3)
                    <img src="{{asset('/images/biginobg.png')}}" alt="product photo" class="product-img" height="50" width="100">
                    @elseif($my_form->model->company->id == 4)
                    <img src="{{asset('/images/bevminobg.png')}}" alt="product photo" class="product-img" height="50" width="100">
                    @elseif($my_form->model->company->id == 5)
                    <img src="{{asset('/images/osp.png')}}" alt="product photo" class="product-img" height="50" width="100">
                    @elseif($my_form->model->company->id == 6)
                    <img src="{{asset('/images/pbb.png')}}" alt="product photo" class="product-img" height="50" width="100">
                    @endif
                    <br>
                    <b>{{$my_form->model->control_number}}</b>

                </td>
                <td class="align-middle text-center">
                    {{$my_form->form->name}}
                </td>
                <td class="align-middle text-center">
                    {{$my_form->user->name}}
                </td>
                <td class="align-middle text-center">
                    {{$my_form->model->date_submitted}}
                </td>
                <td class="align-middle text-center">
                    @if($my_form->status == 'draft')
                        <span class="badge badge-secondary"><b>DRAFT</b></span>
                    @elseif($my_form->status == 'confirmation')
                        <span class="badge badge-warning"><b>Confirmation</b></span>
                    @elseif($my_form->status == 'confirming')
                        <span class="badge badge-warning"><b>Confirmation</b></span>
                    @elseif($my_form->status == 'confirmed')
                        <span class="badge badge-warning"><b>Confirmation</b></span>
                    @elseif($my_form->status == 'endorsement')
                        <span class="badge badge-info"><b>Endorsement</b></span>
                    @elseif($my_form->status == 'approval')
                        <span class="badge badge-primary"><b>Final Approval</b></span>
                    @elseif($my_form->status == 'approved')
                        <span class="badge badge-success"><b>Approved</b></span>
                    @elseif($my_form->status == 'processing')
                        <span class="badge bg-navy"><b>For Processing</b></span>
                    @elseif($my_form->status == 'declined')
                        <span class="badge badge-danger"><b>Declined</b></span>
                    @elseif($my_form->status == 'cancelled')
                        <span class="badge badge-danger"><b>Cancelled</b></span>
                    @elseif($my_form->status == 'checked')
                        <span class="badge bg-purple"><b>Checked</b></span>
                    @elseif($my_form->status == 'received')
                        <span class="badge bg-lime"><b>Received</b></span>
                    @elseif($my_form->status == 'liquidated')
                        <span class="badge bg-navy"><b>Liquidated</b></span>
                    @elseif($my_form->status == 'partially_released')
                        <span class="badge bg-orange"><b>Partially Released</b></span>
                    @else
                        <span class="badge bg-dark"><b>Pending</b></span>
                    @endif
                </td>
                <td class="align-middle text-center">
                    <b>
                    @php
                        $approvers = \App\Models\User::whereIn('id', $my_form->approver ?? [])->get();
                        $endorsers = \App\Models\User::whereIn('id', $my_form->endorser ?? [])->get();
                        $brands = \App\Models\User::whereIn('id', $my_form->bm_signs ?? [])->get();
                        $group_brands = \App\Models\User::whereIn('id', $my_form->gbm_signs ?? [])->get();
                    @endphp

                    @if($my_form->status == 'draft')
                        
                    @elseif($my_form->status == 'endorsement')
                        @foreach($endorsers as $id => $endorser)
                            <span class="badge badge-info">
                                <i class="fas fa-file-signature"></i> {{ $endorser->name }} 
                            </span>
                            @if(!$loop->last)
                                <span class="mx-1 text-muted font-weight-bold">or</span>
                            @endif
                        @endforeach
                    @elseif($my_form->status == 'approval')
                        @foreach($approvers as $id => $approver)
                            <span class="badge badge-primary">
                                <i class="fas fa-file-signature"></i> {{ $approver->name }} 
                            </span>
                            @if(!$loop->last)
                                <span class="mx-1 text-muted font-weight-bold">or</span>
                            @endif
                        @endforeach
                    @elseif($my_form->status == 'confirmation')
                        <span class="badge badge-warning">
                            <i class="fas fa-file-signature"></i> {{$my_form->admin->name}}
                        </span>
                    @elseif($my_form->status == 'confirming')
                        @foreach($brands as $id => $brand)
                            <span class="badge badge-warning">
                                <i class="fas fa-file-signature"></i> {{ $brand->name }}
                            </span>
                            @if(!$loop->last)
                                <span class="mx-1 text-muted font-weight-bold">&</span>
                            @endif
                        @endforeach
                    @elseif($my_form->status == 'confirmed')
                        @foreach($group_brands as $id => $group_brand)
                            <span class="badge badge-warning">
                                <i class="fas fa-file-signature"></i> {{ $group_brand->name }}
                            </span>
                            @if(!$loop->last)
                                <span class="mx-1 text-muted font-weight-bold">&</span>
                            @endif
                        @endforeach
                    @elseif($my_form->status == 'processing')
                        {{$my_form->processed->name}}
                    @elseif($my_form->status == 'approved')
                        <span class="badge badge-success"><b>Completed</b></span>
                    @elseif($my_form->status == 'checked')
                        <span class="badge bg-purple"><b>For Receiving</b></span>
                    @elseif($my_form->status == 'received')
                        <span class="badge bg-lime"><b>Acknowledged & Received</b></span>
                    @elseif($my_form->status == 'liquidated')
                        <span class="badge bg-navy"><b>Received & Liquidated</b></span>
                    @elseif($my_form->status == 'declined')
                        <span class="badge badge-danger"><b>Declined</b></span>
                    @elseif($my_form->status == 'cancelled')
                        <span class="badge badge-danger"><b>Cancelled</b></span>
                    @else
                        <span class="badge bg-dark"><b>Pending</b></span>
                    @endif
                    </b>
                </td>
                <td class="align-middle text-right">
                    @if($my_form->status == 'draft' || $my_form->status == 'declined')
                        <a href="{{ route('myforms.edit', encrypt($my_form->id)) }}" title="edit" class="btn-edit btn bg-warning btn-xs mb-0 ml-0 ">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                    @endif
                    <a href="#" title="view" data-id="{{$my_form->id}}" data-form="{{$my_form->form_id}}" class="btn-view btn bg-dark btn-xs mb-0 ml-0">
                        <i class="fa fa-eye"></i> View
                    </a>
                    @if($my_form->status == 'approved' || $my_form->status == 'declined' || $my_form->status == 'partially_released' || $my_form->status == 'checked' || $my_form->status == 'received' || $my_form->status == 'cancelled' || $my_form->status == 'liquidated')
                        <a href="{{ route('myforms.show', encrypt($my_form->id)) }}" title="show" class="btn bg-orange btn-xs mb-0 ml-0">
                            <i class="fa fa-file-contract"></i> Show
                        </a>
                    @endif
                    <a href="#" title="signatures" data-id="{{$my_form->id}}" data-form="{{$my_form->form_id}}" class="btn-signatures btn bg-success btn-xs mb-0 ml-0">
                        <i class="fa fa-file-signature"></i> Approvers
                    </a>
                    @if($my_form->status != 'approved' && $my_form->status != 'checked' && $my_form->status != 'received' && $my_form->status != 'cancelled' && $my_form->status != 'liquidated')
                    <a href="#" title="cancel" data-id="{{$my_form->id}}" data-form="{{$my_form->form_id}}" class="btn-cancel btn bg-danger btn-xs mb-0 ml-0">
                        <i class="fa fa-ban"></i> Cancel
                    </a>
                    @endif

                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $my_forms->appends(request()->query())->links() }}

