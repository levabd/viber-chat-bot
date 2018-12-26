<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Viber\Api\Event\Type;
use Viber\Api\Message\Picture;
use Viber\Api\Message\Text;
use Viber\Api\Sender;
use Viber\Api\Keyboard;
use Viber\Api\Keyboard\Button;
use App\Models\ViberUser;
use App\Models\Session;
use App\Models\Drug;
use DB;
use Carbon\Carbon;
use Log;

class WebhookController extends Controller
{

    public function handle(Request $request)
    {
        Log::info('WebhookController->handle');
        Log::info($request->input());
        switch ($request->input('event')) {
            case Type::MESSAGE:
                return $this->handleMessage($request);
                break;
            case Type::SUBSCRIBED:
                return $this->handleSubscribed($request);
                break;
            case Type::UNSUBSCRIBED:
                return $this->handleUnsubscribed($request);
                break;
            case Type::CONVERSATION:
                return $this->handleConversation($request);
                break;
        } // switch
        return response()->json();
    }

    private function handleConversation(Request $request)
    {
        Log::debug("WebhookController->handleConversation");
        try {
            $user = $this->getViberUser($request->input('user'));
            $session = Session::create([
                'user_id' => $user->id,
                'last_message_id' => 1
            ]);
            $user->session_id = $session->id;
            $user->save();
            /*
            $response = (new Text())->setSender((new Sender())->setName(config('viber.bot.name'))
                ->setAvatar(asset('pictures/' . config('viber.bot.avatar'))))
                ->setText(__('message.welcome'));
                */
            $response = $this->getMessage1($user, __('message.welcome'));
                        return response()->json($response->toApiArray());
        } catch (\Exception $e) {
            Log::error($e);
        } // catch
        return response()->json();
    }

    private function handleSubscribed(Request $request)
    {
        Log::debug("WebhookController->handleSubscribed");
        $user = $this->getViberUser($request->input('sender'));
        if (! $user->subscribed) {
            $user->subscribed = true;
            $user->save();
            $this->sendWelcome($user);
        } // if unsubscribed
        return response()->json();
    }

    private function handleUnsubscribed(Request $request)
    {
        Log::debug("WebhookController->handleUnsubscribed");
        try {
            $user = ViberUser::where('viber_id', $request->input('user_id'))->first();
            if ($user !== null) {
                $user->subscribed = false;
                $user->save();
            } // if user not null
        } catch (\Exception $e) {
            Log::error($e);
        } // catch
        return response()->json();
    }

    private function handleMessage(Request $request)
    {
        Log::debug("WebhookController->handleMessage");
        try {
            $user = $this->getViberUser($request->input('sender'));
            if (! $user->subscribed) {
                $user->subscribed = true;
                $user->session_id = null;
                $user->save();
            } // if unsubscribed

            if ($user->session_id === null || Carbon::now()->subHours(12)->gt($user->session->updated_at)) {
                $session = Session::create([
                    'user_id' => $user->id
                ]);
                $user->session_id = $session->id;
                $user->save();
                $this->sendMessage1($user, $session, __('message.welcome'));
            } else {
                switch ($user->session->last_message_id) {
                    case 1:
                        $this->handleMessage1($user, $user->session, $request);
                        break;
                    case 2:
                        $this->handleMessage2($user, $user->session, $request);
                        break;
                    case 3:
                        $this->handleMessage3($user, $user->session, $request);
                        break;
                    case 4:
                        $this->handleMessage4($user, $user->session, $request);
                        break;
                    case 5:
                        $this->handleMessage5($user, $user->session, $request);
                        break;
                    case 6:
                        $this->handleMessage6($user, $user->session, $request);
                        break;
                    case 11:
                        $this->handleMessage11($user, $user->session, $request);
                        break;
                    case 12:
                        $this->handleMessage12($user, $user->session, $request);
                        break;
                    case 13:
                        $this->handleMessage13($user, $user->session, $request);
                        break;
                    case 14:
                        $this->handleMessage14($user, $user->session, $request);
                        break;
                    case 15:
                        $this->handleMessage15($user, $user->session, $request);
                        break;
                    case 211:
                        $this->handleMessage211($user, $user->session, $request);
                        break;
                    case 212:
                        $this->handleMessage212($user, $user->session, $request);
                        break;
                    case 213:
                        $this->handleMessage213($user, $user->session, $request);
                        break;
                    case 221:
                        $this->handleMessage221($user, $user->session, $request);
                        break;
                    case 222:
                        $this->handleMessage222($user, $user->session, $request);
                        break;
                    case 223:
                        $this->handleMessage223($user, $user->session, $request);
                        break;
                    case 224:
                        $this->handleMessage224($user, $user->session, $request);
                        break;
                    case 21:
                        $this->handleMessage21($user, $user->session, $request);
                        break;
                    case 22:
                        $this->handleMessage22($user, $user->session, $request);
                        break;
                    case 23:
                        $this->handleMessage23($user, $user->session, $request);
                        break;
                    case 24:
                        $this->handleMessage24($user, $user->session, $request);
                        break;
                    case 25:
                        $this->handleMessage25($user, $user->session, $request);
                        break;
                } // switch
            } // if last_message_at
        } catch (\Exception $e) {
            Log::error($e);
        } // catch
        return response()->json();
    }

