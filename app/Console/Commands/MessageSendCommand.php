<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Viber\Api\Message\CarouselContent;
use Viber\Api\Message\Text;
use Viber\Api\Sender;
use Viber\Api\Keyboard;
use Viber\Api\Keyboard\Button;
use Log;

class MessageSendCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'message:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending message';

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
        Log::debug('MessageSendCommand');
        //Олег
        $receiverId = 't+e31vi2nTxCijJpjkgp1A==';
        //Елена
        //$receiverId = 'gYrrIx6jOOFPUwe+6/LWpw==';
        /*
                $text = "<b>Одноэтапная подготовка Фортранс</b>"
        ."<br/>"
        ."<b>ДЕНЬ ПЕРЕД ИССЛЕДОВАНИЕМ</b><br/>"
        ."Разрешена только жидкая прозрачная пища (фильтровый бульон, кисель и прозрачные соки без мякоти, кроме соков красного и фиолетового цветов, негазированные напитки, чай)<br/>"
        ."<font color=#b00000> 15.00</font> - последний прием жидкой пищи<br/>"
        ."<font color=#b00000>17.00 - 21.00</font> - прием раствора Фортансу - 4 л<br/>"
        ."Рекомендуемая норма применения препарата составляет 1-1,5 литра в час (то есть 250 мл каждые 10-15 минут), возможен перерыв на 1 час после первых 2 литров."
        ."<br/>"
        ."Дополнительно можно выпить 0,5 - 1 л прозрачной жидкости"
        ."<br/>"
        ."<b>Последний стакан Фортранс нужно принимать за 3-4 часа до процедуры.</b>";
$data = json_decode($this->getSecondButton($receiverId, $text), true);
$carouselButtons = array();
        $carouselButtons[] = (new Button())
        ->setText($text)
            ->setActionType('reply')
            ->setActionBody('carousel')
        ->setTextHAlign('left');
        //->setTextOpacity(100);
        //->setImage(asset('pictures/iziklin_1.jpg'));
*/
$keyboardButtons = array();
        $keyboardButtons[] = (new Button())->setText("<b><font color='#ffffff'>Изиклин</font></b>")
        ->setActionType('reply')
            ->setActionBody('button')
            ->setTextHAlign("center")
        ->setTextVAlign("center")
        ->setBgColor("#8074d6")
        ->setColumns(3)
        ->setRows(1)
        ->setImage(asset('pictures/images.png'))
        ->setBgColor("#fdfdfd");
        //->setBgMediaScaleType("crop");
            
        try {
            //$response = app('viber_bot')->call('send_message', $data);
/*
            // carousel
            $response = app('viber_bot')->sendMessage((new CarouselContent())->setSender((new Sender())->setName(config('viber.bot.name')))
                ->setButtons($carouselButtons)
                ->setReceiver($receiverId)
                ->setKeyboard((new Keyboard())->setButtons($keyboardButtons))
                );
                */
            // keyboard
             $response = app('viber_bot')->sendMessage((new Text())->setSender((new Sender())->setName(config('viber.bot.name')))
              ->setText('\<b\>Нажмите\</b\> на любую кнопку.')
              ->setKeyboard((new Keyboard())->setButtons($keyboardButtons))
              ->setReceiver($receiverId));
             
            Log::debug('message response');
            Log::debug($response->getData());
        } catch (\Exception $e) {
            Log::error($e);
        } // catch
    }
    
    private function getData($receiverId) {
        return "{
                \"receiver\":\"$receiverId\",
                \"type\":\"rich_media\",
                \"min_api_version\":2,
                \"rich_media\":{
                \"Type\":\"rich_media\",
                \"ButtonsGroupColumns\":6,
                \"ButtonsGroupRows\":7,
                \"BgColor\":\"#FFFFFF\",
                \"Buttons\":[
                {
                    \"Columns\":6,
                    \"Rows\":3,
                    \"ActionType\":\"open-url\",
                    \"ActionBody\":\"https://www.google.com\",
                    \"Image\":\"http://html-test:8080/myweb/guy/assets/imageRMsmall2.png\"
                },
                {
                    \"Columns\":6,
                    \"Rows\":2,
                    \"Text\":\"<font color=#323232><b>Headphones with Microphone, On-ear Wired earphones</b></font><font color=#777777><br>Sound Intone </font><font color=#6fc133>$17.99</font>\",
                    \"ActionType\":\"open-url\",
                    \"ActionBody\":\"https://www.google.com\",
                    \"TextSize\":\"medium\",
                    \"TextVAlign\":\"middle\",
                    \"TextHAlign\":\"left\"
                },
                {
                    \"Columns\":6,
                    \"Rows\":1,
                    \"ActionType\":\"reply\",
                    \"ActionBody\":\"https://www.google.com\",
                    \"Text\":\"<font color=#ffffff>Buy</font>\",
                    \"TextSize\":\"large\",
                    \"TextVAlign\":\"middle\",
                    \"TextHAlign\":\"middle\",
                    \"Image\":\"https://s14.postimg.org/4mmt4rw1t/Button.png\"
                },
                {
                    \"Columns\":6,
                    \"Rows\":1,
                    \"ActionType\":\"reply\",
                    \"ActionBody\":\"https://www.google.com\",
                    \"Text\":\"<font color=#8367db>MORE DETAILS</font>\",
                    \"TextSize\":\"small\",
                    \"TextVAlign\":\"middle\",
                    \"TextHAlign\":\"middle\"
                },
                {
                    \"Columns\":6,
                    \"Rows\":3,
                    \"ActionType\":\"open-url\",
                    \"ActionBody\":\"https://www.google.com\",
                    \"Image\":\"https://s16.postimg.org/wi8jx20wl/image_RMsmall2.png\"
                },
                {
                    \"Columns\":6,
                    \"Rows\":2,
                    \"Text\":\"<font color=#323232><b>Hanes Men's Humor Graphic T-Shirt</b></font><font color=#777777><br>Hanes</font><font color=#6fc133>$10.99</font>\",
                    \"ActionType\":\"open-url\",
                    \"ActionBody\":\"https://www.google.com\",
                    \"TextSize\":\"medium\",
                    \"TextVAlign\":\"middle\",
                    \"TextHAlign\":\"left\"
                },
                {
                    \"Columns\":6,
                    \"Rows\":1,
                    \"ActionType\":\"reply\",
                    \"ActionBody\":\"https://www.google.com\",
                    \"Text\":\"<font color=#ffffff>Buy</font>\",
                    \"TextSize\":\"large\",
                    \"TextVAlign\":\"middle\",
                    \"TextHAlign\":\"middle\",
                    \"Image\":\"https://s14.postimg.org/4mmt4rw1t/Button.png\"
                },
                {
                    \"Columns\":6,
                    \"Rows\":1,
                    \"ActionType\":\"reply\",
                    \"ActionBody\":\"https://www.google.com\",
                    \"Text\":\"<font color=#8367db>MORE DETAILS</font>\",
                    \"TextSize\":\"small\",
                    \"TextVAlign\":\"middle\",
                    \"TextHAlign\":\"middle\"
                }
                ]
            }
            }";
        }
        
        private function getSecondButton($receiverId, $data) {
            return "{
                \"receiver\":\"$receiverId\",
                \"type\":\"rich_media\",
                \"min_api_version\":2,
                \"rich_media\":{
                \"Type\":\"rich_media\",
                \"ButtonsGroupColumns\":6,
                \"ButtonsGroupRows\":7,
                \"BgColor\":\"#FFFFFF\",
                \"Buttons\":[
{
                    \"Columns\":6,
                    \"Rows\":2,
                    \"Text\":\"$data\",
                    \"ActionType\":\"open-url\",
                    \"ActionBody\":\"https://www.google.com\",
                    \"TextSize\":\"medium\",
                    \"TextVAlign\":\"middle\",
                    \"TextHAlign\":\"left\"
                },
                {
                    \"Columns\":6,
                    \"Rows\":1,
                    \"ActionType\":\"reply\",
                    \"ActionBody\":\"https://www.google.com\",
                    \"Text\":\"<font color=#8367db>MORE DETAILS</font>\",
                    \"TextSize\":\"small\",
                    \"TextVAlign\":\"middle\",
                    \"TextHAlign\":\"middle\"
                }
                ]
            }
            }";
        }
        
        private function getButton() {
            return "{
                \"Columns\": 2,
                \"Rows\": 2,
                \"Text\": \"<b><font color='#ffffff'>Изиклин</font></b>\",
                \"TextHAlign\": \"center\",
                \"TextVAlign\": \"middle\",
                \"ActionType\": \"reply\",
                \"ActionBody\": \"Изиклин\",
                \"BgColor\": \"#8074d6\"
 \"Frame\": {
                \"CornerRadius\": 5
            }
            }";
        }
        }
