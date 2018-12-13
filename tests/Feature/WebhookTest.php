<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Viber\Api\Message\Text;
use Viber\Api\Sender;
use Viber\Api\Event\Type;
use App\Models\ViberUser;
use App\Models\Session;
use Mockery;
use Carbon\Carbon;
use Log;

class WebhookTest extends TestCase
{
    use DatabaseTransactions;

    protected $mock;

    public function setUp()
    {
        Parent::setUp();

        $this->mock = Mockery::mock(\Viber\Client::class);
        $this->app->instance("viber_bot", $this->mock);
    }

    public function testHandleConversation()
    {
        Log::debug('testHandleConversation');
        $counter = 1;
        while (ViberUser::where('viber_id', (string) $counter)->count() > 0) {
            $counter ++;
        } // while
        $response = $this->json('POST', '/api/webhook', [
            'event' => Type::CONVERSATION,
            'user' => (new Sender())->setId((string) $counter)
                ->setName('test user')
                ->toArray()
        ]);

        $this->assertNotNull(ViberUser::where('viber_id', $counter)->first());
        // $content = json_decode($response->content(), true);
        // Log::debug($content);
    }

    public function testHandleSubscribed()
    {
        Log::debug('testHandleSubscribed');
        $this->mock->shouldReceive('sendMessage')->once();
        $counter = 1;
        while (ViberUser::where('viber_id', (string) $counter)->count() > 0) {
            $counter ++;
        } // while
        $response = $this->json('POST', '/api/webhook', [
            'event' => Type::SUBSCRIBED,
            'sender' => (new Sender())->setId((string) $counter)
                ->setName('test user')
                ->toArray()
        ]);

        $user = ViberUser::where('viber_id', (string) $counter)->first();
        $this->assertNotNull($user, 'user is null');
        $this->assertTrue($user->subscribed, 'user not subscribed');

        $response = $this->json('POST', '/api/webhook', [
            'event' => Type::UNSUBSCRIBED,
            'user_id' => $counter
        ]);

        $user = ViberUser::find($user->id);
        $this->assertTrue(! $user->subscribed, 'user subscribed');
    }

    public function testMessages()
    {
        Log::debug("testMessages");
        $this->mock->shouldReceive('sendMessage')->times(6);
        $counter = 1;
        while (ViberUser::where('viber_id', (string) $counter)->count() > 0) {
            $counter ++;
        } // while

        $sender = (new Sender())->setId((string) $counter)
            ->setName('test user')
            ->toArray();

        // hello message
        $response = $this->json('POST', '/api/webhook', [
            'event' => Type::MESSAGE,
            'sender' => $sender,
            'message' => [
                'text' => 'hello'
            ]
        ]);

        $user = ViberUser::where('viber_id', $counter)->first();
        $this->assertNotNull($user, 'user is null');
        $this->assertTrue($user->subscribed, 'user unsubscribed');
        $session = $user->session;
        $this->assertNotNull($session, 'session is null');
        $this->assertTrue($session->last_message_id == 1, 'last_message_id not 01');

        // drug message1
        $response = $this->json('POST', '/api/webhook', [
            'event' => Type::MESSAGE,
            'sender' => $sender,
            'message' => [
                'text' => 'drug2'
            ]
        ]);
        $session = Session::find($session->id);
        $drug = $session->drug;
        $this->assertNotNull($drug, 'drug is null');
        $this->assertTrue($drug->code == 'drug2');

        // stage message2
        $this->assertTrue($session->stage_num == 0, 'stage not 0');
        $response = $this->json('POST', '/api/webhook', [
            'event' => Type::MESSAGE,
            'sender' => $sender,
            'message' => [
                'text' => '2'
            ]
        ]);
        $session = Session::find($session->id);
        $this->assertTrue($session->stage_num == 2, 'stage not 2');

        // month message3
        $this->assertNull($session->procedure_at, 'procedure_at is not null');
        $month = Carbon::now()->month;
        $response = $this->json('POST', '/api/webhook', [
            'event' => Type::MESSAGE,
            'sender' => $sender,
            'message' => [
                'text' => '1'
            ]
        ]);
        $session = Session::find($session->id);
        $this->assertTrue($session->procedure_at->month == ($month < 12 ? $month + 1 : 1), 'month did not increesed');

        // day message4
        $this->assertTrue($session->procedure_at->day == 1, 'not first day of month');
        $day = Carbon::now()->day;
        $day = $day < Carbon::now()->daysInMonth ? $day + 1 : $day - 1;
        $response = $this->json('POST', '/api/webhook', [
            'event' => Type::MESSAGE,
            'sender' => $sender,
            'message' => [
                'text' => $day
            ]
        ]);
        $session = Session::find($session->id);
        $this->assertTrue($session->procedure_at->day == $day, 'day did not set');

        // time message5
        $response = $this->json('POST', '/api/webhook', [
            'event' => Type::MESSAGE,
            'sender' => $sender,
            'message' => [
                'text' => '18:00'
            ]
        ]);
        $session = Session::find($session->id);
        $this->assertTrue($session->procedure_at->hour == 18 && $session->procedure_at->minute == 0, 'day did not set');
    }
}