    private function handleMessage1(ViberUser $user, Session $session, Request $request)
    {
        Log::debug('handleMessage1');
        if (empty($request['message']) || empty($request['message']['text'])) {
            $this->sendMessage1($user, $session, __('message.wrong.drug'));
            return;
        } // if empty

        switch ($this->recognizeDrug($request['message']['text'])) {
            case 'drug1':
                $session->stage_num = 2;
                $session->save();
                DB::update("update sessions set drug_id=(select id from drugs where code=?) where id=?", [
                    'drug1',
                    $session->id
                ]);
                $session = $session->find($session->id);
                $this->sendMessage3($user, $session, __('message.drug1_choose'), true);
                break;
            case 'drug2':
                DB::update("update sessions set drug_id=(select id from drugs where code=?) where id=?", [
                    'drug2',
                    $session->id
                ]);
                $session = Session::where('id', $session->id)->with('drug')->first();
                $this->sendMessage2($user, $session, __('message.drug2_choose'), true);
                break;
            default:
                Log::debug('default');
                $this->sendMessage1($user, $session, __('message.wrong.drug'));
        } // if switch
    }

    private function handleMessage2(ViberUser $user, Session $session, Request $request)
    {
        if (! isset($request['message']) || ! isset($request['message']['text'])) {
            $this->sendMessage2($user, $session, __('message.wrong.stage'));
            return;
        } // if empty
        $stageNum = $this->recognizeStageNum($request['message']['text']);
        if ($stageNum < 1 || $stageNum > 2) {
            $this->sendMessage2($user, $session, __('message.wrong.stage'));
            return;
        } // if not stage

        $session->stage_num = $stageNum;
        $session->save();
        $this->sendMessage3($user, $session);
    }

    private function handleMessage3(ViberUser $user, Session $session, Request $request)
    {
        if (! isset($request['message']) || ! isset($request['message']['text'])) {
            $this->sendMessage3($user, $session, __('message.wrong.month'));
            return;
        } // if empty
        $month = $this->recognizeMonth($request['message']['text']);
        if ($month < 0 || $month > 1) {
            $this->sendMessage3($user, $session, __('message.wrong.month'));
            return;
        } // if not month
        $session->procedure_at = Carbon::now()->startOfMonth();
        $session->procedure_at = $session->procedure_at->addMonths($month);
        $session->save();
        $this->sendMessage4($user, $session);
    }

    private function handleMessage4(ViberUser $user, Session $session, Request $request)
    {
        Log::debug('handleMessage4');
        if (! isset($request['message']) || ! isset($request['message']['text']) || ! ctype_digit((string) $request['message']['text'])) {
            $this->sendMessage4($user, $session, __('message.wrong.day'));
            return;
        } // if empty
        $day = (int) $request['message']['text'];
        $now = Carbon::now();
        $today = $now->month == $session->month ? $now->day : 0;
        if ($day <= $today || $day > $session->procedure_at->daysInMonth) {
            $this->sendMessage4($user, $session, __('message.wrong.day'));
            return;
        } // if not day

        $session->procedure_at = $session->procedure_at->startOfMonth()->addDays($day - 1);
        $session->save();
        $this->sendMessage5($user, $session);
    }

    private function handleMessage5(ViberUser $user, Session $session, Request $request)
    {
        if (! isset($request['message']) || ! isset($request['message']['text'])) {
            $this->sendMessage5($user, $session, __('message.wrong.time'));
            return;
        } // if empty
        switch ($request['message']['text']) {
            case '09:00':
                $session->procedure_at = $session->procedure_at->setTime(9, 0, 0);
                $session->save();
                break;
            case '10:00':
                $session->procedure_at = $session->procedure_at->setTime(10, 0, 0);
                $session->save();
                break;
            case '11:00':
                $session->procedure_at = $session->procedure_at->setTime(11, 0, 0);
                $session->save();
                break;
            case '12:00':
                $session->procedure_at = $session->procedure_at->setTime(12, 0, 0);
                $session->save();
                break;
            case '13:30':
                $session->procedure_at = $session->procedure_at->setTime(13, 30, 0);
                $session->save();
                break;
            case '14:30':
                $session->procedure_at = $session->procedure_at->setTime(14, 30, 0);
                $session->save();
                break;
            case '15:30':
                $session->procedure_at = $session->procedure_at->setTime(15, 30, 0);
                $session->save();
                break;
            case '16:00':
                $session->procedure_at = $session->procedure_at->setTime(16, 0, 0);
                $session->save();
                break;
            case '17:00':
                $session->procedure_at = $session->procedure_at->setTime(17, 0, 0);
                $session->save();
                break;
            case '18:00':
                $session->procedure_at = $session->procedure_at->setTime(18, 0, 0);
                $session->save();
                break;
            default:
                $this->sendMessage5($user, $session, __('message.wrong.time'));
                return;
        } // switch
        $user->completed_session_id = $session->id;
        $user->save();
        if ($session->drug->code == 'drug1') {
            $this->sendMessage11($user, $session);
        } elseif ($session->drug->code == 'drug2') {
            if ($session->stage_num == 1) {
                $this->sendMessage211($user, $session);
            } elseif ($session->stage_num == 2) {
                $this->sendMessage221($user, $session);
            } else {
                $this->sendMessage2($user, $session, __('message.wrong.stage'));
            } // if stage
        } else {
            $this->sendMessage1($user, $session, __('message.wrong.drug'));
        } // if drug
    }

    private function handleMessage6(ViberUser $user, Session $session, Request $request)
    {
        Log::debug('handleMessage6');
        if (empty($request['message']) || empty($request['message']['text'])) {
            $this->sendMessage6($user, $session);
            return;
        } // if empty

        if ($request['message']['text'] == 'restart') {
            $session = Session::create([
                'user_id' => $user->id
            ]);
            $user->session_id = $session->id;
            $user->save();
            $this->sendMessage1($user, $session);
        } else {
            $this->sendMessage6($user, $session);
        } // if restart
    }

