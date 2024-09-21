<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Customer;
use App\Models\Expenditure;
use App\Models\InvoiceItem;
use App\Models\License;
use App\Models\Offer;
use App\Models\Project;
use App\Models\Sales;
use App\Models\Task;
use App\Models\TenantInvoice;
use App\Models\Timesheet;
use App\Models\User;
use App\Services\UserManager;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserRoleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::beginTransaction();

        try {
            // init the user wtih role USER
            $user = User::where('email', 'userdemo@test.com')->first();


            if (!$user) {
                $userManager = new UserManager();

                $data = [
                    'name' => 'User Test 1',
                    'email' => 'userdemo@test.com',
                    'password' => Hash::make('password'),
                    'is_admin' => false,
                ];

                $user = $userManager->createUser($data, 'user');
            }

            $user->settings()->updateOrCreate([
                'user_id' => $user->id
            ], [
                'name' => fake('de_DE')->name(),
                'managing_director' => fake('de_DE')->name(),
                'legal_name' => fake('de_DE')->company(),
                'avatar_url' => $this->saveFakeImage(fake('de_DE')->imageUrl(category: fake('de_DE')->company(), width: 620, height: 320)),
                'website_url' => fake('de_DE')->url(),
                'place_of_jurisdiction' => fake('de_DE')->city(),
                'slug' => Str::slug(fake('de_DE')->name()),
                'address' => fake('de_DE')->address(),
                'postal_code' => fake('de_DE')->postcode(),
                'tax_id' => fake('de_DE')->regexify('[A-Z0-9]{10}'),
                'city' => fake('de_DE')->city(),
                'vat_id' => 'DE20111340209',
                'country_id' => 51, // 'Germany' Id from countries table
                'iban' => fake('de_DE')->iban(),
                'vat_percent' => 14.00,
                'account_number' => fake('de_DE')->bankAccountNumber(),
                'bank_code' => fake('de_DE')->regexify('[0-9]{8}'),
                'bic' => fake('de_DE')->swiftBicNumber(),
                'contact_number' => fake('de_DE')->phoneNumber(),
                'contact_email' => fake('de_DE')->safeEmail(),
                'ready_to_generate' => 1,
            ]);

            $modelId = $user->id;
            $modelType = 'App\Models\User';

            // start seeding data

            // customers
            $customers = Customer::factory(5)->create([
                'customerable_id' => $modelId,
                'customerable_type' => $modelType,
            ]);

            // license
            $license = License::factory(3)->create([
                'licenseable_id' => $modelId,
                'licenseable_type' => $modelType,
            ]);

            // expenditure
            $exp = Expenditure::factory()
                ->count(15)
                ->state(new Sequence(
                    function (Sequence $sequence) {
                        $dateOnTime = fake()->dateTimeBetween('-1 year', 'now');
                        return [
                            'frequency' => 'one-time',
                            'start' => $dateOnTime,
                            'end' => $dateOnTime
                        ];
                    },
                    fn(Sequence $sequence) => [
                        'frequency' => 'monthly',
                        'start' => Carbon::now(),
                        'end' => Carbon::createFromDate(null, fake('de_DE')->numberBetween(2, 12), 1)->endOfMonth()
                    ],
                    fn(Sequence $sequence) =>  [
                        'frequency' => 'yearly',
                        'start' => Carbon::now()->startOfYear(),
                        'end' => Carbon::now()->addYears(fake('de_DE')->numberBetween(2, 7)),
                    ],

                ))
                ->create([
                    'expenditureable_id' => $modelId,
                    'expenditureable_type' => $modelType,
                ]);

            // sales
            $sales = Sales::factory()->count(5)->create([
                'salesable_id' => $modelId,
                'salesable_type' => $modelType,
            ]);

            // invoices and invoice item

            $invoices = TenantInvoice::factory()
                ->count(10)
                ->state(
                    new Sequence(
                        fn(Sequence $sequence) =>  ['customer_id' =>  $customers->random()->id]
                    ),
                )
                ->has(InvoiceItem::factory()->count(3))
                ->create([
                    'invoiceable_id' => $modelId,
                    'invoiceable_type' => $modelType,
                ]);

            // offers
            $offer = Offer::factory()->count(3)
                ->state(
                    new Sequence(
                        fn(Sequence $sequence) =>  ['customer_id' =>  $customers->random()->id],
                        fn(Sequence $sequence) =>  ['customer_id' =>  $customers->random()->id, 'external_offer_url' => fake()->url(), 'external_offer' => true],
                    ),
                )
                ->create([
                    'offerable_id' => $modelId,
                    'offerable_type' => $modelType,
                ]);

            // projects
            $project = Project::factory()
                ->count(3)
                ->state(
                    new Sequence(
                        fn(Sequence $sequence) =>  ['customer_id' =>  $customers->random()->id]
                    ),
                )
                ->create([
                    'projectable_id' => $modelId,
                    'projectable_type' => $modelType,
                ]);

            // tasks and timehseet
            $tasks = Task::factory()->count(8)
                ->has(Timesheet::factory()->count(3)->state(function (array $attributes) use ($modelId, $modelType) {
                    return [
                        'timesheetable_id' => $modelId,
                        'timesheetable_type' => $modelType,
                        'employee_id' => $modelId,
                    ];
                }), 'timesheet_tasks')
                ->state(
                    new Sequence(
                        fn(Sequence $sequence) =>  ['project_id' =>  $project->random()->id]
                    ),
                )
                ->create([
                    'taskable_id' => $modelId,
                    'taskable_type' => $modelType,
                ]);

            $contacts = Contact::factory()->count(6)
                ->state(
                    new Sequence(
                        fn(Sequence $sequence) =>  ['company' => 'Apple'],
                        fn(Sequence $sequence) =>  ['company' => 'Meta']
                    ),
                )->create([
                    'contactable_id' => $modelId,
                    'contactable_type' => $modelType,
                ]);

            // assign some contacts to customers

            foreach ($contacts as  $contact) {
                $contact->customers()->attach($customers->random()->id);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('error dyring seeding: ' . $e->getMessage());

            $this->command->error('error during seeding: ' . $e->getMessage());
        }
    }

    private function saveFakeImage($url)
    {
        $contents = file_get_contents($url);
        $name = 'user_setting_image_' . Str::random(10) . '.jpg';
        Storage::put('public/logos/' . $name, $contents);
        return 'logos/'. $name;
    }
}
