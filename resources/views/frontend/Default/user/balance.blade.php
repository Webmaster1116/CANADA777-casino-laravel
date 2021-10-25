@extends('frontend.Default.user.profile')
@section('content')
<div class="navigation-sibling">
    <div class="layout-column layout-column-1">
        <div class="portlet-dropzone portlet-column-content fn-portlet-container column-1">
            <div class="portlet portlet-wrapper fn-portlet-wrapper portlet-boundary portlet_type_no-border">
                <div class="fn-portlet portlet__content portlet__content_border_none portlet__content_type_56 ">
                    <article data-web-content-id="BALANCE">
                        <div class="page-layout__content my-balance">
                            <ul class="info-list">
                                <li class="info-list__row row">
                                    <span class="info-list__field col-md-6">Total Balance</span>
                                    <span class="info-list__field info-list__field_type_value col-md-6" style="font-weight: 700;"> <span class="val_type_user_balance fn-replacer js-replacer js-replacer-inline casino-gaming-balance-replacer">$ {{number_format((float)Auth::user()->balance, 2, '.', '')}}</span></span>
                                </li>
                                <li class="info-list__row row">
                                    <span class="info-list__field col-md-6">Bonus Balance</span>
                                    <span class="info-list__field info-list__field_type_value col-md-6" style="font-weight: 700;"> <span class="val_type_user_balance fn-replacer js-replacer js-replacer-inline total-bonus-balance-replacer">$ {{number_format((float)Auth::user()->bonus, 2, '.', '')}}</span></span>
                                </li>
                                <li class="info-list__row row">
                                    <span class="info-list__field col-md-6">Real Balance</span>
                                    <span class="info-list__field info-list__field_type_value col-md-6"> <span class="val_type_user_balance fn-replacer js-replacer js-replacer-inline withdrawable-balance-replacer">$ {{number_format((float)(Auth::user()->balance - Auth::user()->bonus), 2, '.', '')}}</span></span>
                                    </li>
                            </ul>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