    private function handleMessage11(ViberUser $user, Session $session, Request $request)
    {
        Log::debug('handleMessage11');
        if (empty($request['message']) || empty($request['message']['text'])) {
            $this->sendMessage11($user, $session);
            return;
        } // if empty

        if ($request['message']['text'] == 'next') {
            $this->sendMessage12($user, $session);
        } else {
            $this->sendMessage11($user, $session);
        } // if next
    }

    private function handleMessage12(ViberUser $user, Session $session, Request $request)
    {
        Log::debug('handleMessage12');
        if (empty($request['message']) || empty($request['message']['text'])) {
            $this->sendMessage12($user, $session);
            return;
        } // if empty
        if ($request['message']['text'] == 'next') {
            $this->sendMessage13($user, $session);
        } else {
            $this->sendMessage12($user, $session);
        } // if next
    }

    private function handleMessage13(ViberUser $user, Session $session, Request $request)
    {
        Log::debug('handleMessage13');
        if (empty($request['message']) || empty($request['message']['text'])) {
            $this->sendMessage13($user, $session);
            return;
        } // if empty
        if ($request['message']['text'] == 'next') {
            $this->sendMessage14($user, $session);
        } else {
            $this->sendMessage13($user, $session);
        } // if next
    }

    private function handleMessage14(ViberUser $user, Session $session, Request $request)
    {
        Log::debug('handleMessage14');
        if (empty($request['message']) || empty($request['message']['text'])) {
            $this->sendMessage14($user, $session);
            return;
        } // if empty
        if ($request['message']['text'] == 'next') {
            $this->sendMessage15($user, $session);
        } else {
            $this->sendMessage14($user, $session);
        } // if next
    }

    private function handleMessage15(ViberUser $user, Session $session, Request $request)
    {
        Log::debug('handleMessage15');
        if (empty($request['message']) || empty($request['message']['text'])) {
            $this->sendMessage15($user, $session);
            return;
        } // if empty
        if ($request['message']['text'] == 'next') {
            $this->sendMessage6($user, $session);
        } else {
            $this->sendMessage15($user, $session);
        } // if next
    }

    private function handleMessage211(ViberUser $user, Session $session, Request $request)
    {
        Log::debug('handleMessage211');
        if (empty($request['message']) || empty($request['message']['text'])) {
            $this->sendMessage211($user, $session);
            return;
        } // if empty

        if ($request['message']['text'] == 'next') {
            $this->sendMessage212($user, $session);
        } else {
            $this->sendMessage211($user, $session);
        } // if next
    }

    private function handleMessage212(ViberUser $user, Session $session, Request $request)
    {
        Log::debug('handleMessage212');
        if (empty($request['message']) || empty($request['message']['text'])) {
            $this->sendMessage212($user, $session);
            return;
        } // if empty

        if ($request['message']['text'] == 'next') {
            $this->sendMessage21($user, $session);
        } else {
            $this->sendMessage212($user, $session);
        } // if next
    }

    private function handleMessage221(ViberUser $user, Session $session, Request $request)
    {
        Log::debug('handleMessage221');
        if (empty($request['message']) || empty($request['message']['text'])) {
            $this->sendMessage221($user, $session);
            return;
        } // if empty

        if ($request['message']['text'] == 'next') {
            $this->sendMessage222($user, $session);
        } else {
            $this->sendMessage221($user, $session);
        } // if next
    }

    private function handleMessage222(ViberUser $user, Session $session, Request $request)
    {
        Log::debug('handleMessage222');
        if (empty($request['message']) || empty($request['message']['text'])) {
            $this->sendMessage222($user, $session);
            return;
        } // if empty

        if ($request['message']['text'] == 'next') {
            $this->sendMessage223($user, $session);
        } else {
            $this->sendMessage222($user, $session);
        } // if next
    }

    private function handleMessage223(ViberUser $user, Session $session, Request $request)
    {
        Log::debug('handleMessage223');
        if (empty($request['message']) || empty($request['message']['text'])) {
            $this->sendMessage223($user, $session);
            return;
        } // if empty

        if ($request['message']['text'] == 'next') {
            $this->sendMessage21($user, $session);
        } else {
            $this->sendMessage223($user, $session);
        } // if next
    }

    private function handleMessage21(ViberUser $user, Session $session, Request $request)
    {
        Log::debug('handleMessage21');
        if (empty($request['message']) || empty($request['message']['text'])) {
            $this->sendMessage21($user, $session);
            return;
        } // if empty

        if ($request['message']['text'] == 'next') {
            $this->sendMessage22($user, $session);
        } else {
            $this->sendMessage21($user, $session);
        } // if next
    }

    private function handleMessage22(ViberUser $user, Session $session, Request $request)
    {
        Log::debug('handleMessage222');
        if (empty($request['message']) || empty($request['message']['text'])) {
            $this->sendMessage22($user, $session);
            return;
        } // if empty

        if ($request['message']['text'] == 'next') {
            $this->sendMessage23($user, $session);
        } else {
            $this->sendMessage22($user, $session);
        } // if next
    }

    private function handleMessage23(ViberUser $user, Session $session, Request $request)
    {
        Log::debug('handleMessage23');
        if (empty($request['message']) || empty($request['message']['text'])) {
            $this->sendMessage23($user, $session);
            return;
        } // if empty

        if ($request['message']['text'] == 'next') {
            $this->sendMessage6($user, $session);
        } else {
            $this->sendMessage23($user, $session);
        } // if next
    }

    private function GetViberUser($user)
    {
        Log::debug('getViberUser');
        Log::debug($user);
        $viberUser = ViberUser::where('viber_id', $user['id'])->with('session')->first();
        if ($viberUser === null) {
            $viberUser = ViberUser::create([
                'viber_id' => $user['id'],
                'name' => (array_key_exists('name', $user) && is_string($user['name']) && strlen($user['name'] > 0)) ? $user['name'] : 'пользователь неизвестен'
            ]);
        } // if viberUser is null
        return $viberUser;
    }

