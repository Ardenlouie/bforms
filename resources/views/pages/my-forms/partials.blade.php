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
                    @elseif($my_form->status == 'endorsement')
                        <span class="badge badge-info"><b>Endorsement</b></span>
                    @elseif($my_form->status == 'approval')
                        <span class="badge badge-primary"><b>Final Approval</b></span>
                    @elseif($my_form->status == 'approved')
                        <span class="badge badge-success"><b>Approved</b></span>
                    @elseif($my_form->status == 'declined')
                        <span class="badge badge-danger"><b>Declined</b></span>
                    @elseif($my_form->status == 'checked')
                        <span class="badge bg-purple"><b>Received & Checked</b></span>
                    @else
                        <span class="badge bg-dark"><b>Pending</b></span>
                    @endif
                </td>
                <td class="align-middle text-center">
                    <b>
                    @if($my_form->status == 'draft')
                        
                    @elseif($my_form->status == 'endorsement')
                        {{$my_form->endorsed->name}}
                    @elseif($my_form->status == 'approval')
                        {{$my_form->approved->name}}
                    @elseif($my_form->status == 'approved')
                        <span class="badge badge-success"><b>Completed</b></span>
                    @elseif($my_form->status == 'checked')
                        <span class="badge badge-success"><b>Completed</b></span>
                    @elseif($my_form->status == 'declined')
                        <span class="badge badge-danger"><b>Declined</b></span>
                    @else
                        <span class="badge bg-dark"><b>Pending</b></span>
                    @endif
                    </b>
                </td>
                <td class="align-middle text-right">
                    @if($my_form->status == 'draft' || $my_form->status == 'declined')
                        <a href="{{ route('myforms.edit', encrypt($my_form->id)) }}" title="edit" class="btn-edit btn ">
                            <i class="fa fa-edit text-warning"></i>
                        </a>
                    @endif
                    @if($my_form->status == 'approved' || $my_form->status == 'declined')
                        <a href="{{ route('myforms.show', encrypt($my_form->id)) }}" title="show" class="btn">
                            <i class="fa fa-file-contract text-orange"></i>
                        </a>
                    @endif
                    <a href="#" title="view" data-id="{{$my_form->id}}" data-form="{{$my_form->form_id}}" class="btn-view btn ">
                        <i class="fa fa-eye text-dark"></i>
                    </a>
                    <a href="#" title="signatures" data-id="{{$my_form->id}}" data-form="{{$my_form->form_id}}" class="btn-signatures btn ">
                        <i class="fa fa-file-signature text-success"></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{$my_forms->links()}}
