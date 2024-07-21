<?php

namespace App\Models;

use App\Helpers\TenancyHelpers;
use App\Mail\User\VerifyEmail;
use App\Notifications\Auth\QueuedVerifyEmail;
use App\Services\OrderManager;
use App\Services\SubscriptionManager;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\HasTenants;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail, HasTenants, HasDefaultTenant
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'public_name',
        'is_blocked',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function roadmapItems()
    {
        return $this->hasMany(RoadmapItem::class);
    }

    public function roadmapItemUpvotes()
    {
        return $this->belongsToMany(RoadmapItem::class, 'roadmap_item_user_upvotes');
    }

    public function userParameters(): HasMany
    {
        return $this->hasMany(UserParameter::class);
    }

    public function stripeData(): HasMany
    {
        return $this->hasMany(UserStripeData::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }


    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() == 'admin' && !$this->is_admin) {
            return false;
        }
        if ($panel->getId() == 'dashboard' && !$this->hasRole('user')) {
            return false;
        }
        if ($panel->getId() == 'company' && !$this->hasRole(['company', 'super_company'])) {
            return false;
        }

        return true;
    }

    public function getPublicName()
    {
        return $this->public_name ?? $this->name;
    }

    public function scopeAdmin($query)
    {
        return $query->where('is_admin', true);
    }

    public function isAdmin()
    {
        return $this->is_admin;
    }

    public function canImpersonate()
    {
        return $this->hasPermissionTo('impersonate users') || $this->isAdmin();
    }

    public function isSubscribed(?string $productSlug = null): bool
    {
        /** @var SubscriptionManager $subscriptionManager */
        $subscriptionManager = app(SubscriptionManager::class);

        return $subscriptionManager->isUserSubscribed($this, $productSlug);
    }

    public function isTrialing(?string $productSlug = null): bool
    {
        /** @var SubscriptionManager $subscriptionManager */
        $subscriptionManager = app(SubscriptionManager::class);

        return $subscriptionManager->isUserTrialing($this, $productSlug);
    }

    public function hasPurchased(?string $productSlug = null): bool
    {
        /** @var OrderManager $orderManager */
        $orderManager = app(OrderManager::class);

        return $orderManager->hasUserOrdered($this, $productSlug);
    }

    public function subscriptionProductMetadata()
    {
        /** @var SubscriptionManager $subscriptionManager */
        $subscriptionManager = app(SubscriptionManager::class);

        return $subscriptionManager->getUserSubscriptionProductMetadata($this);
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new QueuedVerifyEmail());
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'users_companies', 'user_id', 'company_id');
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->companies;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->companies()->whereKey($tenant)->exists();
    }

    public function customers()
    {
        return $this->morphMany(Customer::class, 'customerable');
    }

    public function projects()
    {
        return $this->morphMany(Project::class, 'projectable');
    }

    public function timesheets()
    {
        return $this->morphMany(Timesheet::class, 'timesheetable');
    }

    public function tasks()
    {
        return $this->morphMany(Task::class, 'taskable');
    }

    public function sales()
    {
        return $this->morphMany(Sales::class, 'salesable');
    }

    public function contacts()
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function getDefaultTenant(Panel $panel): ?Model
    {
        return $this->companies()->first();
    }

    /**
     * Get the userSetting associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userSetting()
    {
        return $this->hasOne(UserSetting::class);
    }

    /**
     * Get the userSetting associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function employeeSetting()
    {
        return $this->hasOne(EmployeeSetting::class);
    }

    /**
     * The employee_tasks that belong to the Task
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function employee_tasks()
    {
        return $this->belongsToMany(Task::class, 'employee_tasks', 'user_id', 'task_id');
    }
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // static::created(function (User $model) {
        //     if (!is_null(auth()->user()) && !is_null(TenancyHelpers::getTenant()) && auth()->user()->hasRole(['company', 'super_company'])) {
        //         TenancyHelpers::getTenant()->users()->attach($model->id);
        //     } else {
        //         $model->userSetting()->create();
        //     }
        // });
    }
    public function address()
    {
        return $this->hasOne(Address::class);
    }
}