    private function sendWelcome(ViberUser $user)
    {
        Log::debug('WebhookController->sendWelcome');
        $response = app('viber_bot')->sendMessage((new Text())->setSender((new Sender())->setName(config('viber.bot.name'))
            ->setAvatar(asset('pictures/' . config('viber.bot.avatar'))))
            ->setReceiver($user->viber_id)
            ->setText(__('message.welcome')));
        if ($response !== null) {
            Log::debug($response->getData());
        } // if response
    }

    private function getMessage1(ViberUser $user, $prefix = "") {
                $buttons = array();
        $buttons[] = (new Button())->setText($this->whiteFont(__('message.drug1')))
        ->setActionType('reply')
        ->setActionBody('drug1')
        ->setColumns(3)
        ->setBgMedia(asset('pictures/two.png'))
        ->setSilent(false)
        ->setBgColor(config('viber.keyboard.button_color'));
        $buttons[] = (new Button())->setText($this->whiteFont(__('message.drug2')))
        ->setActionType('reply')
        ->setActionBody('drug2')
        ->setColumns(3)
        ->setBgMedia(asset('pictures/two.png'))
        ->setSilent(false)
        ->setBgColor(config('viber.keyboard.button_color'));
        
        return (new Text())->setSender((new Sender())->setName(config('viber.bot.name'))
            ->setAvatar(asset('pictures/' . config('viber.bot.avatar'))))
            ->setReceiver($user->viber_id)
            ->setText($prefix . __('message.1'))
            ->setKeyboard((new Keyboard())->setBgColor(config('viber.keyboard.bg_color'))
                ->setButtons($buttons));
        }
    
    private function sendMessage1(ViberUser $user, Session $session, $prefix = "")
    {
        Log::debug('WebhookController->sendMessage1');
        $session->last_message_id = 1;
        $session->save();

        $response = app('viber_bot')->sendMessage($this->getMessage1($user, $prefix));
        Log::debug('sendMessage response');
        if ($response !== null) {
            Log::debug($response->getData());
        } // if response not null
    }

    private function sendMessage2(ViberUser $user, Session $session, $prefix = "", $hasPicture = false)
    {
        Log::debug('WebhookController->sendMessage2');
        $session->last_message_id = 2;
        $session->save();

        $buttons = array();
        $buttons[] = (new Button())->setText($this->whiteFont(__('message.stage.1')))
            ->setActionType('reply')
            ->setActionBody('1')
            ->setColumns(3)
            ->setBgMedia(asset('pictures/two.png'))
            ->setSilent(false)
            ->setBgColor(config('viber.keyboard.button_color'));
        $buttons[] = (new Button())->setText($this->whiteFont(__('message.stage.2')))
            ->setActionType('reply')
            ->setActionBody('2')
            ->setColumns(3)
            ->setBgMedia(asset('pictures/two.png'))
            ->setSilent(false)
            ->setBgColor(config('viber.keyboard.button_color'));

        if ($hasPicture) {
            $message = (new Picture())->setMedia(asset('pictures/fortrans.jpg'))->setThumbnail(asset('pictures/fortrans.jpg'));
        } else {
            $message = new Text();
        } // if haspicture
        $response = app('viber_bot')->sendMessage($message->setSender((new Sender())->setName(config('viber.bot.name'))
            ->setAvatar(asset('pictures/' . config('viber.bot.avatar'))))
            ->setReceiver($user->viber_id)
            ->setText($prefix . __('message.2'))
            ->setKeyboard((new Keyboard())->setBgColor(config('viber.keyboard.bg_color'))
            ->setButtons($buttons)));
        if ($response != null) {
            Log::debug($response->getData());
        } // if response not null
    }

    private function sendMessage3(ViberUser $user, Session $session, $prefix = "", $hasPicture = false)
    {
        Log::debug('WebhookController->sendMessage3');
        $session->last_message_id = 3;
        $session->save();

        $month = Carbon::now()->month;
        $buttons = array();
        $buttons[] = (new Button())->setText($this->whiteFont(__('message.month.' . $month)))
            ->setActionType('reply')
            ->setActionBody('0')
            ->setColumns(3)
            ->setBgMedia(asset('pictures/two.png'))
            ->setSilent(false)
            ->setBgColor(config('viber.keyboard.button_color'));
        $buttons[] = (new Button())->setText($this->whiteFont(__('message.month.' . ($month < 12 ? $month + 1 : 1))))
            ->setActionType('reply')
            ->setActionBody('1')
            ->setColumns(3)
            ->setBgMedia(asset('pictures/two.png'))
            ->setSilent(false)
            ->setBgColor(config('viber.keyboard.button_color'));
        if ($hasPicture) {
            $message = (new Picture())->setMedia(asset('pictures/Iziklin.jpg'))->setThumbnail(asset('pictures/Iziklin.jpg'));
        } else {
            $message = new Text();
        } // if haspicture

        $response = app('viber_bot')->sendMessage($message->setSender((new Sender())->setName(config('viber.bot.name'))
            ->setAvatar(asset('pictures/' . config('viber.bot.avatar'))))
            ->setReceiver($user->viber_id)
            ->setText($prefix . __('message.3'))
            ->setKeyboard((new Keyboard())->setBgColor(config('viber.keyboard.bg_color'))
            ->setButtons($buttons)));
        if ($response !== null) {
            Log::debug($response->getData());
        } // if response not null
    }

