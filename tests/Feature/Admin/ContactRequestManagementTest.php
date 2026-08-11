<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\ContactRequests\ContactRequestResource;
use App\Filament\Resources\ContactRequests\Pages\ListContactRequests;
use App\Mail\ContactReceived;
use App\Models\ContactRequest;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class ContactRequestManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('admin');
    }

    private function makeContactRequest(array $overrides = []): ContactRequest
    {
        return ContactRequest::create([
            'name' => 'Ola Nordmann',
            'contact' => 'ola@example.com',
            'subject' => 'bike',
            'message' => 'Jeg ser etter en hybridsykkel i størrelse M.',
            'consent' => true,
            ...$overrides,
        ]);
    }

    public function test_a_submitted_contact_form_is_saved(): void
    {
        Mail::fake();

        $response = $this->from(route('home'))->post(route('contact.store'), [
            'name' => 'Kari Nordmann',
            'contact' => '99887766',
            'subject' => 'bike',
            'message' => 'Jeg lurer på om dere har en damesykkel på lager.',
            'consent' => '1',
        ]);

        $response->assertRedirect(route('home'))
            ->assertSessionHas('status', 'Takk! Henvendelsen er sendt.');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('id="toast-status"', false)
            ->assertSee('Takk! Henvendelsen er sendt.');

        $this->assertDatabaseHas('contact_requests', [
            'name' => 'Kari Nordmann',
            'contact' => '99887766',
            'subject' => 'bike',
            'message' => 'Jeg lurer på om dere har en damesykkel på lager.',
            'read_at' => null,
        ]);

        Mail::assertQueued(ContactReceived::class);
    }

    public function test_service_subject_is_allowed_in_contact_form(): void
    {
        Mail::fake();

        $this->post(route('contact.store'), [
            'name' => 'Kari Nordmann',
            'contact' => '99887766',
            'subject' => 'service',
            'message' => 'Trenger service',
            'consent' => '1',
        ])->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseHas('contact_requests', [
            'name' => 'Kari Nordmann',
            'contact' => '99887766',
            'subject' => 'service',
            'message' => 'Trenger service',
            'read_at' => null,
        ]);

        Mail::assertQueued(ContactReceived::class);
    }

    public function test_guests_are_redirected_from_the_contact_requests_page(): void
    {
        $this->get(route('filament.admin.resources.contact-requests.index'))
            ->assertRedirect(route('filament.admin.auth.login'));
    }

    public function test_authenticated_users_can_view_the_contact_requests_list(): void
    {
        $this->makeContactRequest();

        $this->actingAs(User::factory()->create())
            ->get(route('filament.admin.resources.contact-requests.index'))
            ->assertOk()
            ->assertSee('Ola Nordmann');
    }

    public function test_viewing_a_contact_request_marks_it_as_read(): void
    {
        $entry = $this->makeContactRequest();

        $this->assertNull($entry->read_at);

        $this->actingAs(User::factory()->create())
            ->get(route('filament.admin.resources.contact-requests.view', ['record' => $entry]))
            ->assertOk()
            ->assertSee('Ola Nordmann')
            ->assertSee('Jeg ser etter en hybridsykkel i størrelse M.');

        $this->assertNotNull($entry->refresh()->read_at);
    }

    public function test_a_contact_request_can_be_deleted(): void
    {
        $entry = $this->makeContactRequest();

        $this->actingAs(User::factory()->create());

        Livewire::test(ListContactRequests::class)
            ->callTableAction(DeleteAction::class, $entry);

        $this->assertDatabaseMissing('contact_requests', ['id' => $entry->id]);
    }

    public function test_contact_requests_cannot_be_created_from_the_panel(): void
    {
        $this->assertFalse(
            ContactRequestResource::canCreate(),
        );
    }
}
