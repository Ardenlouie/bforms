<?php

use Livewire\Component;
use App\Models\Department;
use App\Models\Category;
use App\Models\Form;
use Livewire\WithPagination;
use App\Http\Traits\SettingTrait;

new class extends Component
{
    use SettingTrait;
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $activeTab = 'tab0';
    public $departments, $categories, $category_id = '', $search = '', $item_per_page;


    public function mount() {

        $this->item_per_page = '10';

        $this->categories = Category::all()->keyBy('id');

    }

    public function updatedSearch() {
        $this->resetPage('forms-page');
    }

    public function changeTab($tab, $categoryId = '')
    {
        $this->activeTab = $tab;

        $this->category_id = $categoryId;

        $this->resetPage('forms-page');

    }

    public function getFormsProperty() {
        return Form::where('display', 1)
                    ->when($this->category_id, function($query) {
                        $query->where('category_id', $this->category_id);
                    })
                    ->when($this->search, function($query) {
                        $query->where(function($qry) {
                            $qry->where('prefix', 'like', '%'.$this->search.'%')
                            ->orWhere('name', 'like', '%'.$this->search.'%');
                        });
                    })
                    ->paginate($this->item_per_page, ['*'], 'forms-page')->onEachSide(1);
    }

    public function navigateToForm($id)
    {
        return redirect()->to("/forms/{$id}");
    }


};
?>

<div>
    <div class="card">
        <div class="card-header bg-gradient-navy">
            <h3 class="card-title float-none text-center text-bold ">FORMS
                <i class="fa fa-spinner fa-spin" wire:loading></i></h3>
        </div>

        <div class="card-body">
            <div class="row ">
                <div class="col-lg-3"></div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <input type="text" placeholder="Search" class="form-control form-control-md text-center" wire:model.live ="search">
                    </div>
                </div>
                <div class="col-lg-3">
                    
                </div>
                <div class="col-lg-12 mb-3">
                    <ul class="nav nav-tabs justify-content-center">
                        <li class="nav-item">
                            <a class="btn nav-link {{ $activeTab === 'tab0' ? 'active bg-navy text-white' : 'btn-outline-secondary' }}" 
                            wire:click="changeTab('tab0', '')">
                                <b>ALL</b>
                            </a>
                        </li>
                        @foreach($this->categories as $key => $category)
                        <li class="nav-item text-center">
                            <a class="btn nav-link {{ $activeTab === 'tab'.$key ? 'active bg-navy text-white' : 'btn-outline-secondary' }}" 
                                wire:click="changeTab('tab{{$key}}','{{$key}}')">
                                <b>{{$category->name}}</b>
                            </a>
                        </li>
                        @endforeach
                        
                    </ul>
                </div>
            </div>
            <div class="tab-content">
                <div class="row justify-content-center">
                    <div class="col-md-6 ">
                        @foreach($this->forms as $key =>$form)
                            <div x-data="{ loading: false }">
                                <a href="/forms/list/{{ encrypt($form->id) }}" 
                                    class="btn bg-primary btn-block mb-3"
                                    @click="loading = true"
                                    :class="loading ? 'disabled' : ''"
                                    >
                                    <template x-if="!loading">
                                        <span>{{ $form->name }}</span>
                                    </template>
                                    
                                    <template x-if="loading">
                                        <span><i class="fas fa-circle-notch fa-spin"></i> Please wait...</span>
                                    </template>
                                </a>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
            @if($item_per_page != 'all')
            <div class="row">
                <div class="col-12">
                    {{$this->forms->links()}}
                </div>
            </div>
            @endif
            <a href="{{ asset('manuals/B-FORMS USER MANUAL.pdf') }}" 
                download="BFORMS_Manual.pdf" 
                class="btn btn-success float-right">
                <i class="fa fa-file-export"></i> Download System Manual
            </a>

        </div>
    </div>
</div>