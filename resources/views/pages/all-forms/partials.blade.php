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
        @foreach($all_forms as $all_form)
            <tr>
                <td class="align-middle text-center">

                    @if($all_form->model->company->id == 1)
                    <img src="{{asset('/images/bevinobg.png')}}" alt="product photo" class="product-img" height="50" width="100">
                    @elseif($all_form->model->company->id == 2)
                    <img src="{{asset('/images/bevanobg.png')}}" alt="product photo" class="product-img" height="50" width="80">
                    @elseif($all_form->model->company->id == 3)
                    <img src="{{asset('/images/biginobg.png')}}" alt="product photo" class="product-img" height="50" width="100">
                    @elseif($all_form->model->company->id == 4)
                    <img src="{{asset('/images/bevminobg.png')}}" alt="product photo" class="product-img" height="50" width="100">
                    @elseif($all_form->model->company->id == 5)
                    <img src="{{asset('/images/osp.png')}}" alt="product photo" class="product-img" height="50" width="100">
                    @elseif($all_form->model->company->id == 6)
                    <img src="{{asset('/images/pbb.png')}}" alt="product photo" class="product-img" height="50" width="100">
                    @endif
                    <br>
                    <b>{{$all_form->model->control_number}}</b>

                </td>
                <td class="align-middle text-center">
                    {{$all_form->form->name}}
                </td>
                <td class="align-middle text-center">
                    {{$all_form->user->name}}
                </td>
                <td class="align-middle text-center">
                    {{$all_form->model->date_submitted}}
                </td>
                <td class="align-middle text-center">
                    <b>
                    @if($all_form->status == 'endorsement')
                        <span class="badge badge-info">Endorsement</span>
                    @elseif($all_form->status == 'confirmation')
                        <span class="badge badge-warning"><b>Confirmation</b></span>
                    @elseif($all_form->status == 'confirming')
                        <span class="badge badge-warning"><b>Confirmation</b></span>
                    @elseif($all_form->status == 'confirmed')
                        <span class="badge badge-warning"><b>Confirmation</b></span>
                    @elseif($all_form->status == 'approval')
                        <span class="badge badge-primary">Final Approval</span>
                    @elseif($all_form->status == 'draft')
                        <span class="badge badge-secondary">DRAFT</span>
                    @elseif($all_form->status == 'approved')
                        <span class="badge badge-success">Approved</span>
                    @elseif($all_form->status == 'checked')
                        <span class="badge bg-purple"><b>Checked</b></span>
                    @elseif($all_form->status == 'received')
                        <span class="badge bg-lime"><b>Received</b></span>
                    @elseif($all_form->status == 'declined')
                        <span class="badge badge-danger">Declined</span>
                    @elseif($all_form->status == 'cancelled')
                        <span class="badge badge-danger"><b>Cancelled</b></span>
                    @elseif($all_form->status == 'partially_released')
                        <span class="badge bg-orange"><b>Partially Released</b></span>
                    @else
                        <span class="badge bg-dark">Pending</span>
                    @endif
                    </b>
                </td>
                <td class="align-middle text-center">
                    <b>
                    @php
                        $approvers = \App\Models\User::whereIn('id', $all_form->approver ?? [])->get();
                        $endorsers = \App\Models\User::whereIn('id', $all_form->endorser ?? [])->get();
                        $brands = \App\Models\User::whereIn('id', $all_form->bm_signs ?? [])->get();
                        $group_brands = \App\Models\User::whereIn('id', $all_form->gbm_signs ?? [])->get();
                    @endphp

                    @if($all_form->status == 'endorsement')
                        @foreach($endorsers as $id => $endorser)
                            <span class="badge badge-info">
                                <i class="fas fa-file-signature"></i> {{ $endorser->name }}
                            </span>
                            @if(!$loop->last)
                                <span class="mx-1 text-muted font-weight-bold">or</span>
                            @endif
                        @endforeach
                    @elseif($all_form->status == 'approval')
                        @foreach($approvers as $id => $approver)
                            <span class="badge badge-primary">
                                <i class="fas fa-file-signature"></i> {{ $approver->name }}
                            </span>
                            @if(!$loop->last)
                                <span class="mx-1 text-muted font-weight-bold">or</span>
                            @endif
                        @endforeach
                    @elseif($all_form->status == 'confirmation')
                        <span class="badge badge-warning">
                            <i class="fas fa-file-signature"></i> {{$all_form->admin->name}}
                        </span>
                    @elseif($all_form->status == 'confirming')
                        @foreach($brands as $id => $brand)
                            <span class="badge badge-warning">
                                <i class="fas fa-file-signature"></i> {{ $brand->name }}
                            </span>
                            @if(!$loop->last)
                                <span class="mx-1 text-muted font-weight-bold">&</span>
                            @endif
                        @endforeach
                    @elseif($all_form->status == 'confirmed')
                        @foreach($group_brands as $id => $group_brand)
                            <span class="badge badge-warning">
                                <i class="fas fa-file-signature"></i> {{ $group_brand->name }}
                            </span>
                            @if(!$loop->last)
                                <span class="mx-1 text-muted font-weight-bold">&</span>
                            @endif
                        @endforeach
                    @elseif($all_form->status == 'approved')
                        <span class="badge badge-success">Completed</span>
                    @elseif($all_form->status == 'checked')
                        <span class="badge bg-purple"><b>Checked</b></span>
                    @elseif($all_form->status == 'received')
                        <span class="badge bg-lime"><b>Acknowledged & Received</b></span>
                    @elseif($all_form->status == 'draft')
                        <span class="badge badge-secondary">DRAFT</span>
                    @elseif($all_form->status == 'declined')
                        <span class="badge badge-danger"><b>Declined</b></span>
                    @elseif($all_form->status == 'cancelled')
                        <span class="badge badge-danger"><b>Cancelled</b></span>
                    @else
                        <span class="badge bg-dark"><b>Pending</b></span>
                    @endif
                    </b>
                </td>
                <td class="align-middle text-right">
                    <a href="#" title="view" data-id="{{$all_form->id}}" data-form="{{$all_form->form_id}}" class="btn-view btn bg-dark btn-xs mb-0 ml-0">
                        <i class="fa fa-eye"></i> View
                    </a>
                    <a href="{{ route('myforms.edit', encrypt($all_form->id)) }}" title="edit" class="btn-edit btn bg-warning btn-xs mb-0 ml-0">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('myforms.show', encrypt($all_form->id)) }}" title="show" class="btn bg-orange btn-xs mb-0 ml-0">
                        <i class="fa fa-file-contract"></i> Show
                    </a>
                    <a href="#" title="signatures" data-id="{{$all_form->id}}" data-form="{{$all_form->form_id}}" class="btn btn-signatures btn-success btn-xs mb-0 ml-0">
                        <i class="fa fa-file-signature"></i> Approvers
                    </a>
                    <a href="#" title="delete" data-id="{{encrypt($all_form->id)}}" class="btn-delete btn bg-danger btn-xs mb-0 ml-0 ">
                        <i class="fa fa-trash-alt"></i> Delete
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $all_forms->appends(request()->query())->links() }}
