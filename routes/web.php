<?php
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\Categories;
use App\Livewire\Products;
use App\Livewire\Customers;
use App\Livewire\Pos;
use App\Livewire\SalesIndex;
use App\Livewire\SuppliersIndex;
use App\Livewire\PurchasesIndex;
use App\Livewire\PurchaseCreate;
use App\Livewire\SalesCreate;
use App\Livewire\InventoryKardex;
use App\Livewire\Users;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', Login::class)->name('login')->middleware('guest');
Route::post('/logout', Login::class)->name('logout');

//Rutas Autenticadas General (Accesible por cualquier usuario logueado)
Route::middleware(['auth'])->group(function (){
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
});

//RUTAS EXCLUSIVAS DE ADMINISTRADOR 
Route::middleware(['auth', 'role:Administrador'])->group(function (){
  Route::get('/users', Users::class)->name('users');
});

//RUTAS PARA ADMINISTRADOR Y ALMACENISTA(Inventario, Compras y Proveedores)
Route::middleware(['auth', 'role:Administrador|Almacenista'])->group(function (){
    Route::get('/categorias', Categories::class)->name('categorias');
    Route::get('/products', Products::class)->name('productos');
    Route::get('/Suppliers', SuppliersIndex::class)->name('Suppliers');
    Route::get('/purchases', PurchasesIndex::class)->name('purchases');
    Route::get('/purchases/create', PurchaseCreate::class)->name('purchases.create');
    Route::get('/Kardex', InventoryKardex::class)->name('Kardex');
});

//RUTAS PARA ADMINISTRADOR Y CAJERO (VENTAS, Punto de venta y clientes)
Route::middleware(['auth', 'role:Administrador|Cajero'])->group(function (){
    Route::get('/customers', Customers::class)->name('clientes');
    Route::get('/pos', Pos::class)->name('pos');
    Route::get('/sales', SalesIndex::class)->name('sales');
    Route::get('/sales/create', salesCreate::class)->name('sales.create');
});

Route::middleware(['auth'])->group(function () {
    // Generación de PDFs
    Route::get('/reports/sale-invoice/{id}', [ReportController::class, 'saleInvoice'])->name('reports.sale-invoice');
    Route::get('/reports/purchase-receipt/{id}', [ReportController::class, 'purchaseReceipt'])->name('reports.purchase-receipt');
    Route::get('/reports/kardex-pdf', [ReportController::class, 'kardexReport'])->name('reports.kardex-pdf');
    Route::get('/reports/inventory-pdf', [ReportController::class, 'inventoryReport'])->name('reports.inventory-pdf');
});