    private function sendMessage4(ViberUser $user, Session $session, $prefix = "")
    {
        Log::debug('WebhookController->sendMessage4');
        $session->last_message_id = 4;
        $session->save();

        $calendar = $this->makeCalendar($session->procedure_at->copy());
        $today = Carbon::now()->month == $session->procedure_at->month ? Carbon::now()->day : 0;
        $buttons = array();

        for ($y = 0; $y < 7; $y ++) {
            for ($x = 0; $x < count($calendar); $x ++) {
                if ($calendar[$x][$y] > 0) {
                    if ($calendar[$x][$y] > $today) {
                        if ($y < 5) {
                            $fontColor = config('viber.color.black');
                        } else {
                            $fontColor = config('viber.color.red');
                        } // if
                    } else {
                        $fontColor = config('viber.color.gray');
                    } // if today
                    $button = (new Button())->setText("<font color='$fontColor'><b>" . $calendar[$x][$y] . "</b></font>")->setBgMedia(asset('pictures/data.png'));
                } else {
                    $button = (new Button())->setText("");
                } // if empty day
                $buttons[] = $button->setActionType('reply')
                    ->setActionBody($calendar[$x][$y] > $today ? $calendar[$x][$y] : 0)
                    ->setColumns(1)
                    ->setRows(1)
                    ->setBgColor(config('viber.keyboard.button_color'));
            } // for x
        } // for y

        $response = app('viber_bot')->sendMessage((new Text())->setSender((new Sender())->setName(config('viber.bot.name'))
            ->setAvatar(asset('pictures/' . config('viber.bot.avatar'))))
            ->setReceiver($user->viber_id)
            ->setText($prefix . __('message.4'))
            ->setKeyboard((new Keyboard())->setBgColor(config('viber.keyboard.bg_color'))
            ->setButtons($buttons)
            ->setButtonsGroupColumns(count($calendar))
            ->setButtonsGroupRows(7)));
        if ($response !== null) {
            Log::debug($response->getData());
        } // if response not null
    }

    private function sendMessage5(ViberUser $user, Session $session, $prefix = "")
    {
        Log::debug('WebhookController->sendMessage5');
        $session->last_message_id = 5;
        $session->save();
        $buttons = array();
        $buttons[] = (new Button())->setText($this->whiteFont('09:00'))
            ->setActionType('reply')
            ->setActionBody('09:00')
            ->setColumns(3)
            ->setRows(1)
            ->setBgMedia(asset('pictures/two.png'))
            ->setSilent(false)
            ->setBgColor(config('viber.keyboard.button_color'));
        $buttons[] = (new Button())->setText($this->whiteFont('10:00'))
            ->setActionType('reply')
            ->setActionBody('10:00')
            ->setColumns(3)
            ->setRows(1)
            ->setBgMedia(asset('pictures/two.png'))
            ->setSilent(false)
            ->setBgColor(config('viber.keyboard.button_color'));
        $buttons[] = (new Button())->setText($this->whiteFont('11:00'))
            ->setActionType('reply')
            ->setActionBody('11:00')
            ->setColumns(3)
            ->setRows(1)
            ->setBgMedia(asset('pictures/two.png'))
            ->setSilent(false)
            ->setBgColor(config('viber.keyboard.button_color'));
        $buttons[] = (new Button())->setText($this->whiteFont('12:00'))
            ->setActionType('reply')
            ->setActionBody('12:00')
            ->setColumns(3)
            ->setRows(1)
            ->setBgMedia(asset('pictures/two.png'))
            ->setSilent(false)
            ->setBgColor(config('viber.keyboard.button_color'));
        $buttons[] = (new Button())->setText($this->whiteFont('13:30'))
            ->setActionType('reply')
            ->setActionBody('13:30')
            ->setColumns(3)
            ->setRows(1)
            ->setBgMedia(asset('pictures/two.png'))
            ->setSilent(false)
            ->setBgColor(config('viber.keyboard.button_color'));
        $buttons[] = (new Button())->setText($this->whiteFont('14:30'))
            ->setActionType('reply')
            ->setActionBody('14:30')
            ->setColumns(3)
            ->setRows(1)
            ->setBgMedia(asset('pictures/two.png'))
            ->setSilent(false)
            ->setBgColor(config('viber.keyboard.button_color'));
        $buttons[] = (new Button())->setText($this->whiteFont('15:30'))
            ->setActionType('reply')
            ->setActionBody('15:30')
            ->setColumns(3)
            ->setRows(1)
            ->setBgMedia(asset('pictures/two.png'))
            ->setSilent(false)
            ->setBgColor(config('viber.keyboard.button_color'));
        $buttons[] = (new Button())->setText($this->whiteFont('16:00'))
            ->setActionType('reply')
            ->setActionBody('16:00')
            ->setColumns(3)
            ->setRows(1)
            ->setBgMedia(asset('pictures/two.png'))
            ->setSilent(false)
            ->setBgColor(config('viber.keyboard.button_color'));
        $buttons[] = (new Button())->setText($this->whiteFont('17:00'))
            ->setActionType('reply')
            ->setActionBody('17:00')
            ->setColumns(3)
            ->setRows(1)
            ->setBgMedia(asset('pictures/two.png'))
            ->setSilent(false)
            ->setBgColor(config('viber.keyboard.button_color'));
        $buttons[] = (new Button())->setText($this->whiteFont('18:00'))
            ->setActionType('reply')
            ->setActionBody('18:00')
            ->setColumns(3)
            ->setRows(1)
            ->setBgMedia(asset('pictures/two.png'))
            ->setSilent(false)
            ->setBgColor(config('viber.keyboard.button_color'));

        $response = app('viber_bot')->sendMessage((new Text())->setSender((new Sender())->setName(config('viber.bot.name'))
            ->setAvatar(asset('pictures/' . config('viber.bot.avatar'))))
            ->setReceiver($user->viber_id)
            ->setText($prefix . __('message.5'))
            ->setKeyboard((new Keyboard())->setBgColor(config('viber.keyboard.bg_color'))
            ->setButtons($buttons)
            ->setButtonsGroupColumns(6)
            ->setButtonsGroupRows(count($buttons))));
        if ($response !== null) {
            Log::debug($response->getData());
        } // if response not null
    }

