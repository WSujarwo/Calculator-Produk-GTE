<aside class="sidebar fixed top-0 left-0 z-50">
    <!-- Sidebar header -->
    <header class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="header-logo">
            <img src="{{ asset('img/MasterLogo.png') }}" alt="Logo">
        </a>

        <button class="toggler sidebar-toggler">
            <span class="material-symbols-rounded">chevron_left</span>
        </button>

        <button class="toggler menu-toggler">
            <span class="material-symbols-rounded">menu</span>
        </button>   
    </header>

    <nav class="sidebar-nav">

       <!-- Primary nav -->
<ul class="nav-list primary-nav">

{{-- Dashboard --}}
<li class="nav-item">
    <a href="{{ route('dashboard') }}"
       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <span class="nav-icon material-symbols-rounded">dashboard</span>
        <span class="nav-label">Dashboard</span>
    </a>
    <span class="nav-tooltip">Dashboard</span>
</li>

{{-- ADMIN ONLY --}}
@role('admin')

<li class="nav-item has-submenu 
    {{ request()->routeIs('calculation.*') ? 'open' : '' }}">

    <a href="#"
       class="nav-link submenu-toggle 
       {{ request()->routeIs('calculation.*') ? 'active' : '' }}">
       
        <span class="nav-icon material-symbols-rounded">calculate</span>
        <span class="nav-label">Calculation Product</span>
        <span class="submenu-arrow material-symbols-rounded">expand_more</span>
    </a>

    <ul class="submenu">
        <li>
            <a href="{{ route('calculation.rti') }}"
               class="{{ request()->routeIs('calculation.rti') ? 'active' : '' }}">
                RTI
            </a>
        </li>
        <li>
            <a href="{{ route('calculation.gpp') }}"
               class="{{ request()->routeIs('calculation.gpp') ? 'active' : '' }}">
                GPP
            </a>
        </li>
        <li>
            <a href="{{ route('calculation.ejm') }}"
               class="{{ request()->routeIs('calculation.ejm') ? 'active' : '' }}">
                EJM
            </a>
        </li>
    </ul>

    <li class="nav-item has-submenu 
    {{ request()->routeIs('extractor.*') ? 'open' : '' }}">

    <a href="#"
       class="nav-link submenu-toggle 
       {{ request()->routeIs('extractor.*') ? 'active' : '' }}">
       
       <span class="nav-icon material-symbols-rounded">inventory_2</span>
       <span class="nav-label">Extractor Product</span>
        <span class="submenu-arrow material-symbols-rounded">expand_more</span>
    </a>

    <ul class="submenu">
        <li>
            <a href="{{ route('extractor.rti') }}"
               class="{{ request()->routeIs('extractor.rti') ? 'active' : '' }}">
                RTI
            </a>
        </li>
        <li>
            <a href="{{ route('extractor.gpp') }}"
               class="{{ request()->routeIs('extractor.gpp') ? 'active' : '' }}">
                GPP
            </a>
        </li>
        <li>
            <a href="{{ route('extractor.ejm') }}"
               class="{{ request()->routeIs('extractor.ejm') ? 'active' : '' }}">
                EJM
            </a>
        </li>
    </ul>

    <li class="nav-item">
        
    <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="nav-icon material-symbols-rounded">storage</span>
            <span class="nav-label">Database Product</span>
        </a>
        <span class="nav-tooltip">Database Product</span>
    </li>

    <li class="nav-item">
        <a href="#" class="nav-link">
            <span class="nav-icon material-symbols-rounded">request_quote</span>
            <span class="nav-label">Quotation</span>
        </a>
        <span class="nav-tooltip">Quotation</span>
    </li>

    <li class="nav-item">
        <a href="#" class="nav-link">
            <span class="nav-icon material-symbols-rounded">list_alt</span>
            <span class="nav-label">Order List</span>
        </a>
        <span class="nav-tooltip">Order List</span>
    </li>

@endrole


