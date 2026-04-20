<table class="table table-striped table-hover mb-0 rounded">
    <thead class="text-center bg-gradient-navy">
        <tr class="text-center">
            <th>Reference No.</th>
            <th>Form Name</th>
            <th>Created By</th>
            <th>Date Submitted</th>
            <th>Status</th>
            <th>Next Approver</th>
            <th></th>
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
                    @elseif($approval->status == 'approval')
                        <span class="badge badge-primary">Final Approval</span>
                    @elseif($approval->status == 'approved')
                        <span class="badge badge-success">Approved</span>
                    @elseif($approval->status == 'processing')
                        <span class="badge bg-navy"><b>For Processing</b></span>
                    @elseif($approval->status == 'checked')
                        <span class="badge bg-purple"><b>Received & Checked</b></span>
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
                    @if($approval->status == 'endorsement')
                        <span class="badge badge-info">
                            <i class="fas fa-file-signature"></i> {{$approval->endorsed->name}}
                        </span>
                    @elseif($approval->status == 'approval')
                        @php
                            $approvers = \App\Models\User::whereIn('id', $approval->approver ?? [])->get();
                        @endphp

                        @foreach($approvers as $id => $approver)
                            <span class="badge badge-primary">
                                <i class="fas fa-file-signature"></i> {{ $approver->name }}
                            </span>
                        @endforeach
                    @elseif($approval->status == 'confirmation')
                        <span class="badge badge-navy">
                            <i class="fas fa-file-signature"></i> {{$approval->admin->name}}
                        </span>
                    @elseif($approval->status == 'processing')
                        {{$approval->processed->name}}
                    @elseif($approval->status == 'approved')
                        <span class="badge badge-success">Completed</span>
                    @elseif($approval->status == 'checked')
                        <span class="badge badge-success">Completed</span>
                    @elseif($approval->status == 'declined')
                        <span class="badge badge-danger"><b>Declined</b></span>
                    @else
                        <span class="badge bg-dark"><b>Pending</b></span>
                    @endif
                    </b>
                </td>
                <td class="align-middle text-right">
                    @if($approval->endorser == $user_id && $approval->status == 'endorsement')
                        <a href="{{ route('approver.show', encrypt($approval->id)) }}" title="approve" class="btn">
                            <i class="fa fa-pen-alt text-purple"></i>
                        </a>
                    @elseif($approval->approver == $user_id && $approval->status == 'approval')
                         <a href="{{ route('approver.show', encrypt($approval->id)) }}" title="approve" class="btn">
                            <i class="fa fa-pen-alt text-purple"></i>
                        </a>
                    @elseif($approval->admin_id == $user_id && $approval->status == 'confirmation')
                         <a href="{{ route('approver.show', encrypt($approval->id)) }}" title="approve" class="btn">
                            <i class="fa fa-pen-alt text-purple"></i>
                        </a>
                    @else

                    @endif

                    @if($approval->status == 'approved' || $approval->status == 'partially_released' || $approval->status == 'checked')
                        <a href="{{ route('approver.show', encrypt($approval->id)) }}" title="show" class="btn">
                            <i class="fa fa-file-contract text-orange"></i>
                        </a>
                    @endif
                    <a href="#" title="view" data-id="{{$approval->id}}" data-form="{{$approval->form_id}}" class="btn-view btn ">
                        <i class="fa fa-eye text-dark"></i>
                    </a>
                    <a href="#" title="signatures" data-id="{{$approval->id}}" data-form="{{$approval->form_id}}" class="btn-signatures btn ">
                        <i class="fa fa-file-signature text-success"></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{$approvals->links()}}
