<?php

namespace Tests\Feature\Admin;

use App\Enums\BikeStatus;
use App\Filament\Resources\Bikes\Pages\CreateBike;
use App\Filament\Resources\Bikes\Pages\EditBike;
use App\Filament\Resources\Bikes\Pages\ListBikes;
use App\Filament\Resources\Bikes\RelationManagers\ImagesRelationManager;
use App\Models\Bike;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class BikeManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('admin');
    }

    private function makeBike(array $overrides = []): Bike
    {
        return Bike::create([
            'name' => 'Testsykkel',
            'slug' => 'testsykkel',
            'brand' => 'DBS',
            'model' => 'Test',
            'type' => 'Hybridsykkel',
            'price' => 2000,
            'size' => 'M',
            'description' => 'Test',
            'published_at' => now(),
            ...$overrides,
        ]);
    }

    private function fakeImage(): UploadedFile
    {
        // A real 1x1 PNG, so the test does not depend on the GD extension.
        return UploadedFile::fake()->createWithContent(
            'sykkel.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function validFormData(array $overrides = []): array
    {
        return [
            'name' => 'Trek FX 2',
            'brand' => 'Trek',
            'model' => 'FX 2',
            'type' => 'Hybridsykkel',
            'price' => 4500,
            'status' => BikeStatus::FOR_SALE->value,
            'size' => 'M',
            'description' => 'En fin hybridsykkel.',
            'workItems' => [
                ['title' => 'Gir og bremser', 'description' => 'Kontrollert og justert.'],
                ['title' => 'Drivverk', 'description' => 'Rengjort og smurt.'],
            ],
            'published' => true,
            ...$overrides,
        ];
    }

    public function test_guests_are_redirected_from_the_admin_panel(): void
    {
        $loginUrl = route('filament.admin.auth.login');

        $this->get(route('filament.admin.resources.bikes.index'))->assertRedirect($loginUrl);
        $this->get(route('filament.admin.resources.bikes.create'))->assertRedirect($loginUrl);
        $this->get(route('filament.admin.pages.dashboard'))->assertRedirect($loginUrl);
    }

    public function test_authenticated_users_can_view_the_bike_overview(): void
    {
        $this->makeBike();

        $this->actingAs(User::factory()->create())
            ->get(route('filament.admin.resources.bikes.index'))
            ->assertOk()
            ->assertSee('Testsykkel');
    }

    public function test_the_create_and_edit_pages_can_be_rendered(): void
    {
        $bike = $this->makeBike();
        $bike->workItems()->create(['title' => 'Gir og bremser', 'description' => 'Justert.', 'sort_order' => 1]);
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('filament.admin.resources.bikes.create'))->assertOk();
        $this->actingAs($user)->get(route('filament.admin.resources.bikes.edit', ['record' => $bike]))->assertOk()->assertSee('Gir og bremser');
    }

    public function test_a_bike_can_be_created(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(CreateBike::class)
            ->fillForm($this->validFormData())
            ->call('create')
            ->assertHasNoFormErrors();

        $bike = Bike::where('slug', 'trek-fx-2')->first();

        $this->assertNotNull($bike);
        $this->assertNotNull($bike->published_at);
        $this->assertSame(2, $bike->workItems()->count());
        $this->assertSame('Gir og bremser', $bike->workItems->first()->title);
        $this->assertSame('Kontrollert og justert.', $bike->workItems->first()->description);
    }

    public function test_slugs_are_unique(): void
    {
        $this->makeBike(['slug' => 'trek-fx-2']);

        $this->actingAs(User::factory()->create());

        Livewire::test(CreateBike::class)
            ->fillForm($this->validFormData())
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('bikes', ['slug' => 'trek-fx-2-2']);
    }

    public function test_creating_a_bike_requires_a_name(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(CreateBike::class)
            ->fillForm($this->validFormData(['name' => null]))
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    }

    public function test_a_bike_can_be_updated_and_unpublished(): void
    {
        $bike = $this->makeBike();

        $this->actingAs(User::factory()->create());

        Livewire::test(EditBike::class, ['record' => $bike->getRouteKey()])
            ->fillForm([
                'name' => 'Oppdatert sykkel',
                'published' => false,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $bike->refresh();

        $this->assertSame('Oppdatert sykkel', $bike->name);
        $this->assertSame('testsykkel', $bike->slug, 'Slug should stay stable for SEO.');
        $this->assertNull($bike->published_at);
    }

    public function test_updating_keeps_the_original_publish_date(): void
    {
        $bike = $this->makeBike(['published_at' => now()->subDays(10)]);

        $this->actingAs(User::factory()->create());

        Livewire::test(EditBike::class, ['record' => $bike->getRouteKey()])
            ->fillForm(['name' => 'Fortsatt publisert'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertTrue($bike->refresh()->published_at->lt(now()->subDays(9)));
    }

    public function test_a_bike_can_be_published_and_unpublished_from_the_list(): void
    {
        $bike = $this->makeBike(['published_at' => null]);

        $this->actingAs(User::factory()->create());

        Livewire::test(ListBikes::class)
            ->callTableAction('toggle_published', $bike)
            ->assertNotified('Sykkelen er publisert');

        $this->assertNotNull($bike->refresh()->published_at);

        Livewire::test(ListBikes::class)
            ->callTableAction('toggle_published', $bike)
            ->assertNotified('Sykkelen er avpublisert');

        $this->assertNull($bike->refresh()->published_at);
    }

    public function test_the_status_can_be_changed_from_the_list(): void
    {
        $bike = $this->makeBike(['status' => BikeStatus::FOR_SALE]);

        $this->actingAs(User::factory()->create());

        Livewire::test(ListBikes::class)
            ->call('updateTableColumnState', 'status', (string) $bike->getKey(), BikeStatus::SOLD->value);

        $this->assertSame(BikeStatus::SOLD, $bike->refresh()->status);
    }

    public function test_a_bike_can_be_deleted(): void
    {
        $bike = $this->makeBike();

        $this->actingAs(User::factory()->create());

        Livewire::test(ListBikes::class)
            ->callTableAction(DeleteAction::class, $bike);

        $this->assertDatabaseMissing('bikes', ['id' => $bike->id]);
    }

    public function test_an_image_can_be_uploaded_and_deleted(): void
    {
        Storage::fake('public');

        $bike = $this->makeBike();

        $this->actingAs(User::factory()->create());

        Livewire::test(ImagesRelationManager::class, [
            'ownerRecord' => $bike,
            'pageClass' => EditBike::class,
        ])
            ->callTableAction(CreateAction::class, data: [
                'path' => $this->fakeImage(),
                'alt' => 'Testsykkel sett fra siden',
                'stage' => 'after',
            ])
            ->assertHasNoTableActionErrors();

        $image = $bike->images()->first();

        $this->assertNotNull($image);
        $this->assertSame('after', $image->stage);
        $this->assertSame(1, $image->sort_order);
        Storage::disk('public')->assertExists($image->path);

        Livewire::test(ImagesRelationManager::class, [
            'ownerRecord' => $bike->refresh(),
            'pageClass' => EditBike::class,
        ])
            ->callTableAction(DeleteAction::class, $image);

        $this->assertDatabaseMissing('bike_images', ['id' => $image->id]);
        Storage::disk('public')->assertMissing($image->path);
    }

    public function test_image_upload_requires_an_image_file(): void
    {
        Storage::fake('public');

        $bike = $this->makeBike();

        $this->actingAs(User::factory()->create());

        Livewire::test(ImagesRelationManager::class, [
            'ownerRecord' => $bike,
            'pageClass' => EditBike::class,
        ])
            ->callTableAction(CreateAction::class, data: [
                'path' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
                'alt' => 'Ikke et bilde',
                'stage' => 'after',
            ])
            ->assertHasTableActionErrors(['path']);

        $this->assertSame(0, $bike->images()->count());
    }
}