{{-- LOGISTIK --}}
@role('logistik')
<li class="nav-item has-submenu 
    {{ request()->routeIs('extractor.*') ? 'open' : '' }}">

    <a href="#"
       class="nav-link submenu-toggle 
       {{ request()->routeIs('extractor.*') ? 'active' : '' }}">
       
       <span class="nav-icon material-symbols-rounded">inventory_2</span>
       <span class="nav-label">Extractor Product</span>
        <span class="submenu-arrow material-symbols-rounded">expand_more</span>
    </a>

    <ul class="submenu">
        <li>
            <a href="{{ route('extractor.rti') }}"
               class="{{ request()->routeIs('extractor.rti') ? 'active' : '' }}">
                RTI
            </a>
        </li>
        <li>
            <a href="{{ route('extractor.gpp') }}"
               class="{{ request()->routeIs('extractor.gpp') ? 'active' : '' }}">
                GPP
            </a>
        </li>
        <li>
            <a href="{{ route('extractor.ejm') }}"
               class="{{ request()->routeIs('extractor.ejm') ? 'active' : '' }}">
                EJM
            </a>
        </li>
    </ul>

    <li class="nav-item">
        <a href="#" class="nav-link">
            <span class="nav-icon material-symbols-rounded">storage</span>
            <span class="nav-label">Database Product</span>
        </a>
        <span class="nav-tooltip">Database Product</span>
    </li>

    
@endrole


{{-- PPC --}}
@role('ppc')
<li class="nav-item has-submenu 
    {{ request()->routeIs('extractor.*') ? 'open' : '' }}">

    <a href="#"
       class="nav-link submenu-toggle 
       {{ request()->routeIs('extractor.*') ? 'active' : '' }}">
       
       <span class="nav-icon material-symbols-rounded">inventory_2</span>
       <span class="nav-label">Extractor Product</span>
        <span class="submenu-arrow material-symbols-rounded">expand_more</span>
    </a>

    <ul class="submenu">
        <li>
            <a href="{{ route('extractor.rti') }}"
               class="{{ request()->routeIs('extractor.rti') ? 'active' : '' }}">
                RTI
            </a>
        </li>
        <li>
            <a href="{{ route('extractor.gpp') }}"
               class="{{ request()->routeIs('extractor.gpp') ? 'active' : '' }}">
                GPP
            </a>
        </li>
        <li>
            <a href="{{ route('extractor.ejm') }}"
               class="{{ request()->routeIs('extractor.ejm') ? 'active' : '' }}">
                EJM
            </a>
        </li>
    </ul>
    
    <li class="nav-item">
        <a href="#" class="nav-link">
            <span class="nav-icon material-symbols-rounded">storage</span>
            <span class="nav-label">Database Product</span>
        </a>
        <span class="nav-tooltip">Database Product</span>
    </li>
@endrole


{{-- ESTIMATOR --}}
@role('estimator')
   
<li class="nav-item has-submenu 
    {{ request()->routeIs('calculation.*') ? 'open' : '' }}">

    <a href="#"
       class="nav-link submenu-toggle 
       {{ request()->routeIs('calculation.*') ? 'active' : '' }}">
       
        <span class="nav-icon material-symbols-rounded">calculate</span>
        <span class="nav-label">Calculation Product</span>
        <span class="submenu-arrow material-symbols-rounded">expand_more</span>
    </a>

    <ul class="submenu">
        <li>
            <a href="{{ route('calculation.rti') }}"
               class="{{ request()->routeIs('calculation.rti') ? 'active' : '' }}">
                RTI
            </a>
        </li>
        <li>
            <a href="{{ route('calculation.gpp') }}"
               class="{{ request()->routeIs('calculation.gpp') ? 'active' : '' }}">
                GPP
            </a>
        </li>
        <li>
            <a href="{{ route('calculation.ejm') }}"
               class="{{ request()->routeIs('calculation.ejm') ? 'active' : '' }}">
                EJM
            </a>
        </li>
    </ul>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <span class="nav-icon material-symbols-rounded">storage</span>
            <span class="nav-label">Database Product</span>
        </a>
        <span class="nav-tooltip">Database Product</span>
    </li>

    <li class="nav-item">
        <a href="#" class="nav-link">
            <span class="nav-icon material-symbols-rounded">request_quote</span>
            <span class="nav-label">Quotation</span>
        </a>
        <span class="nav-tooltip">Quotation</span>
    </li>

    <li class="nav-item">
        <a href="#" class="nav-link">
            <span class="nav-icon material-symbols-rounded">list_alt</span>
            <span class="nav-label">Order List</span>
        </a>
        <span class="nav-tooltip">Order List</span>
    </li>


@endrole

</ul>

        <!-- Secondary nav -->
        <!-- <ul class="nav-list secondary-nav">
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <span class="nav-icon material-symbols-rounded">account_circle</span>
                    <span class="nav-label">{{ auth()->user()->name }}</span>
                </a>
                <span class="nav-tooltip">Profile</span>
            </li> -->

            <li class="nav-item secondary-nav">
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="nav-link w-full flex items-center gap-12">
                            <span class="nav-icon material-symbols-rounded">logout</span>
                            <span class="nav-label">Logout</span>
                        </button>
                    </form>
                </li>
        </ul>

    </nav>
</aside>