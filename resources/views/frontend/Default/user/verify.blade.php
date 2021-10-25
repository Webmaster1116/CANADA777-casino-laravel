@extends('frontend.Default.user.profile')
@section('content')
<div class="navigation-sibling">
    <div class="layout-column layout-column-1" data-column-id="column-1" id="column-1">
        <div class="portlet-dropzone portlet-column-content fn-portlet-container column-1">
            <div class="portlet portlet-wrapper fn-portlet-wrapper portlet-boundary portlet-56 portlet_type_no-border">
                <div class="fn-portlet portlet__content portlet__content_border_none portlet__content_type_56 ">
                    <article data-web-content-id="ACCOUNT_VERIFICATION">
                        <div class="generic-text-wrapper">
                            <h1>Account Verification</h1>
                            <div class="account-verification-wrapper">
                                <div class="account-verification-header">
                                    <p> @if($adminVerified == -1)
                                            To verify your account and continue playing at canada777.com. Please upload the requested documents below. 
                                        @elseif($adminVerified == 0 && $idVerified && $addressVerified) 
                                            You have already submit verify documents. Please wait until verify will be finished. 
                                        @elseif($adminVerified == 1)  
                                            You have already verified.
                                        @endif
                                    </p>
                                </div>
                                @if(!$idVerified || !$addressVerified)
                                <div class="account-verification-body">
                                    <div class="box-wrapper">
                                        <div class="line">
                                            <div class="box-item" name="idArea">
                                                <a href="javascript:javascript:void(0);" target="_self">
                                                    <img class="box-content" id="idImage" src="/frontend/Page/image/account-verification-id.png" @if(!$idVerified) onClick="uploadImg('id');"@endif>
                                                    @if(!$idVerified)
                                                    <img class="plus-icon" id="addIdImage" src="/frontend/Page/image/account-verification-link.png" onClick="uploadImg('id');">
                                                    @else
                                                    <img class="plus-icon" id="addIdImage" src="/frontend/Page/image/account-verification-submit.png">
                                                    @endif
                                                </a>
                                            </div>
                                            <div class="box-item" name="addressArea">
                                                <a href="javascript:javascript:void(0);"> 
                                                    <img class="box-content" id="addressImage" src="/frontend/Page/image/account-verification-address.png" @if(!$addressVerified)  onClick="uploadImg('address');"@endif>
                                                    @if(!$addressVerified)
                                                    <img class="plus-icon" id="addAddressImage" src="/frontend/Page/image/account-verification-link.png" onClick="uploadImg('address');">
                                                    @else
                                                    <img class="plus-icon" id="addAddressImage" src="/frontend/Page/image/account-verification-submit.png">
                                                    @endif
                                                </a>
                                            </div>
                                            <input type="file" id="imageFile" style="display:none" accept="image/*">
                                            <input type="text" id="imageType" style="display:none">
                                        </div>
                                    </div>
                                    <div class="line-item">
                                        <p>*Max Size 20MB.</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection