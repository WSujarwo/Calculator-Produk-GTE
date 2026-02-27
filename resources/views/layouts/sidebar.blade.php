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
        <li class="has-submenu {{ request()->routeIs('calculation.ejm') || request()->routeIs('pcelist') || request()->routeIs('pce-orderlist*') ? 'open' : '' }}">
            <a href="#"
               class="submenu-toggle {{ request()->routeIs('calculation.ejm') || request()->routeIs('pcelist') || request()->routeIs('pce-orderlist*') ? 'active' : '' }}">
                <span>EJM</span>
                <span class="submenu-arrow material-symbols-rounded">expand_more</span>
            </a>
            <ul class="submenu">
                    <li>
                        <a href="{{ route('pcelist') }}"
                           class="{{ request()->routeIs('pcelist') ? 'active' : '' }}">
                            List PCE Order
                        </a>
                    </li>
                <li>
                    <a href="{{ route('pce-orderlist.index') }}"
                       class="{{ request()->routeIs('pce-orderlist*') ? 'active' : '' }}">
                        List Order
                    </a>
                </li>
            </ul>
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
        <li class="has-submenu {{ request()->routeIs('extractor.ejm') || request()->routeIs('detailTube') || request()->routeIs('detailBellow') || request()->routeIs('detailCollar') || request()->routeIs('detailPipeEnd') || request()->routeIs('detailFlange') || request()->routeIs('detailEJM') ? 'open' : '' }}">
            <a href="#"
               class="submenu-toggle {{ request()->routeIs('extractor.ejm') || request()->routeIs('detailTube') || request()->routeIs('detailBellow') || request()->routeIs('detailCollar') || request()->routeIs('detailPipeEnd') || request()->routeIs('detailFlange') || request()->routeIs('detailEJM') ? 'active' : '' }}">
                <span>EJM</span>
                <span class="submenu-arrow material-symbols-rounded">expand_more</span>
            </a>
            <ul class="submenu">
                <li>
                    <a href="#"
                        class="{{ request()->routeIs('detailTube') ? 'active' : '' }}">
                        Detail Tube 
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="{{ request()->routeIs('detailbellows') ? 'active' : '' }}">
                        Detail Bellows 
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="{{ request()->routeIs('detailCollar') ? 'active' : '' }}">
                        Detail Collar 
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="{{ request()->routeIs('detailMetalBellows') ? 'active' : '' }}">
                        Detail Metal Bellows  
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="{{ request()->routeIs('detailPipeEnd') ? 'active' : '' }}">
                        Detail Pipe End  
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="{{ request()->routeIs('detailFlange') ? 'active' : '' }}">
                        Detail Flange 
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="{{ request()->routeIs('detailEJM') ? 'active' : '' }}">
                        Detail EJM 
                    </a>
                </li>
            </ul>
        </li>
    </ul>

    <li class="nav-item">
        
        <li class="nav-item has-submenu 
        {{ request()->routeIs(
            'master.products.*',
            'master.shapes.*',
            'master.product-shapes.*',
            'master.type-configs.*',
            'master.cost-products.*',
            'master.materials.*',
        ) ? 'open' : '' }}">

        <a href="#"
        class="nav-link submenu-toggle 
        {{ request()->routeIs(
                'master.products.*',
                'master.shapes.*',
                'master.product-shapes.*',
                'master.type-configs.*',
                'master.cost-products.*',
                'master.materials.*'
        ) ? 'active' : '' }}">
        
        <span class="nav-icon material-symbols-rounded">storage</span>
        <span class="nav-label">Database</span>
        <span class="submenu-arrow material-symbols-rounded">expand_more</span>
        </a>

        <ul class="submenu">

            <li>
                <a href="{{ route('master.products.index') }}"
                class="{{ request()->routeIs('master.products.*') ? 'active' : '' }}">
                    Products
                </a>
            </li>

            <li>
                <a href="{{ route('master.shapes.index') }}"
                class="{{ request()->routeIs('master.shapes.*') ? 'active' : '' }}">
                    Shapes
                </a>
            </li>

            <li>
                <a href="{{ route('master.product-shapes.index') }}"
                class="{{ request()->routeIs('master.product-shapes.*') ? 'active' : '' }}">
                    Product ↔ Shape
                </a>
            </li>

            <li>
                <a href="{{ route('master.type-configs.index') }}"
                class="{{ request()->routeIs('master.type-configs.*') ? 'active' : '' }}">
                    Type / Configuration
                </a>
            </li>

            <li>
                <a href="{{ route('master.cost-products.index') }}"
                class="{{ request()->routeIs('master.cost-products.*') ? 'active' : '' }}">
                    Cost Product
                </a>
            </li>

            <li>
                <a href="{{ route('master.materials.index') }}"
                class="{{ request()->routeIs('master.materials.*') ? 'active' : '' }}">
                    Materials
                </a>
            </li>

        </ul>
    </li>

    @can('quotations.view')
    <li class="nav-item">
        <a href="{{ route('quotations.index') }}"
           class="nav-link {{ request()->routeIs('quotations.*') ? 'active' : '' }}">
            <span class="nav-icon material-symbols-rounded">request_quote</span>
            <span class="nav-label">Quotation</span>
        </a>
        <span class="nav-tooltip">Quotation</span>
    </li>
    @endcan

    <li class="nav-item">
        <a href="#" class="nav-link">
            <span class="nav-icon material-symbols-rounded">list_alt</span>
            <span class="nav-label">Order List</span>
        </a>
        <span class="nav-tooltip">Order List</span>
    </li>

    <li class="nav-item">
        <a href="{{ route('setting') }}"
        class="nav-link {{ request()->routeIs('setting*') ? 'active' : '' }}">
            <span class="nav-icon material-symbols-rounded">admin_panel_settings</span>
            <span class="nav-label">Setting</span>
        </a>
        <span class="nav-tooltip">Setting</span>
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
        <li class="has-submenu {{ request()->routeIs('calculation.ejm') || request()->routeIs('pcelist') || request()->routeIs('pce-orderlist*') ? 'open' : '' }}">
            <a href="#"
               class="submenu-toggle {{ request()->routeIs('calculation.ejm') || request()->routeIs('pcelist') || request()->routeIs('pce-orderlist*') ? 'active' : '' }}">
                <span>EJM</span>
                <span class="submenu-arrow material-symbols-rounded">expand_more</span>
            </a>
            <ul class="submenu">
                    <li>
                        <a href="{{ route('pcelist') }}"
                           class="{{ request()->routeIs('pcelist') ? 'active' : '' }}">
                            List PCE Order
                        </a>
                    </li>
                <li>
                    <a href="{{ route('pce-orderlist.index') }}"
                       class="{{ request()->routeIs('pce-orderlist*') ? 'active' : '' }}">
                        List Order
                    </a>
                </li>
            </ul>
        </li>
    </ul>
    
    <li class="nav-item">
        
        <li class="nav-item has-submenu 
        {{ request()->routeIs(
            'master.products.*',
            'master.shapes.*',
            'master.product-shapes.*',
            'master.type-configs.*',
            'master.cost-products.*',
            'master.materials.*'
        ) ? 'open' : '' }}">

        <a href="#"
        class="nav-link submenu-toggle 
        {{ request()->routeIs(
                'master.products.*',
                'master.shapes.*',
                'master.product-shapes.*',
                'master.type-configs.*',
                'master.cost-products.*',
                'master.materials.*'
        ) ? 'active' : '' }}">
        
        <span class="nav-icon material-symbols-rounded">storage</span>
        <span class="nav-label">Database Product</span>
        <span class="submenu-arrow material-symbols-rounded">expand_more</span>
        </a>

        <ul class="submenu">

            <li>
                <a href="{{ route('master.products.index') }}"
                class="{{ request()->routeIs('master.products.*') ? 'active' : '' }}">
                    Products
                </a>
            </li>

            <li>
                <a href="{{ route('master.shapes.index') }}"
                class="{{ request()->routeIs('master.shapes.*') ? 'active' : '' }}">
                    Shapes
                </a>
            </li>

            <li>
                <a href="{{ route('master.product-shapes.index') }}"
                class="{{ request()->routeIs('master.product-shapes.*') ? 'active' : '' }}">
                    Product ↔ Shape
                </a>
            </li>

            <li>
                <a href="{{ route('master.type-configs.index') }}"
                class="{{ request()->routeIs('master.type-configs.*') ? 'active' : '' }}">
                    Type / Configuration
                </a>
            </li>

            <li>
                <a href="{{ route('master.cost-products.index') }}"
                class="{{ request()->routeIs('master.cost-products.*') ? 'active' : '' }}">
                    Cost Product
                </a>
            </li>

            <li>
                <a href="{{ route('master.materials.index') }}"
                class="{{ request()->routeIs('master.materials.*') ? 'active' : '' }}">
                    Materials
                </a>
            </li>

        </ul>
    </li>

    @can('quotations.view')
    <li class="nav-item">
        <a href="{{ route('quotations.index') }}"
           class="nav-link {{ request()->routeIs('quotations.*') ? 'active' : '' }}">
            <span class="nav-icon material-symbols-rounded">request_quote</span>
            <span class="nav-label">Quotation</span>
        </a>
        <span class="nav-tooltip">Quotation</span>
    </li>
    @endcan

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
