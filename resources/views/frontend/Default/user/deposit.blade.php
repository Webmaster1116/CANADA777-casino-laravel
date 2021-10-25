@extends('frontend.Default.user.profile')
@section('content')
    <div class="navigation-sibling">
        <div class="layout-column layout-column-1">
            <div class="portlet-dropzone portlet-column-content fn-portlet-container column-1">
                <div class="portlet portlet_name_56 portlet-wrapper fn-portlet-wrapper portlet-boundary portlet-boundary_56_INSTANCE_8jhHr00eAgvG_ portlet-56 portlet_type_border">
                    <div class="portlet-title fn-portlet-title">
                        <span class="fn-portlet-title-region portlet-title__text-wrapper"><span class="portlet-title-text">Deposit</span></span>
                    </div>
                    <div class="fn-portlet portlet__content portlet__content_border_show portlet__content_type_56 ">
                        <article data-web-content-id="Deposit">
                            <p>
                            <div class="deposit-content-body">

                                <div class="col-sm-5 modal-content-body-card-list">
                                    <div class="row">
                                        <label for="currency" class="mb-2 mt-4 text-dark">Currency</label>
                                        <div class="row add-currency">
                                            <select class="mb-4 custom-control modal-content-deposit_currency" id="deposit_currency" name="deposit_currency" placeholder="Currency" onchange="fn_change_currency()">
                                                @if(isset($currencys) && count($currencys))
                                                    @foreach($currencys as $currency)
                                                        <option value="{{$currency->id}}">{{$currency->currency}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <!-- <button type="button" class="btn btn-secondary modal-content-deposit_currency-add" onclick="fn_add_currency_type()"><i class="fa fa-plus"></i></button> -->
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="mb-2 text-dark">All Payment Methods</label>
                                        <!-- <div class="btn-group-vertical w-100 payment-button-group">
                                                <button type="button" class="btn btn-outline-success mb-1 col-sm-6 rounded-0 payment-method-button-element payment-method-button-element-selected" onclick="fn_payment_method_select(this)">
                                                    <img src="{{asset('frontend/Default/img/interac.png')}}" alt="">
                                                </button>
                                            </div> -->
                                            <div class="btn-group-vertical w-100 payment-button-group">
                                                <div class="payment-pair">
                                                <button type="button" class="btn btn-outline-success col-sm-6 rounded-0 payment-method-button-element payment-method-button-element-selected" onclick="fn_payment_method_select(this, 'interac')">
                                                    <img src="{{asset('frontend/Default/img/interac.png')}}" alt="" />
                                                </button>
                                                <button type="button" id="crypto_payment_btc" class="btn btn-outline-success col-sm-6 rounded-0 payment-method-button-element payment-method-button payment-crypto-currency" onclick="fn_payment_method_select(this, 'crypto', 'BTC')">
                                                    <img src="{{asset('frontend/Default/img/bitcoin.png')}}" alt="" /><span>Bitcoin</span>
                                                </button>
                                                </div>
                                                <div class="payment-pair">
                                                <button type="button" id="crypto_payment_ltc" class="btn btn-outline-success col-sm-6 rounded-0 payment-method-button-element payment-method-button payment-crypto-currency" onclick="fn_payment_method_select(this, 'crypto', 'BCH')">
                                                    <img src="{{asset('frontend/Default/img/bitcoin-cash.png')}}" alt="" /><span>BitcoinCash</span>
                                                </button>
                                                <button type="button" id="crypto_payment_bch" class="btn btn-outline-success col-sm-6 rounded-0 payment-method-button-element payment-method-button payment-crypto-currency" onclick="fn_payment_method_select(this, 'crypto', 'LTC')">
                                                <img src="{{asset('frontend/Default/img/litecoin.png')}}" alt="" /><span>LiteCoin</span>
                                                </button>
                                                </div>
                                                <div class="payment-pair">
                                                <button type="button" id="crypto_payment_eth" class="btn btn-outline-success col-sm-6 rounded-0 payment-method-button-element payment-method-button payment-crypto-currency" onclick="fn_payment_method_select(this, 'crypto', 'ETH')">
                                                <img src="{{asset('frontend/Default/img/ethereum.png')}}" alt="" /><span>Ethereum</span>
                                                </button>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                                <div class="col-sm-7 deposit-interac-content">
                                    <form id="deposit-form" class="modal-form container col-sm-7" action="{{route('frontend.deposit.payment')}}" method="GET" target="_blank">
                                        <input type="hidden" name="payment_type" id="payment_type" value="1"/>
                                        <input type="hidden" name="payment_method" id="payment_method" value="interac">
                                        <div class="row deposit-content-style">
                                            <div class="col-sm-12 payment-interac-style">
                                                <span class="mb-2 text-dark interac-text">Interac</span><br />
                                                <span class="mb-1 text-dark">Amount</span>
                                                <div class="btn-group w-100 mb-4 modal-content-deposit-amount-button-group">
                                                    <button type="button" class="btn btn-success m-1 rounded-0 modal-content-deposit-amount" onclick="fn_price(25, this)">25 $</button>
                                                    <button type="button" class="btn btn-success m-1 rounded-0 modal-content-deposit-amount" onclick="fn_price(75, this)">75 $</button>
                                                    <button type="button" class="btn btn-success m-1 rounded-0 modal-content-deposit-amount" onclick="fn_price(125, this)">125 $</button>
                                                    <button type="button" class="btn btn-success m-1 rounded-0 modal-content-deposit-amount" onclick="fn_price(250, this)">250 $</button>
                                                    <button type="button" class="btn btn-success m-1 rounded-0 modal-content-deposit-amount" onclick="fn_price(500, this)">500 $</button>
                                                </div>
{{--                                                <div class="info-body alert alert-warning alert-dismissible fade show">--}}
{{--                                                    <!-- <button type="button" class="close" data-dismiss="alert" onclick="fn_alert_close()">&times;</button> -->--}}
{{--                                                    <div class="info-content">{{ $user->max_deposit }}</div>--}}
{{--                                                </div>--}}
                                                <div class="form-group m-0">
                                                    <!-- <label class="mb-2 text-dark" for="deposit_amount">Amount</label> -->
                                                    <div class="d-flex align-items-center mb-1 deposit-amount-wrap">
                                                        <input type="number" id="deposit_amount" name="deposit_amount" class="border-0 m-0 p-0" oninput="fn_amount_input()" />
                                                        <span id="deposit_currency"></span>
                                                    </div>
                                                    <div class="mb-1 text-dark modal-content-deposit-amount-limit">Instantly, min 20, max 3000, Payment CAD</div>
                                                </div>

                                                {{-- @if($no_bonus == "0") --}}
                                                <div class="custom-control custom-checkbox mb-4 any-bonus-style">
                                                    <input type="checkbox" class="custom-control-input" id="customCheck1">
                                                    <label class="custom-control-label text-dark p-1" for="customCheck1">I don't want to receive any bonus</label>
                                                </div>
                                                {{-- @else
                                                    <div class="info-body alert alert-danger alert-dismissible fade show">
                                                        <!-- <button type="button" class="close" data-dismiss="alert" onclick="fn_alert_close()">&times;</button> -->
                                                        <div class="info-content">@lang('app.no_bonus')</div>
                                                    </div>
                                                @endif --}}
                                                <div class="error-body alert alert-danger alert-dismissible fade show">
                                                    <button type="button" class="close" data-dismiss="alert" onclick="fn_alert_close()">&times;</button>
                                                    <strong>Wrong!</strong> <div class="error-content"></div>
                                                </div>
                                                <div class="form-group m-0">
                                                    <label class="form-group text-dark p-1 m-0" for="deposit_email">Email</label>
                                                    <input type="text" id="deposit_email" name="deposit_email" />
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-group text-dark p-1 m-0" for="deposit_phone">Phone</label>
                                                    <input type="text" id="deposit_phone" name="deposit_phone" />
                                                </div>
                                                <input type="hidden" name="cur_deposit_currency" id="cur_deposit_currency"/>
                                                <button type="button" class="btn btn-warning btn-block mb-2 rounded-20 modal-content-deposit-button" onclick="fn_deposit_submit()">Deposit</button>
                                                <button type="button" class="btn btn-warning btn-block rounded-20 modal-content-deposit-button" onclick="fn_deposit_close()">Close</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-sm-7 deposit-crypto-content">
                                    <div class="row deposit-content-style">
                                        <!-- cryptoprocessing payment -->
                                        <div class="col-sm-12 payment-crypto-style">
                                            <span class="mb-2 text-dark interac-text">Crypto</span><br />

                                            <span class="mb-1 text-dark">Amount</span>
                                            <div class="btn-group w-100 mb-4 modal-content-deposit-amount-button-group">
                                                <button type="button" class="btn btn-success m-1 rounded-0 modal-content-deposit-amount modal-selected-deposit-currency" onclick="fn_crypto_price(25, this)">25 $</button>
                                                <button type="button" class="btn btn-success m-1 rounded-0 modal-content-deposit-amount" onclick="fn_crypto_price(75, this)">75 $</button>
                                                <button type="button" class="btn btn-success m-1 rounded-0 modal-content-deposit-amount" onclick="fn_crypto_price(125, this)">125 $</button>
                                                <button type="button" class="btn btn-success m-1 rounded-0 modal-content-deposit-amount" onclick="fn_crypto_price(250, this)">250 $</button>
                                                <button type="button" class="btn btn-success m-1 rounded-0 modal-content-deposit-amount" onclick="fn_crypto_price(500, this)">500 $</button>
                                            </div>
                                            <div class="form-group m-0">
                                                <!-- <label class="mb-2 text-dark" for="deposit_amount">Amount</label> -->
                                                <div class="info-body info-body-min-deposit alert alert-danger alert-dismissible" style="display:none">
                                                    <!-- <button type="button" class="close" data-dismiss="alert" onclick="fn_alert_close()">&times;</button> -->
                                                    <div class="info-content">@lang('app.min_deposit')</div>
                                                </div>
                                                <div class="d-flex align-items-center mb-1 deposit-amount-wrap crypto-deposit-amount-wrap">
                                                    <input type="number" id="crypto_deposit_amount" name="crypto_deposit_amount" class="border-0 m-0 p-0" oninput="fn_crypto_amount_input(this.value)" />
                                                    <span id="crypto_deposit_currency"></span>
                                                </div>
                                                <div class="mb-1 text-dark modal-content-deposit-amount-limit">Instantly, min 20, max 3000, Payment CAD</div>
                                            </div>

                                            <div class="crypto-currency-item mb-1">
                                            </div>

                                            {{-- @if($no_bonus == "0") --}}
                                                <div class="custom-control custom-checkbox mb-4 any-bonus-style">
                                                    <input type="checkbox" class="custom-control-input" id="customCheck2">
                                                    <label class="custom-control-label text-dark p-1" for="customCheck2">I don't want to receive any bonus</label>
                                                </div>
                                            {{-- @else
                                                <div class="info-body alert alert-danger alert-dismissible fade show">
                                                    <!-- <button type="button" class="close" data-dismiss="alert" onclick="fn_alert_close()">&times;</button> -->
                                                    <div class="info-content">@lang('app.no_bonus')</div>
                                                </div>
                                            @endif --}}
                                            <div class="crypto-address-link">
                                            </div>

                                            <div class="custom-control crypto-address mb-4">
                                                <!-- <label class="form-group text-dark p-1 m-0" for="crypto_address">Crypto Address</label> -->
                                                <input type="hidden" id="crypto_address" name="crypto_address" value="" readonly/>
                                            </div>

                                            <input type="hidden" id="sel_currency" name="sel_currency" val="" />
                                            <input type="hidden" name="currency_to" id="currency_to"/>
                                            <button type="button" class="btn btn-warning btn-block rounded-20 modal-content-deposit-button" onclick="fn_deposit_close()">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </p>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
