<?php

namespace Feature\Services;

use App\Constants\SubscriptionStatus;
use App\Exceptions\SubscriptionCreationNotAllowedException;
use App\Models\Currency;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\SubscriptionManager;
use Illuminate\Support\Str;
use Tests\Feature\FeatureTest;

class SubscriptionManagerTest extends FeatureTest
{

    /**
     * @dataProvider nonDeadSubscriptionProvider
     */
    public function test_can_only_create_subscription_if_no_other_non_dead_subscription_exists($status)
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $slug = Str::random();
        $plan = Plan::factory()->create([
            'slug' => $slug,
            'is_active' => true,
        ]);

        Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => $status,
            'plan_id' => $plan->id,
        ])->save();

        $manager = app()->make(SubscriptionManager::class);

        $this->expectException(SubscriptionCreationNotAllowedException::class);
        $manager->create($slug, $user->id);
    }

    public function test_can_create_subscription_multiple_subscriptions_are_enabled()
    {
        config()->set('app.multiple_subscriptions_enabled', true);
        $user = $this->createUser();
        $this->actingAs($user);

        $slug = Str::random();
        $plan = Plan::factory()->create([
            'slug' => $slug,
            'is_active' => true,
        ]);

        // add a plan price
        $plan->prices()->create([
            'price' => 1000,
            'currency_id' => Currency::where('code', 'USD')->first()->id,
        ]);

        Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => SubscriptionStatus::ACTIVE,
            'plan_id' => $plan->id,
        ])->save();

        $manager = app()->make(SubscriptionManager::class);

        $subscription = $manager->create($slug, $user->id);

        $this->assertNotNull($subscription);
    }


    public static function nonDeadSubscriptionProvider()
    {
        return [
            'pending' => [
                'pending',
            ],
            'active' => [
                'active',
            ],
            'paused' => [
                'paused',
            ],
            'past_due' => [
                'past_due',
            ],
        ];
    }


}
