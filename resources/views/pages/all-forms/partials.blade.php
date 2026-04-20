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
                    @elseif($all_form->status == 'approval')
                        <span class="badge badge-primary">Final Approval</span>
                    @elseif($all_form->status == 'draft')
                        <span class="badge badge-secondary">DRAFT</span>
                    @elseif($all_form->status == 'approved')
                        <span class="badge badge-success">Approved</span>
                    @elseif($all_form->status == 'checked')
                        <span class="badge bg-purple"><b>Received & Checked</b></span>
                    @elseif($all_form->status == 'declined')
                        <span class="badge badge-danger">Declined</span>
                    @elseif($all_form->status == 'partially_released')
                        <span class="badge bg-orange"><b>Partially Released</b></span>
                    @else
                        <span class="badge bg-dark">Pending</span>
                    @endif
                    </b>
                </td>
                <td class="align-middle text-center">
                    <b>
                    @if($all_form->status == 'endorsement')
                        {{$all_form->endorsed->name}}
                    @elseif($all_form->status == 'approval')
                        {{$all_form->approved->name}}
                    @elseif($all_form->status == 'confirmation')
                        {{$all_form->admin->name}}
                    @elseif($all_form->status == 'approved')
                        <span class="badge badge-success">Completed</span>
                    @elseif($all_form->status == 'checked')
                        <span class="badge badge-success">Completed</span>
                    @elseif($all_form->status == 'draft')
                        <span class="badge badge-secondary">DRAFT</span>
                    @elseif($all_form->status == 'declined')
                        <span class="badge badge-danger"><b>Declined</b></span>
                    @else
                        <span class="badge bg-dark"><b>Pending</b></span>
                    @endif
                    </b>
                </td>
                <td class="align-middle text-right">
                    <a href="#" title="view" data-id="{{$all_form->id}}" data-form="{{$all_form->form_id}}" class="btn-view btn ">
                        <i class="fa fa-eye text-dark"></i>
                    </a>
                    <a href="{{ route('myforms.edit', encrypt($all_form->id)) }}" title="edit" class="btn-edit btn ">
                        <i class="fa fa-edit text-warning"></i>
                    </a>
                    <a href="#" title="delete" data-id="{{encrypt($all_form->id)}}" class="btn-delete btn ">
                        <i class="fa fa-trash-alt text-danger"></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{$all_forms->links()}}
