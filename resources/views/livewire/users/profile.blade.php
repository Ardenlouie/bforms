<div>
    <div class="row">
        <div class="col-lg-12">
            <div class="text-left">
                @if(!empty($profile_pic))
                    <img class="img-fluid img-circle profile-img"
                        src="{{$profile_pic->temporaryUrl()}}"
                        alt="User profile picture">
                @else
                    <img class="img-fluid img-circle profile-img"
                        src="{{$user->adminlte_image()}}"
                        alt="User profile picture">
                @endif
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <label for="profile_pic">{{__('adminlte::profile.profile_picture')}}</label>
                <input type="file" class="form-control form-control-sm" id="profile_pic" wire:model.live="profile_pic">
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-lg-12">
            <button class="btn btn-primary" wire:click.prevent="changeProfile">
                <i class="fa fa-save mr-1"></i>
                    Save Profile Pic
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="text-left">
                @if(!empty($signature))
                    <img class="img-fluid"
                        src="{{$signature->temporaryUrl()}}"
                        alt="User signature"
                        height="100" width="200">
                @else
                    <img class="img-fluid"
                        src="{{$user->adminlte_signature()}}"
                        alt="User signature"
                        height="100" width="200">
                @endif
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <label for="signature">E-Signature</label>
                <input type="file" class="form-control form-control-sm" id="signature" wire:model.live="signature">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <button class="btn btn-primary" wire:click.prevent="changeSignature">
                <i class="fa fa-save mr-1"></i>
                    Save Signature
            </button>
        </div>
    </div>
</div>
