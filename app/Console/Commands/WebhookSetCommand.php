<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Viber\Api\Event\Type;
use Viber\Api\Message\Text;
use Viber\Api\Sender;
use Log;

class WebhookSetCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook:set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setting viber webhooks.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::debug('WebhookSetCommand start');
                try {
                    //$response = app('viber_bot')->getAccountInfo();
                        //set webhook
                       $response = app('viber_bot')->setWebhook(config('viber.webhook_url'));
                                                Log::debug('set message webhook response');
            Log::debug($response->getData());

            /*
                    $accountId = 'pa:5244217616731985205';
                    $receiverId = 't+e31vi2nTxCijJpjkgp1A==';
                                        $response = app('viber_bot')->sendMessage((new Text())->setSender((new Sender())->setName("chat bot"))->setReceiver($receiverId)->setText('Hello from chat bot.'));
                                        Log::info('sendMessage response');
                    Log::info($response->getData());
*/
                        } catch (\Exception $e) {
            Log::error($e);
        } // catch
        Log::debug('WebhookSetCommand stop');
    }
}
