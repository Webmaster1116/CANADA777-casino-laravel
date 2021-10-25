<style>
.slick-track, .slick-slide{ width:100% !important;}
#menu-outer {
	height: 84px;  
	width: 100%;
}

.tablemenu {
	width:100%;
	display: table;   /* Allow the centering to work */
	margin: 0 auto;
}

ul#horizontal-list {
	min-width: 800px;
	overflow-x:auto; 
width:100%;
	list-style: none; 
	    display: table;
    table-layout: fixed; /* the magic dust that ensures equal width */
 
	}
	ul#horizontal-list li {
		display: table-cell; 
    text-align: center;
     padding:5px;
	}
#horizontal-list .menulist a{
    text-decoration: none;
    width:100% !important;
     padding:5px;
}


#horizontal-list img{
    text-align: center;
    width:50px;
    height:50px;
	}
.mt-1{
  text-align: center;  
  color:white;
  text-decoration:none;
}
</style>

<section id="category-section">
    
    <div id="menu-outer " class="top-category-list d-none d-md-block">
  <div class="tablemenu d-md-block">
    <ul id="horizontal-list">
      
      
@if( settings('use_all_categories') )
		<li class="menulist ">
            <a href="{{ route('frontend.game.list.category', 'all') }}" class="@if($currentSliderNum != -1 && $currentSliderNum == 'all') active @endif">
                <img src="https://static.canada777.com/frontend/Page/image/icon/Game lobby.png" alt="">
                <span class="mt-1">@lang('app.all')</span>
            </a>
          </li>
        @endif


@if ($categories)
            @foreach($categories as $category)
                @if($category->position < 6 &&  $category->icon)
                    @if($category->href == 'table')
                        @if(@current_shop_id == $category->shop_id)
                        <li class="menulist">
                        <a href="{{ route('frontend.game.list.category', $category->href) }}" class="@if($currentSliderNum != -1 && $category->href == $currentSliderNum) active @endif">
                            <img src="https://static.canada777.com/frontend/Page/image/icon/{{$category->icon}}" alt="">
                            <span class="mt-1">
                        @if($category->title == 'Live Casino')
                               @php
                               $live_category ='Live'
                               @endphp
                               {{ $live_category }}
                          @else
                                {{ $category->title }}
                          @endif 
                            </span>
                        </a>
                    </li>
                        @endif
                    @else
                   <li class="menulist"> 
                    <a href="{{ route('frontend.game.list.category', $category->href) }}" class="@if($currentSliderNum != -1 && $category->href == $currentSliderNum) active @endif">
                        <img src="https://static.canada777.com/frontend/Page/image/icon/{{$category->icon}}" alt="">
                        <span class="mt-1">
                          @if($category->title == 'Live Casino')
                               @php
                               $live_category ='Live'
                               @endphp
                               {{ $live_category }}
                          @else
                                {{ $category->title }}
                          @endif  
                        </span>
                    </a>
                </li>
                    @endif
                @endif
            @endforeach
        @endif

    </ul>
  </div>
</div>
    <div class="mobile-top-category d-block d-md-none">
        <div class="mobile-top-category-list">
           <table border="0">
               <tr>
                   @if( settings('use_all_categories') )
                <td><a href="{{ route('frontend.game.list.category', 'all') }}" class="ml-1 mr-1 pr-1 @if($currentSliderNum != -1 && $currentSliderNum == 'all') active @endif">@lang('app.all')</a>
           </td>
            @endif
                        @if ($categories)
                @foreach($categories as $category)
                    @if($category->position < 6)
                        @if($category->href == 'table')
                            @if(@current_shop_id == $category->shop_id)
                            <td><a href="{{ route('frontend.game.list.category', $category->href) }}" class="ml-1 mr-1 p-1 @if($currentSliderNum != -1 && $category->href == $currentSliderNum) active @endif">
                         @if($category->title == 'Live Casino')
                               @php
                               $live_category ='Live'
                               @endphp
                               {{ $live_category }}
                          @else
                                {{ $category->title }}
                          @endif 
                            </a>
                           </td>
                            @endif
                        @else
                        <td><a href="{{ route('frontend.game.list.category', $category->href) }}" class="ml-1 mr-1 p-1 @if($currentSliderNum != -1 && $category->href == $currentSliderNum) active @endif">
                          @if($category->title == 'Live Casino')
                               @php
                               $live_category ='Live'
                               @endphp
                               {{ $live_category }}
                          @else
                                {{ $category->title }}
                          @endif 
                            </a>
                        </td>
                        @endif
                    @endif
                @endforeach
            @endif
               </tr>
           </table>
            

        </div>
    </div>
    <div class="mobile-top-category-list-search">
        <a href="#search-modal" class="search-box">
            <svg
                xmlns='http://www.w3.org/2000/svg'
                width='14'
                height='14'
                viewBox='0 0 14 14'
            >
                <path
                    data-name='_ionicons_svg_ios-search (5)'
                    d='M77.845,76.9l-3.9-3.932a5.553,5.553,0,1,0-.843.854l3.871,3.906a.6.6,0,0,0,.846.022A.6.6,0,0,0,77.845,76.9Zm-8.26-3.031a4.384,4.384,0,1,1,3.1-1.284A4.358,4.358,0,0,1,69.586,73.865Z'
                    transform='translate(-64 -63.9)'
                    fill='currentColor'
                />
            </svg>
            <span>Find Game</span>
        </a>
        <div class="category-toggle-button dropdown">
            <button class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">game provider</button>
            <div class="dropdown-menu dropdown-large">
                <ul>
                    @if($categories)
                        @foreach($categories as $category)
                            @if($category->href != "livecasino" && $category->href != "jackpot" && $category->href != "table" && $category->href != "pragmatic_play" && $category->href != "pragmatic_play_live" && $category->href != "card" && $category->href != "card" && $category->href != "roulette" && $category->href != "ct_gaming")
                                <li>
                                    <a href="{{ route('frontend.game.list.category', $category->href) }}" class="@if($currentSliderNum != -1 && $category->href == $currentSliderNum) active @endif">
                                <span class="providers__dropdown-icon">
                                    <img class="providers__icon-img" src="https://static.canada777.com/frontend/Page/image/icon/{{strtolower($category->href)}}.svg">
                                </span>
                                        <span class="providers__dropdown-name">{{ $category->title }}</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
    </div>
</section>