    private function sendMessage6(ViberUser $user, Session $session)
    {
        Log::debug('WebhookController->sendMessage6');
        $session->last_message_id = 6;
        $session->save();

        $buttons = array();
        $buttons[] = (new Button())->setText($this->whiteFont(__('message.restart')))
            ->setActionType('reply')
            ->setActionBody('restart')
            ->setSilent(true)
            ->setColumns(6)
            ->setRows(1)
            ->setBgMedia(asset('pictures/one.png'))
            ->setBgColor(config('viber.keyboard.button_color'));
        $response = app('viber_bot')->sendMessage((new Text())->setSender((new Sender())->setName(config('viber.bot.name'))
            ->setAvatar(asset('pictures/' . config('viber.bot.avatar'))))
            ->setReceiver($user->viber_id)
            ->setText(__('message.goodby'))
            ->setKeyboard((new Keyboard())->setBgColor(config('viber.keyboard.bg_color'))
            ->setButtons($buttons)));
        Log::debug('sendMessage response');
        if ($response !== null) {
            Log::debug($response->getData());
        } // if response not null
    }

    private function sendMessage11(ViberUser $user, Session $session)
    {
        Log::debug('WebhookController->sendMessage11');
        $session->last_message_id = 11;
        $session->save();

        $buttons = array();
        $buttons[] = (new Button())->setText($this->whiteFont(__('message.next')))
            ->setActionType('reply')
            ->setActionBody('next')
            ->setSilent(true)
            ->setBgMedia(asset('pictures/one.png'))
            ->setBgColor(config('viber.keyboard.button_color'));
        $response = app('viber_bot')->sendMessage((new Text())->setSender((new Sender())->setName(config('viber.bot.name'))
            ->setAvatar(asset('pictures/' . config('viber.bot.avatar'))))
            ->setReceiver($user->viber_id)
            ->setText(__('message.11'))
            ->setKeyboard((new Keyboard())->setBgColor(config('viber.keyboard.bg_color'))
            ->setButtons($buttons)));
        Log::debug('sendMessage response');
        if ($response !== null) {
            Log::debug($response->getData());
        } // if response not null
    }

    private function sendMessage12(ViberUser $user, Session $session)
    {
        Log::debug('WebhookController->sendMessage12');
        $session->last_message_id = 12;
        $session->save();

        $buttons = array();
        $buttons[] = (new Button())->setText($this->whiteFont(__('message.next')))
            ->setActionType('reply')
            ->setActionBody('next')
            ->setSilent(true)
            ->setBgMedia(asset('pictures/one.png'))
            ->setBgColor(config('viber.keyboard.button_color'));
        $response = app('viber_bot')->sendMessage((new Text())->setSender((new Sender())->setName(config('viber.bot.name'))
            ->setAvatar(asset('pictures/' . config('viber.bot.avatar'))))
            ->setReceiver($user->viber_id)
            ->setText(__('message.12'))
            ->setKeyboard((new Keyboard())->setBgColor(config('viber.keyboard.bg_color'))
            ->setButtons($buttons)));
        Log::debug('sendMessage response');
        if ($response !== null) {
            Log::debug($response->getData());
        } // if response not null
    }

    private function sendMessage13(ViberUser $user, Session $session)
    {
        Log::debug('WebhookController->sendMessage13');
        $session->last_message_id = 13;
        $session->save();

        $buttons = array();
        $buttons[] = (new Button())->setText($this->whiteFont(__('message.next')))
            ->setActionType('reply')
            ->setActionBody('next')
            ->setSilent(true)
            ->setBgMedia(asset('pictures/one.png'))
            ->setBgColor(config('viber.keyboard.button_color'));

        $response = app('viber_bot')->sendMessage((new Text())->setSender((new Sender())->setName(config('viber.bot.name'))
            ->setAvatar(asset('pictures/' . config('viber.bot.avatar'))))
            ->setReceiver($user->viber_id)
            ->setText(__('message.last_time', [
            'time' => $session->procedure_at->subHours(1)
                ->format(config('viber.datetime_format'))
        ]))
            ->setKeyboard((new Keyboard())->setBgColor(config('viber.keyboard.bg_color'))
            ->setButtons($buttons)));
        Log::debug('sendMessage response');
        if ($response !== null) {
            Log::debug($response->getData());
        } // if response not null
    }

    private function sendMessage14(ViberUser $user, Session $session)
    {
        Log::debug('WebhookController->sendMessage14');
        $session->last_message_id = 14;
        $session->save();

        $buttons = array();
        $buttons[] = (new Button())->setText($this->whiteFont(__('message.next')))
            ->setActionType('reply')
            ->setActionBody('next')
            ->setSilent(true)
            ->setBgMedia(asset('pictures/one.png'))
            ->setBgColor(config('viber.keyboard.button_color'));

        $response = app('viber_bot')->sendMessage((new Text())->setSender((new Sender())->setName(config('viber.bot.name'))
            ->setAvatar(asset('pictures/' . config('viber.bot.avatar'))))
            ->setReceiver($user->viber_id)
            ->setText(__('message.14'))
            ->setKeyboard((new Keyboard())->setBgColor(config('viber.keyboard.bg_color'))
            ->setButtons($buttons)));
        Log::debug('sendMessage response');
        if ($response !== null) {
            Log::debug($response->getData());
        } // if response not null
    }

