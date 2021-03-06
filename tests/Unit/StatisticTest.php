<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Drug;
use App\Models\ViberUser;
use App\Models\Session;
use Carbon\Carbon;
use Log;

class StatisticTest extends TestCase
{
use DatabaseTransactions;

private $viberUser;

    public function setUp() {
    Parent::setUp();

    $viberId = 1;
    while(ViberUser::where('viber_id', (string) $viberId)->count() > 0) {
        $viberId++;
    }//while
    $this->viberUser = ViberUser::create([
        'viber_id' => (string) $viberId,
        'name' => 'test user',
        'subscribed' => true
    ]);
    }
    
    public function testMonthStatistic() {
        $now = Carbon::now();
        Log::debug("format\n"
            .$now->format('d.m.Y H:i:s'));
                try {
        $now = Carbon::now()->startOfMonth();
        $daysInMonth = $now->daysInMonth;
        $drug1 = Drug::where('code', 'drug1')->first();
        $drug2 = Drug::where('code', 'drug2')->first();
        $drugChooser = true;
        for($i = 0; $i < $daysInMonth; $i++) {
            $session = Session::create([
            'user_id' => $this->viberUser->id,
            'drug_id' => $drugChooser ? $drug1->id : $drug2->id,
            'stage_num' => 2,
            'last_message_id' => 6,
            'procedure_at' => $now,
        ]);
                        $now = $now->addDay();
        $drugChooser = !$drugChooser;
        $this->viberUser->completed_session_id = $session->id;
        $this->viberUser->save();
        }//for
        $now = Carbon::now();
                $statistic = monthStatistic($now);
                Log::debug('month statistic');
                                Log::debug($statistic);
                                } catch(\Wxception $e) {
            Log::error($e);
        }//catch
        }
        
        public function testTotalDrugStatistic() {
            $now = Carbon::now();
            Log::debug("format\n"
                .$now->format('d.m.Y H:i:s'));
            try {
                $now = Carbon::now()->startOfMonth();
                $daysInMonth = $now->daysInMonth;
                $drug1 = Drug::where('code', 'drug1')->first();
                $drug2 = Drug::where('code', 'drug2')->first();
                $drugChooser = true;
                for($i = 0; $i < $daysInMonth; $i++) {
                    $session = Session::create([
                        'user_id' => $this->viberUser->id,
                        'drug_id' => $drugChooser ? $drug1->id : $drug2->id,
                        'stage_num' => 2,
                        'last_message_id' => 6,
                        'procedure_at' => $now,
                    ]);
                    $now = $now->addDay();
                    $drugChooser = !$drugChooser;
                    $this->viberUser->completed_session_id = $session->id;
                    $this->viberUser->save();
                }//for
                $now = Carbon::now();
                $statistic = totalDrugStatistic();
                Log::debug('total drug statistic');
                Log::debug($statistic);
            } catch(\Wxception $e) {
                Log::error($e);
            }//catch
        }
        
    }