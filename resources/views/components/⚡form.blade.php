<?php

use Livewire\Component;
use App\Models\Department;
use App\Models\Category;
use App\Models\Form;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $activeTab = 'tab1';
    public $departments, $categories, $category_id=1;


    public function mount() {

        $this->item_per_page = '5';

        $this->categories = Category::all()->keyBy('id');

    }

    public function changeTab($tab, $category_id)
    {
        $this->activeTab = $tab;

        $this->category_id = $category_id;

        $this->resetPage('forms-page');

    }

    public function getFormsProperty() {
        return Form::where('category_id', $this->category_id)->get();
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
                <div class="col-lg-12 mb-3">
                    <ul class="nav nav-tabs justify-content-center">
                        @foreach($this->categories as $key => $category)
                        <li class="nav-item text-center">
                            <a class="btn nav-link {{ $activeTab === 'tab'.$key ? 'active bg-navy text-white' : 'btn-outline-secondary' }}" 
                                wire:click="changeTab('tab{{$key}}','{{$key}}')">
                                <b>{{$category->name}}</b>
                            </a>
                        </li>
                        @endforeach
                        <li class="nav-item">
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content">
                @foreach($this->categories as $key => $category)
                <div class="tab-pane {{ $activeTab === 'tab'.$key ? 'active' : '' }}" id="tab{{$key}}">
                    <div class="row justify-content-center">
                        <div class="col-md-6 ">
                            @foreach($this->forms as $key =>$form)
                                <div x-data="{ loading: false }">
                                    <a href="/forms/{{ encrypt($form->id) }}" 
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
                @endforeach
            </div>

        </div>
    </div>
</div>