@extends('frontend.Default.user.profile')
@section('content')
<div class="navigation-sibling">
    <div class="layout-column layout-column-1">
        <div class="portlet-dropzone portlet-column-content fn-portlet-container column-1">
            <div class="portlet portlet_name_56 portlet-wrapper fn-portlet-wrapper portlet-boundary portlet-boundary_56_INSTANCE_8jhHr00eAgvG_ portlet-56 portlet_type_border">
                <div class="portlet-title fn-portlet-title">
                    <span class="fn-portlet-title-region portlet-title__text-wrapper"><span class="portlet-title-text">Change Password</span></span>
                </div>
                <div class="fn-portlet portlet__content portlet__content_border_show portlet__content_type_56 ">
                    <article data-web-content-id="CH_PASS">
                        <p>
                            <div class="fn-replacer js-replacer change-password-replacer">
                                <form action="" class="form form_name_user-change-password fn-changepassword-form">
                                    <div class="form-messages fn-form-messages">
                                    <p class="message error" style="display:none;"></p>
                                    </div>
                                    <div class="form__fieldset">
                                        <div class="field field_name_old-password fn-validate password">
                                            <div class="field__control">
                                                <input type="password" name="oldPassword" placeholder="Old password" class="fn-input-type-password">
                                                <div class="password-visibility fn-toggle-password-visibility"></div>
                                            </div>
                                        </div>
                                        <div class="field field_name_new-password fn-validate password" data-validation-type="password">
                                            <div class="field__control"><input type="password" name="newPassword" placeholder="New password" class="fn-input-type-password">
                                                <div class="password-visibility fn-toggle-password-visibility"></div>
                                            </div>
                                        </div>
                                        <div class="field field_name_confirm-new-password fn-validate fn-new-password-confirm password" data-validation-type="passwordVerify">
                                            <div class="field__control">
                                                <input type="password" name="passwordVerify" placeholder="Confirm new password" class="fn-input-type-password">
                                                <div class="password-visibility fn-toggle-password-visibility"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form__actions"><button class="btn" type="button" onClick="changePassword();">Change</button></div>
                                </form>
                            </div>
                        </p>
                    </article>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection