<?php

use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\TenantInvoiceController;
use App\Http\Controllers\PaymentProviders\PaddleController as PaddleController;
use App\Livewire\CustomerInvoices;
use App\Livewire\ViewOffer;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
| If you want the URL to be added to the sitemap, add a "sitemapped" middleware to the route (it has to GET route)
|
*/
Route::group(['prefix' => LaravelLocalization::setLocale()], function () {
    Route::get('/', function () {
        return view('home');
    })->name('home')->middleware('sitemapped');
Auth::routes();
});


Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    $user = $request->user();
    if ($user->hasVerifiedEmail()) {
        return redirect()->route('registration.thank-you');
    }

    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/registration/thank-you', function () {
    return view('auth.thank-you');
})->middleware('auth')->name('registration.thank-you');

Route::get('/auth/{provider}/redirect', [OAuthController::class, 'redirect'])
    ->where('provider', 'google|github|facebook|twitter-oauth-2|linkedin|bitbucket|gitlab')
    ->name('auth.oauth.redirect');

Route::get('/auth/{provider}/callback', [OAuthController::class, 'callback'])
    ->where('provider', 'google|github|facebook|twitter-oauth-2|linkedin|bitbucket|gitlab')
    ->name('auth.oauth.callback');

Route::get('/checkout/plan/{planSlug}', [
    App\Http\Controllers\SubscriptionCheckoutController::class,
    'subscriptionCheckout',
])->name('checkout.subscription');

Route::get('/already-subscribed', function () {
    return view('checkout.already-subscribed');
})->name('checkout.subscription.already-subscribed');

Route::get('/checkout/subscription/success', [
    App\Http\Controllers\SubscriptionCheckoutController::class,
    'subscriptionCheckoutSuccess',
])->name('checkout.subscription.success')->middleware('auth');

Route::get('/payment-provider/paddle/payment-link', [
    PaddleController::class,
    'paymentLink',
])->name('payment-link.paddle');

Route::get('/subscription/plans', [App\Http\Controllers\SubscriptionController::class, 'index'])->name('subscription.plans');

Route::get('/subscription/{subscriptionUuid}/change-plan/{planSlug}', [
    App\Http\Controllers\SubscriptionController::class,
    'changePlan',
])->name('subscription.change-plan')->middleware('auth');

Route::post('/subscription/{subscriptionUuid}/change-plan/{planSlug}', [
    App\Http\Controllers\SubscriptionController::class,
    'changePlan',
])->name('subscription.change-plan.post')->middleware('auth');

Route::get('/subscription/change-plan-thank-you', [
    App\Http\Controllers\SubscriptionController::class,
    'success',
])->name('subscription.change-plan.thank-you')->middleware('auth');

// blog
Route::get('/blog/{slug}', [
    App\Http\Controllers\BlogController::class,
    'view',
])->name('blog.view');

Route::get('/blog', [
    App\Http\Controllers\BlogController::class,
    'all',
])->name('blog')->middleware('sitemapped');

Route::get('/blog/category/{slug}', [
    App\Http\Controllers\BlogController::class,
    'category',
])->name('blog.category');

Route::get('/terms-of-service', function () {
    return view('pages.terms-of-service');
})->name('terms-of-service')->middleware('sitemapped');

Route::get('/privacy-policy', function () {
    return view('pages.privacy-policy');
})->name('privacy-policy')->middleware('sitemapped');

// Product checkout routes

Route::get('/buy/product/{productSlug}/{quantity?}', [
    App\Http\Controllers\ProductCheckoutController::class,
    'addToCart',
])->name('buy.product');

Route::get('/cart/clear', [
    App\Http\Controllers\ProductCheckoutController::class,
    'clearCart',
])->name('cart.clear');

Route::get('/checkout/product', [
    App\Http\Controllers\ProductCheckoutController::class,
    'productCheckout',
])->name('checkout.product');

Route::get('/checkout/product/success', [
    App\Http\Controllers\ProductCheckoutController::class,
    'productCheckoutSuccess',
])->name('checkout.product.success')->middleware('auth');

// roadmap

Route::get('/roadmap/suggest', [
    App\Http\Controllers\RoadmapController::class,
    'suggest',
])->name('roadmap.suggest')->middleware('auth');

Route::get('/roadmap', [
    App\Http\Controllers\RoadmapController::class,
    'index',
])->name('roadmap');

Route::get('/roadmap/i/{itemSlug}', [
    App\Http\Controllers\RoadmapController::class,
    'viewItem',
])->name('roadmap.viewItem');


// new routes


// Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice.index');
// Route::get('/invoice/data',[TenantInvoiceController::class, 'getData'])->name('invoice.data');
// Route::get('/invoice/create',[TenantInvoiceController::class, 'create'])->name('invoice.create');
// Route::post('/invoice/store',[TenantInvoiceController::class, 'store'] )->name('invoice.store');
// Route::post('/invoice/{invoice}/update',[TenantInvoiceController::class, 'update'] )->name('invoice.update');
// Route::get('/invoice/{invoice}/payed', [TenantInvoiceController::class, 'payed'])->name('invoice.payed');
// Route::get('/invoice/{invoice}/edit', [TenantInvoiceController::class, 'edit'])->name('invoice.edit'); /// Delete
// Route::post('/invoice/{invoice}/sendreminder',[TenantInvoiceController::class, 'sendReminder'])->name('invoice.sendreminder');
// Route::get('/invoice/{invoice}/resend',[TenantInvoiceController::class, 'resend'])->name('invoice.resend');
// Route::get('/invoice/month2month',[TenantInvoiceController::class, 'month2month'])->name('invoice.month2month');
// Route::get('/invoice/pdf/{invoice}',[TenantInvoiceController::class, 'pdf'])->name('invoice.pdf');

// with us
Route::get('/invoice/{rgnr}/send', [TenantInvoiceController::class, 'send'])->name('invoice.send');
Route::get('/invoice/{rgnr}/duplicate', [TenantInvoiceController::class, 'duplicate'])->name('invoice.duplicate');
Route::get('/invoice/{rgnr}/stream', [TenantInvoiceController::class, 'streamPdfInvoice'])->name('invoice.stream');
Route::get('/invoice/{rgnr}/view', [TenantInvoiceController::class, 'view'])->name('invoice.view');
Route::get('/invoice/{rgnr}/merge/{profile}', [TenantInvoiceController::class, 'mergeWithPdf'])->name('invoice.merge');
Route::get('/xml/{rgnr}/download/{profile}', [TenantInvoiceController::class, 'xmlDownload'])->name('invoice.ddxml');
Route::get('/invoice/{rgnr}/reminder', [TenantInvoiceController::class, 'reminder'])->name('invoice.reminder');
Route::get('/sign-contract/{token}', [OfferController::class, 'view'])->name('sign.contract');
Route::get('/list-invoices/{token}', [TenantInvoiceController::class, 'customerInvoices'])->name('list.invoices');
Route::get('/fakepdf/{type}', [TenantInvoiceController::class, 'createFakeInvoice'])->name('fake.pdf');

// Invoice
Route::get('/invoice/generate/{transactionUuid}', [
    App\Http\Controllers\InvoiceController::class,
    'generate',
])->name('invoice.generate');

Route::get('/invoice/preview', [
    App\Http\Controllers\InvoiceController::class,
    'preview',
])->name('invoice.preview');
