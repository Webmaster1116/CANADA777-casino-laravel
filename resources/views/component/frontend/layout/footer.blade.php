<footer id="main_footer">
    <div class="footer-service">
        <ul> 
            <li>
                <a href="#">
                    <img data-original="https://canada777.com/frontend/Default/img/footer-services/info-icons__img__1.png" alt="">
                    <span>1000+ Games</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <img data-original="https://canada777.com/frontend/Default/img/footer-services/info-icons__img__2.png" alt="">
                    <span>Fast Payments</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <img data-original="https://canada777.com/frontend/Default/img/footer-services/info-icons__img__3.png" alt="">
                    <span>24/7 Support</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <img data-original="https://canada777.com/frontend/Default/img/footer-services/info-icons__img__4.png" alt="">
                    <span>Only Licensed Games</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <img data-original="https://canada777.com/frontend/Default/img/footer-services/info-icons__img__5.png" alt="">
                    <span>Multi-Currency</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <img data-original="https://canada777.com/frontend/Default/img/footer-services/info-icons__img__6.png" alt="">
                    <span>100% Secure</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <img data-original="https://canada777.com/frontend/Default/img/footer-services/info-icons__img__7.png" alt="">
                    <span>Awesome Promos</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <img data-original="https://canada777.com/frontend/Default/img/footer-services/info-icons__img__8.png" alt="">
                    <span>Responsible Gambling Limits</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="footer-content">
        <div class="footer-banner">
            @if(!Auth::check())
                <a href="#signup-modal">
                    <img src="https://canada777.com/frontend/Default/img/footer-banner.jpg" alt="" />
                </a>
            @else
                <img src="https://canada777.com/frontend/Default/img/footer-banner.jpg" alt="" />
            @endif
        </div>
        <div class="footer-support">
            <div class="payment">
                <ul>
                    <li>
                        <a href="#">
                            <img data-original="https://canada777.com/frontend/Default/img/footer-logos/interac.png" alt="" />
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <img data-original="https://canada777.com/frontend/Default/img/footer-logos/skrill.png" alt="" />
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <img data-original="https://canada777.com/frontend/Default/img/footer-logos/visa.png" alt="" />
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <img data-original="https://canada777.com/frontend/Default/img/footer-logos/maestro.png" alt="" />
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <img data-original="https://canada777.com/frontend/Default/img/footer-logos/mastercard.png" alt="" />
                        </a>
                    </li>
                </ul>
            </div>
            <div class="company">
                <ul>
                    <li>
                        <a href="#">
                            <img data-original="https://canada777.com/frontend/Default/img/footer-logos/pragmatic.png" alt="" />
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <img data-original="https://canada777.com/frontend/Default/img/footer-logos/netent.png" alt="" />
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <img data-original="https://canada777.com/frontend/Default/img/footer-logos/playtech.png" alt="" />
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <img data-original="https://canada777.com/frontend/Default/img/footer-logos/isoftbet.png" alt="" />
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <img data-original="https://canada777.com/frontend/Default/img/footer-logos/wazdan.png" alt="" />
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <img data-original="https://canada777.com/frontend/Default/img/footer-logos/amatic.png" alt="" />
                        </a>
                    </li>
                </ul>
            </div>
            <div class="support">
                <ul>
                    <li>
                        <a href="#">
                            <img data-original="https://canada777.com/frontend/Default/img/footer-logos/18plus.png" alt="" />
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <img data-original="https://canada777.com/frontend/Default/img/footer-logos/gambling_therapy.png" alt="" />
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <img data-original="https://canada777.com/frontend/Default/img/footer-logos/gamblers_anonymous.png" alt="" />
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <img data-original="https://canada777.com/frontend/Default/img/footer-logos/gamanon.png" alt="" />
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <img data-original="https://canada777.com/frontend/Default/img/footer-logos/gamcare.png" alt="" />
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="footer-game-categories">
            <img src="https://canada777.com/frontend/Default/img/footer-categories.png" alt="" />
        </div>
    </div>
    <div class="footer-menu">
        <div class="footer-menu-content d-none d-lg-flex">
            <div class="quick-menu footer-menu-section">
                <h5 class="footer-menu-header-text">Quick</h5>
                <ul>
                    <li><a href="{{url('categories/all')}}">All Games</a></li>
                    <li><a href="{{url('about')}}">About Us</a></li>
                    <li><a href="javascript:fn_profile_load('deposit')">Payments</a></li>
                    <li><a href="{{url('bonus')}}">Promotions</a></li>
                    <li><a href="{{url('bonus/term')}}">Games Rules</a></li>
                    <li><a href="{{url('bonus/term')}}">Games Max Limits</a></li>
                    <li><a href="#">Affiliates</a></li>
                </ul>
            </div>
            <div class="quick-menu footer-menu-section">
                <h5 class="footer-menu-header-text">Info</h5>
                <ul>
                    <li><a href="{{url('bonus/term')}}">Registration Procedure</a></li>
                    <li><a href="{{url('bonus/term')}}">Terms and Conditions</a></li>
                    <li><a href="{{url('bonus/term')}}">Bonus Terms</a></li>
                    <li><a href="https://www.privacypolicygenerator.info/live.php?token=5IRscQHRpBtL0PaYXfxiT1RP2t8YTrC4">Privacy Policy</a></li>
                    <li><a href="{{url('blog/responsible-gaming')}}">Responsible Gaming</a></li>
                    <li><a href="{{url('categories/all')}}">Complaints</a></li>
                </ul>
            </div>
            <div class="quick-menu footer-menu-section">
                <h5 class="footer-menu-header-text">Games</h5>
                <ul>
                    <li><a href="{{url('categories/all')}}">Slots</a></li>
                    <li><a href="{{url('categories/all')}}">Live</a></li>
                    <li><a href="{{url('categories/card')}}">Card</a></li>
                    <li><a href="{{url('categories/card')}}">Roulette</a></li>
                    <li><a href="{{url('categories/all')}}">Jackpot</a></li>
                    <li><a href="{{url('categories/new')}}">New</a></li>
                    <li><a href="{{url('bonus')}}">Bonus Buy</a></li>
                </ul>
            </div>
            <div class="quick-menu footer-menu-section">
                <h5 class="footer-menu-header-text">Contact</h5>
                <ul>
                    <li><a href="#">Support</a></li>
                    <li><a href="#">Affiliates</a></li>
                </ul>
                <div class="footer-menu-support d-none d-xl-block">
                    <ul>
                        <li>
                            <a href="#">
                                <img data-original="https://canada777.com/frontend/Default/img/footer-logos/18plus.png" alt="" />
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <img data-original="https://canada777.com/frontend/Default/img/footer-logos/gambling_therapy.png" alt="" />
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <img data-original="https://canada777.com/frontend/Default/img/footer-logos/gamblers_anonymous.png" alt="" />
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <img data-original="https://canada777.com/frontend/Default/img/footer-logos/gamanon.png" alt="" />
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <img data-original="https://canada777.com/frontend/Default/img/footer-logos/gamcare.png" alt="" />
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-mobile-section d-block d-lg-none">
            <ul>
                <li><a href="#">All Games</a></li>
                <li><a href="#">Bonus Terms</a></li>
                <li><a href="#">Terms and Conditions</a></li>
                <li><a href="#">About Us</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-logo">
        <div class="footer-logo-content">
            <div class="footer-logo-item">
                <img data-original="https://canada777.com/frontend/Default/img/footer-logos/curacaoegaming.png" alt="" />
                <p>
                    Canada777.com is operated by Gammix STS B.V. a company registered under the laws of Curacao, with company registration number 125471 and registered address at Fransche Bloemweg 4, Willemstad, Curaçaoa company incorporated under the laws of Curaçao.
                </p>
            </div>
            <div class="footer-logo-item">
                <img data-original="https://canada777.com/frontend/Default/img/footer-logos/over-18.png" alt="" />
                <p>
                    Only players above the age of 18 and who reside in countries where gambling is legal are allowed to play on Canada777.com
                </p>
            </div>
        </div>
    </div>

    <div class='lepopup-outline' data-slug='popup-welcome-bonus*popup-welcome-bonus-mobile'></div>
    <input type="hidden" id="auth_status" name="auth_status" value={{Auth::check()}} />
</footer>