    private function sendMessage15(ViberUser $user, Session $session)
    {
        Log::debug('WebhookController->sendMessage15');
        $session->last_message_id = 15;
        $session->save();

        $buttons = array();
        $buttons[] = (new Button())->setText($this->whiteFont(__('message.next')))
            ->setActionType('reply')
            ->setActionBody('next')
            ->setSilent(true)
            ->setBgMedia(asset('pictures/one.png'))
            ->setBgColor(config('viber.keyboard.button_color'));

        $response = app('viber_bot')->sendMessage((new Text())->setSender((new Sender())->setName(config('viber.bot.name'))
            ->setAvatar(asset('pictures/' . config('viber.bot.avatar'))))
            ->setReceiver($user->viber_id)
            ->setText(__('message.15'))
            ->setKeyboard((new Keyboard())->setBgColor(config('viber.keyboard.bg_color'))
            ->setButtons($buttons)));
        Log::debug('sendMessage response');
        if ($response !== null) {
            Log::debug($response->getData());
        } // if response not null
    }

    private function sendMessage211(ViberUser $user, Session $session)
    {
        Log::debug('WebhookController->sendMessage211');
        $session->last_message_id = 211;
        $session->save();

        $buttons = array();
        $buttons[] = (new Button())->setText($this->whiteFont(__('message.next')))
            ->setActionType('reply')
            ->setActionBody('next')
            ->setSilent(true)
            ->setBgMedia(asset('pictures/one.png'))
            ->setBgColor(config('viber.keyboard.button_color'));

        $response = app('viber_bot')->sendMessage((new Text())->setSender((new Sender())->setName(config('viber.bot.name'))
            ->setAvatar(asset('pictures/' . config('viber.bot.avatar'))))
            ->setReceiver($user->viber_id)
            ->setText(__('message.211'))
            ->setKeyboard((new Keyboard())->setBgColor(config('viber.keyboard.bg_color'))
            ->setButtons($buttons)));
        Log::debug('sendMessage response');
        if ($response !== null) {
            Log::debug($response->getData());
        } // if response not null
    }

    private function sendMessage212(ViberUser $user, Session $session)
    {
        Log::debug('WebhookController->sendMessage212');
        $session->last_message_id = 212;
        $session->save();

        $buttons = array();
        $buttons[] = (new Button())->setText($this->whiteFont(__('message.next')))
            ->setActionType('reply')
            ->setActionBody('next')
            ->setSilent(true)
            ->setBgMedia(asset('pictures/one.png'))
            ->setBgColor(config('viber.keyboard.button_color'));

        $response = app('viber_bot')->sendMessage((new Text())->setSender((new Sender())->setName(config('viber.bot.name'))
            ->setAvatar(asset('pictures/' . config('viber.bot.avatar'))))
            ->setReceiver($user->viber_id)
            ->setText(__('message.last_time', [
            'time' => $session->procedure_at->subHours(3)
                ->format(config('viber.datetime_format'))
        ]))
            ->setKeyboard((new Keyboard())->setBgColor(config('viber.keyboard.bg_color'))
            ->setButtons($buttons)));
        Log::debug('sendMessage response');
        if ($response !== null) {
            Log::debug($response->getData());
        } // if response not null
    }

    private function sendMessage21(ViberUser $user, Session $session)
    {
        Log::debug('WebhookController->sendMessage21');
        $session->last_message_id = 21;
        $session->save();

        $buttons = array();
        $buttons[] = (new Button())->setText($this->whiteFont(__('message.next')))
            ->setActionType('reply')
            ->setActionBody('next')
            ->setSilent(true)
            ->setBgMedia(asset('pictures/one.png'))
            ->setBgColor(config('viber.keyboard.button_color'));

        $response = app('viber_bot')->sendMessage((new Text())->setSender((new Sender())->setName(config('viber.bot.name'))
            ->setAvatar(asset('pictures/' . config('viber.bot.avatar'))))
            ->setReceiver($user->viber_id)
            ->setText(__('message.21'))
            ->setKeyboard((new Keyboard())->setBgColor(config('viber.keyboard.bg_color'))
            ->setButtons($buttons)));
        Log::debug('sendMessage response');
        if ($response !== null) {
            Log::debug($response->getData());
        } // if response not null
    }

    private function sendMessage22(ViberUser $user, Session $session)
    {
        Log::debug('WebhookController->sendMessage22');
        $session->last_message_id = 22;
        $session->save();

        $buttons = array();
        $buttons[] = (new Button())->setText($this->whiteFont(__('message.next')))
            ->setActionType('reply')
            ->setActionBody('next')
            ->setSilent(true)
            ->setBgMedia(asset('pictures/one.png'))
            ->setBgColor(config('viber.keyboard.button_color'));

        $response = app('viber_bot')->sendMessage((new Text())->setSender((new Sender())->setName(config('viber.bot.name'))
            ->setAvatar(asset('pictures/' . config('viber.bot.avatar'))))
            ->setReceiver($user->viber_id)
            ->setText(__('message.22'))
            ->setKeyboard((new Keyboard())->setBgColor(config('viber.keyboard.bg_color'))
            ->setButtons($buttons)));
        Log::debug('sendMessage response');
        if ($response !== null) {
            Log::debug($response->getData());
        } // if response not null
    }

    private function sendMessage23(ViberUser $user, Session $session)
    {
        Log::debug('WebhookController->sendMessage23');
        $session->last_message_id = 23;
        $session->save();

        $buttons = array();
        $buttons[] = (new Button())->setText($this->whiteFont(__('message.next')))
            ->setActionType('reply')
            ->setActionBody('next')
            ->setSilent(true)
            ->setBgMedia(asset('pictures/one.png'))
            ->setBgColor(config('viber.keyboard.button_color'));

        $response = app('viber_bot')->sendMessage((new Text())->setSender((new Sender())->setName(config('viber.bot.name'))
            ->setAvatar(asset('pictures/' . config('viber.bot.avatar'))))
            ->setReceiver($user->viber_id)
            ->setText(__('message.23'))
            ->setKeyboard((new Keyboard())->setBgColor(config('viber.keyboard.bg_color'))
            ->setButtons($buttons)));
        Log::debug('sendMessage response');
        if ($response !== null) {
            Log::debug($response->getData());
        } // if response not null
    }

