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
        @foreach($approvals as $approval)
            <tr>
                <td class="align-middle text-center">

                    @if($approval->model->company->id == 1)
                    <img src="{{asset('/images/bevinobg.png')}}" alt="product photo" class="product-img" height="50" width="100">
                    @elseif($approval->model->company->id == 2)
                    <img src="{{asset('/images/bevanobg.png')}}" alt="product photo" class="product-img" height="50" width="80">
                    @elseif($approval->model->company->id == 3)
                    <img src="{{asset('/images/biginobg.png')}}" alt="product photo" class="product-img" height="50" width="100">
                    @elseif($approval->model->company->id == 4)
                    <img src="{{asset('/images/bevminobg.png')}}" alt="product photo" class="product-img" height="50" width="100">
                    @elseif($approval->model->company->id == 5)
                    <img src="{{asset('/images/osp.png')}}" alt="product photo" class="product-img" height="50" width="100">
                    @elseif($approval->model->company->id == 6)
                    <img src="{{asset('/images/pbb.png')}}" alt="product photo" class="product-img" height="50" width="100">
                    @endif
                    <br>
                    <b>{{$approval->model->control_number}}</b>

                </td>
                <td class="align-middle text-center">
                    {{$approval->form->name}}
                </td>
                <td class="align-middle text-center">
                    {{$approval->user->name}}
                </td>
                <td class="align-middle text-center">
                    {{$approval->model->date_submitted}}
                </td>
                <td class="align-middle text-center">
                    <b>
                    @if($approval->status == 'endorsement')
                        <span class="badge badge-info">Endorsement</span>
                    @elseif($approval->status == 'confirmation')
                        <span class="badge badge-warning"><b>Confirmation</b></span>
                    @elseif($approval->status == 'confirming')
                        <span class="badge badge-warning"><b>Confirmation</b></span>
                    @elseif($approval->status == 'confirmed')
                        <span class="badge badge-warning"><b>Confirmation</b></span>
                    @elseif($approval->status == 'approval')
                        <span class="badge badge-primary">Final Approval</span>
                    @elseif($approval->status == 'approved')
                        <span class="badge badge-success">Approved</span>
                    @elseif($approval->status == 'processing')
                        <span class="badge bg-navy"><b>For Processing</b></span>
                    @elseif($approval->status == 'checked')
                        <span class="badge bg-purple"><b>Checked</b></span>
                    @elseif($approval->status == 'received')
                        <span class="badge bg-lime"><b>Received</b></span>
                    @elseif($approval->status == 'declined')
                        <span class="badge badge-danger">Declined</span>
                    @elseif($approval->status == 'partially_released')
                        <span class="badge bg-orange"><b>Partially Released</b></span>
                    @else
                        <span class="badge bg-dark">Pending</span>
                    @endif
                    </b>
                </td>
                <td class="align-middle text-center">
                    <b>
                    @php
                        $approvers = \App\Models\User::whereIn('id', $approval->approver ?? [])->get();
                        $endorsers = \App\Models\User::whereIn('id', $approval->endorser ?? [])->get();
                        $brands = \App\Models\User::whereIn('id', $approval->bm_signs ?? [])->get();
                        $group_brands = \App\Models\User::whereIn('id', $approval->gbm_signs ?? [])->get();
                    @endphp

                    @if($approval->status == 'endorsement')
                        @foreach($endorsers as $id => $endorser)
                            <span class="badge badge-info">
                                <i class="fas fa-file-signature"></i> {{ $endorser->name }}
                            </span>
                            @if(!$loop->last)
                                <span class="mx-1 text-muted font-weight-bold">or</span>
                            @endif
                        @endforeach
                    @elseif($approval->status == 'approval')
                        @foreach($approvers as $id => $approver)
                            <span class="badge badge-primary">
                                <i class="fas fa-file-signature"></i> {{ $approver->name }}
                            </span>
                            @if(!$loop->last)
                                <span class="mx-1 text-muted font-weight-bold">or</span>
                            @endif
                        @endforeach
                    @elseif($approval->status == 'confirmation')
                        <span class="badge badge-navy">
                            <i class="fas fa-file-signature"></i> {{$approval->admin->name}}
                        </span>
                    @elseif($approval->status == 'confirming')
                        @foreach($brands as $id => $brand)
                            <span class="badge badge-warning">
                                <i class="fas fa-file-signature"></i> {{ $brand->name }}
                            </span>
                            @if(!$loop->last)
                                <span class="mx-1 text-muted font-weight-bold">&</span>
                            @endif
                        @endforeach
                    @elseif($approval->status == 'confirmed')
                        @foreach($group_brands as $id => $group_brand)
                            <span class="badge badge-warning">
                                <i class="fas fa-file-signature"></i> {{ $group_brand->name }}
                            </span>
                            @if(!$loop->last)
                                <span class="mx-1 text-muted font-weight-bold">&</span>
                            @endif
                        @endforeach
                    @elseif($approval->status == 'processing')
                        {{$approval->processed->name}}
                    @elseif($approval->status == 'approved')
                        <span class="badge badge-success">Completed</span>
                    @elseif($approval->status == 'checked')
                        <span class="badge bg-purple"><b>Checked</b></span>
                    @elseif($approval->status == 'received')
                        <span class="badge bg-lime"><b>Acknowledged & Received</b></span>
                    @elseif($approval->status == 'declined')
                        <span class="badge badge-danger"><b>Declined</b></span>
                    @else
                        <span class="badge bg-dark"><b>Pending</b></span>
                    @endif
                    </b>
                </td>
                <td class="align-middle text-right">
                    @if(in_array($user_id, $approval->endorser ?? []) && $approval->status == 'endorsement')
                        <a href="{{ route('approver.show', encrypt($approval->id)) }}" title="approve" class="btn bg-purple btn-xs mb-0 ml-0">
                            <i class="fa fa-pen-alt"></i> Sign
                        </a>
                    @elseif(in_array($user_id, $approval->approver ?? []) && $approval->status == 'approval')
                         <a href="{{ route('approver.show', encrypt($approval->id)) }}" title="approve" class="btn bg-purple btn-xs mb-0 ml-0">
                            <i class="fa fa-pen-alt"></i> Sign
                        </a>
                    @elseif($approval->admin_id == $user_id && $approval->status == 'confirmation')
                         <a href="{{ route('approver.show', encrypt($approval->id)) }}" title="approve" class="btn bg-purple btn-xs mb-0 ml-0">
                            <i class="fa fa-pen-alt"></i> Sign
                        </a>
                    @elseif(in_array($user_id, $approval->brands ?? []) && $approval->status == 'confirming')
                         <a href="{{ route('approver.show', encrypt($approval->id)) }}" title="approve" class="btn bg-purple btn-xs mb-0 ml-0">
                            <i class="fa fa-pen-alt"></i> Sign
                        </a>
                    @elseif(in_array($user_id, $approval->group_brands ?? []) && $approval->status == 'confirmed')
                         <a href="{{ route('approver.show', encrypt($approval->id)) }}" title="approve" class="btn bg-purple btn-xs mb-0 ml-0">
                            <i class="fa fa-pen-alt"></i> Sign
                        </a>
                    @else
                    @endif

                    <a href="#" title="view" data-id="{{$approval->id}}" data-form="{{$approval->form_id}}" class="btn btn-view btn-dark btn-xs mb-0 ml-0">
                        <i class="fa fa-eye"></i> View
                    </a>
                    @if($approval->status == 'approved' || $approval->status == 'partially_released' || $approval->status == 'checked' || $approval->status == 'received')
                        <a href="{{ route('approver.show', encrypt($approval->id)) }}" title="show" class="btn bg-orange btn-xs mb-0 ml-0">
                            <i class="fa fa-file-contract"></i> Show
                        </a>
                    @endif
                    
                    <a href="#" title="signatures" data-id="{{$approval->id}}" data-form="{{$approval->form_id}}" class="btn btn-signatures btn-success btn-xs mb-0 ml-0">
                        <i class="fa fa-file-signature"></i> Approvers
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{$approvals->links()}}
