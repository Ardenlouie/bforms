<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{__('adminlte::org-structures.org_structure_maintenance')}}</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- LIST -->
                <div class="col-lg-6">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{__('adminlte::org-structures.structure_list')}}</h3>
                        </div>
                        <div class="card-body table-responsive p-0" style="max-height: 400px; overflow: auto;">
                            <table class="table table-sm table-striped table-hover mb-0 rounded">
                                <thead>
                                    <tr>
                                        <th>{{__('adminlte::users.user')}}</th>
                                        <th>{{__('adminlte::org-structures.title')}}</th>
                                        <th>{{__('adminlte::org-structures.reports_to')}}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($structure_trees as $structure)
                                        <tr>
                                            <td>{{$structure->user->name ?? 'Vacant'}}</td>
                                            <td>{{$structure->title ?? '-'}}</td>
                                            <td>{{$reports_to_arr[$structure->id]}}</td>
                                            <td class="text-right">
                                                <button class="btn btn-xs btn-primary" wire:click.prevent="editStructure('{{encrypt($structure->id)}}')"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-xs btn-danger" wire:click="confirmDelete({{$structure->id}})"><i class="fas fa-trash-alt"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            {{$structure_trees->links()}}
                        </div>
                    </div>
                </div>
                <!-- FORM -->
                <div class="col-lg-6">
                    <div class="card card-outline {{$type == 'add' ? 'card-primary' : 'card-success'}}">
                        <div class="card-header">
                            @if($type == 'add')
                                <h3 class="card-title">{{__('adminlte::org-structures.org_structure_form')}}</h3>
                            @elseif($type == 'edit')
                                <h3 class="card-title">{{__('adminlte::org-structures.org_structure_form')}}</h3>
                                <div class="card-tools">
                                    <button class="btn btn-xs btn-primary" wire:click.prevent="addNew()">
                                        <i class="fa fa-plus"></i>
                                        {{__('adminlte::org-structures.new_org_structure')}}
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div class="card-body">
                            <h4>{{__('adminlte::org-structures.new_org_structure')}}</h4>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">{{__('adminlte::org-structures.reports_to')}}</label>
                                        <select class="form-control form-control-sm" wire:model.live="reports_to_id">
                                            <option value="">{{__('adminlte::utilities.select')}}</option>
                                            @foreach($org_structure->structure_trees as $tree)
                                                <option value="{{$tree->id}}">{{$tree->user->name ?? 'Vacant'}} - {{$tree->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="user_id">{{__('adminlte::users.user')}}</label>
                                        <select name="user_id" class="form-control form-control-sm" wire:model.live="user_id" id="user_id">
                                            <option value="">{{__('adminlte::org-structures.vacant')}}</option>
                                            @foreach($users as $user)
                                                <option value="{{$user->id}}">{{$user->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="title">{{__('adminlte::org-structures.title')}}</label>
                                        <input type="text" class="form-control form-control-sm" wire:model.live="title" id="title" placeholder="{{__('adminlte::org-structures.title')}}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button class="btn btn-sm btn-secondary" wire:click.prevent="resetForm()">
                                <i class="fa fa-recycle"></i>
                                {{__('adminlte::utilities.reset')}}
                            </button>
                            <button class="btn btn-sm btn-primary" wire:click.prevent="save()">
                                <i class="fa fa-save"></i>
                                {{__('adminlte::utilities.save')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
