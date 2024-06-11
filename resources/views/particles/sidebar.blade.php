<div class="quixnav">
    <div class="quixnav-scroll">
        <ul class="metismenu" id="menu">
            <li class="nav-label first">Main Menu</li>
            <!-- <li><a href="index.html"><i class="icon icon-single-04"></i><span class="nav-text">Dashboard</span></a>
            </li> -->
            @if(auth()->guard(userGuard())->user()->can('dashboard view'))
            <li><a href="{{ routePut('app.dashboard') }}"><span class="nav-text">Dashboard</span></a></li>
            @endif
            <li><a class="has-arrow" href="javascript:void()" aria-expanded="false"><i
                        class="mdi mdi-account"></i><span class="nav-text">Users</span></a>
                <ul aria-expanded="false">
                    <li class=" {{ in_array(routeCurrName(),['subadmin.list','subadmin.create','subadmin.edit']) ? 'mm-active' : '' }}"><a href="{{ routePut('subadmin.list') }}">Sub Admin</a></li>
                    <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">Team</a>
                        <ul aria-expanded="false">
                            <li><a href="{{ routePut('teams.list') }}">Team List</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
           

            <li><a class="has-arrow" href="javascript:void()" aria-expanded="false"><i
                        class="mdi mdi-calendar"></i><span class="nav-text">Leads</span></a>
                <ul aria-expanded="false">
                    <li><a href="{{ routePut('leads.list') }}">List</a></li>
                    <li><a href="{{ routePut('leads.asign') }}">Assign Leads</a></li>
                </ul>
            </li>
            <li>
                <a href="{{ routePut('teams.view') }}">
                    <i class="fa fa-clipboard" style="font-size:14px">      
                    </i> 
                    Report
                </a>
            </li>
            <li><a class="has-arrow" href="javascript:void()" aria-expanded="false"><i
                        class="mdi mdi-book-open-variant"></i><span class="nav-text">Pages</span></a>
                <ul aria-expanded="false">
                    <li><a href="{{ routePut('pages.about-us') }}">About Us</a></li>
                    <li><a href="{{ routePut('pages.contact-us') }}">Contact Us</a></li>
                    <li><a href="{{ routePut('pages.privacy-policy') }}">Privacy Policy</a></li>
                    <li><a href="{{ routePut('pages.terms-condition') }}">Terms and Condition</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