    private function sendMessage221(ViberUser $user, Session $session)
    {
        Log::debug('WebhookController->sendMessage221');
        $session->last_message_id = 221;
        $session->save();

        $buttons = array();
        $buttons[] = (new Button())->setText($this->whiteFont(__('message.next')))
            ->setActionType('reply')
            ->setActionBody('next')
            ->setSilent(true)
            ->setBgMedia(asset('pictures/one.png'))
            ->setBgColor(config('viber.keyboard.button_color'));

        $response = app('viber_bot')->sendMessage((new Text())->setSender((new Sender())->setName(config('viber.bot.name'))
            ->setAvatar(asset('pictures/' . config('viber.bot.avatar'))))
            ->setReceiver($user->viber_id)
            ->setText(__('message.221'))
            ->setKeyboard((new Keyboard())->setBgColor(config('viber.keyboard.bg_color'))
            ->setButtons($buttons)));
        Log::debug('sendMessage response');
        if ($response !== null) {
            Log::debug($response->getData());
        } // if response not null
    }

    private function sendMessage222(ViberUser $user, Session $session)
    {
        Log::debug('WebhookController->sendMessage222');
        $session->last_message_id = 222;
        $session->save();

        $buttons = array();
        $buttons[] = (new Button())->setText($this->whiteFont(__('message.next')))
            ->setActionType('reply')
            ->setActionBody('next')
            ->setSilent(true)
            ->setBgMedia(asset('pictures/one.png'))
            ->setBgColor(config('viber.keyboard.button_color'));

        $response = app('viber_bot')->sendMessage((new Text())->setSender((new Sender())->setName(config('viber.bot.name'))
            ->setAvatar(asset('pictures/' . config('viber.bot.avatar'))))
            ->setReceiver($user->viber_id)
            ->setText(__('message.222'))
            ->setKeyboard((new Keyboard())->setBgColor(config('viber.keyboard.bg_color'))
            ->setButtons($buttons)));
        Log::debug('sendMessage response');
        if ($response !== null) {
            Log::debug($response->getData());
        } // if response not null
    }

    private function sendMessage223(ViberUser $user, Session $session)
    {
        Log::debug('WebhookController->sendMessage223');
        $session->last_message_id = 223;
        $session->save();

        $buttons = array();
        $buttons[] = (new Button())->setText($this->whiteFont(__('message.next')))
            ->setActionType('reply')
            ->setActionBody('next')
            ->setSilent(true)
            ->setBgMedia(asset('pictures/one.png'))
            ->setBgColor(config('viber.keyboard.button_color'));

        $response = app('viber_bot')->sendMessage((new Text())->setSender((new Sender())->setName(config('viber.bot.name'))
            ->setAvatar(asset('pictures/' . config('viber.bot.avatar'))))
            ->setReceiver($user->viber_id)
            ->setText(__('message.last_time', [
            'time' => $session->procedure_at->subHours(3)
                ->format(config('viber.datetime_format'))
        ]))
            ->setKeyboard((new Keyboard())->setBgColor(config('viber.keyboard.bg_color'))
            ->setButtons($buttons)));
        Log::debug('sendMessage response');
        if ($response !== null) {
            Log::debug($response->getData());
        } // if response not null
    }

    private function makeCalendar($procedureAt)
    {
        $daysInMonth = $procedureAt->daysInMonth;
        $y = $procedureAt->startOfMonth()->dayOfWeek - 1;
        $calendar = array();
        $x = 0;
        $calendar[$x] = array();
        for ($i = 0; $i < $y; $i ++) {
            $calendar[$x][$i] = 0;
        } // for i
        $z = 1;
        while ($z <= $daysInMonth) {
            $calendar[$x][$y] = $z;
            $z ++;
            if ($y == 6) {
                $x ++;
                $y = 0;
            } else {
                $y ++;
            } // if y
        } // while z

        for ($i = $y; $i < 7; $i ++) {
            $calendar[$x][$i] = 0;
        } // for i

        return $calendar;
    }

    private function whiteFont($text)
    {
        return "<font color='" . config('viber.color.white') . "'><b>$text</b></font>";
    }

    private function recognizeDrug($drug)
    {
        if ($drug == 'drug1' || $drug == 'drug2') {
            return $drug;
        } // if code
        if (in_array($drug, config('vocabulary.drug1'))) {
            return 'drug1';
        } elseif (in_array($drug, config('vocabulary.drug2'))) {
            return 'drug2';
        } // if recognize
        return "";
    }

    private function recognizeStageNum($stageNum)
    {
        if (ctype_digit((string) $stageNum)) {
            return (int) $stageNum;
        } elseif (in_array($stageNum, config('vocabulary.stage1'))) {
            return 1;
        } elseif (in_array($stageNum, config('vocabulary.stage2'))) {
            return 2;
        } // if vocabulary
        return 0;
    }

    public function recognizeMonth($month)
    {
        if (ctype_digit((string) $month)) {
            return (int) $month;
        } // if digit
        $m = Carbon::now()->month;
        if (in_array($month, config("vocabulary.month.$m"))) {
            return 0;
        } // if m0
        $m = $m == 12 ? 1 : $m + 1;
        if (in_array($month, config("vocabulary.month.$m"))) {
            return 1;
        } // if m1
        return - 1;
    }
}
