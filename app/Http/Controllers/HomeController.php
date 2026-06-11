<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Banner;
use App\Models\Link;
use App\Models\Visit;
use App\Models\SmartAd;
use App\Models\Option;
use App\Models\Setting;
use App\Models\User;
use App\Support\SmartAdsSettings;
use App\Models\PtsVoucher;
use App\Services\PointLedgerService;
use App\Services\NotificationService;
use Illuminate\Support\Str;
class HomeController extends Controller
{
    public function index()
    {
        $this->noindex([
            'scope_key' => 'dashboard',
        ]);

        $user = Auth::user();
        
        $bannerStats = [
            'vu' => Banner::where('uid', $user->id)->sum('vu'),
            'clik' => Banner::where('uid', $user->id)->sum('clik'),
        ];
        
        $linkStats = [
            'clik' => Link::where('uid', $user->id)->sum('clik'),
        ];
        
        $visitStats = [
            'vu' => Visit::where('uid', $user->id)->sum('vu'),
        ];

        $smartAdStats = [
            'impressions' => SmartAd::where('uid', $user->id)->sum('impressions'),
            'clicks' => SmartAd::where('uid', $user->id)->sum('clicks'),
            'total' => SmartAd::where('uid', $user->id)->count(),
        ];
        
        $site_settings = Setting::first();

        $vouchers = PtsVoucher::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        return view('theme::home', compact('user', 'bannerStats', 'linkStats', 'visitStats', 'smartAdStats', 'site_settings', 'vouchers'));
    }

    public function convertPoints(Request $request)
    {
        $user = Auth::user();
        $points = (int) $request->input('pts');
        $type = $request->input('to');

        // SECURITY: Validate type against whitelist before doing anything
        $validTypes = ['link', 'banners', 'exchv', 'smartads'];
        if (!in_array($type, $validTypes, true)) {
            return redirect()->back()->with('errMSG', __('messages.invalid_conversion_type'));
        }

        // Validation
        if ($points <= 0) {
            return redirect()->back()->with('errMSG', __('cnc0p'));
        }

        // SECURITY: Use DB transaction with pessimistic locking to prevent race conditions / double-spend
        try {
            return DB::transaction(function () use ($user, $points, $type) {
                // Re-read user with lock to prevent concurrent manipulation
                $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

                if ($lockedUser->pts < $points) {
                    return redirect()->back()->with('errMSG', __('tnopmtrnon') . " : " . $lockedUser->pts);
                }

                // Determine operation details
                $o_type = "hest_pts";
                $bn_desc = "-" . $points;
                $bn_name = "";

                if ($type == "link") {
                    $bn_name = "tostads";
                } elseif ($type == "banners") {
                    $bn_name = "towthbaner";
                } elseif ($type == "exchv") {
                    $bn_name = "toexchvisi";
                } elseif ($type == "smartads") {
                    $bn_name = "tosmartads";
                }

                // Insert into options
                Option::create([
                    'name' => $bn_name,
                    'o_valuer' => $bn_desc,
                    'o_type' => $o_type,
                    'o_parent' => $lockedUser->id,
                    'o_order' => 0,
                    'o_mode' => time(),
                ]);

                // Update User Points and Stats atomically
                if ($type == "link") {
                    $le_go = $points / 2;
                    $lockedUser->nlink += $le_go;
                    $lockedUser->pts -= $points;
                    $lockedUser->save();
                    
                    $msg = str_replace("[le_go]", $le_go, __('phbdp'));
                    $msg = str_replace("[le_name]", $points, $msg);
                    return redirect()->route('dashboard')->with('MSG', $msg);

                } elseif ($type == "banners") {
                    $le_go = $points / 2;
                    $lockedUser->nvu += $le_go;
                    $lockedUser->pts -= $points;
                    $lockedUser->save();

                    $msg = str_replace("[le_go]", $le_go, __('phbdb'));
                    $msg = str_replace("[le_name]", $points, $msg);
                    return redirect()->route('dashboard')->with('MSG', $msg);

                } elseif ($type == "exchv") {
                    $le_go = $points / 4;
                    $lockedUser->vu += $le_go;
                    $lockedUser->pts -= $points;
                    $lockedUser->save();

                    $msg = str_replace("[le_go]", $le_go, __('phbdv'));
                    $msg = str_replace("[le_name]", $points, $msg);
                    return redirect()->route('dashboard')->with('MSG', $msg);
                } elseif ($type == "smartads") {
                    $le_go = $points / SmartAdsSettings::pointsDivisor();
                    $lockedUser->nsmart += $le_go;
                    $lockedUser->pts -= $points;
                    $lockedUser->save();

                    $msg = __('messages.smart_points_converted', [
                        'points' => $points,
                        'credits' => rtrim(rtrim(number_format($le_go, 2, '.', ''), '0'), '.'),
                    ]);
                    return redirect()->route('dashboard')->with('MSG', $msg);
                }

                return redirect()->route('dashboard');
            });
        } catch (\Exception $e) {
            \Log::error('Point Conversion Error: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('errMSG', __('messages.error_occurred'));
        }
    }

    public function transferPts(Request $request, PointLedgerService $ledger, NotificationService $notifications)
    {
        $request->validate([
            'username' => 'required|string|exists:users,username',
            'amount' => 'required|numeric|min:1'
        ]);

        $sender = Auth::user();
        $amount = (float) $request->input('amount');
        $recipientUsername = $request->input('username');

        if (strtolower($sender->username) === strtolower($recipientUsername)) {
            return redirect()->back()->with('errMSG', __('messages.cannot_transfer_to_self'));
        }

        try {
            return DB::transaction(function () use ($sender, $recipientUsername, $amount, $ledger, $notifications) {
                // Lock sender
                $lockedSender = User::where('id', $sender->id)->lockForUpdate()->first();
                if ($lockedSender->pts < $amount) {
                    return redirect()->back()->with('errMSG', __('tnopmtrnon') . " : " . $lockedSender->pts);
                }

                // Lock recipient
                $lockedRecipient = User::where('username', $recipientUsername)->lockForUpdate()->first();

                // Deduct from sender
                $ledger->award(
                    $lockedSender,
                    -$amount,
                    'transfer_sent',
                    'transfer_sent_desc',
                    'user',
                    $lockedRecipient->id,
                    ['recipient_username' => $lockedRecipient->username],
                    true
                );

                // Add to recipient
                $ledger->award(
                    $lockedRecipient,
                    $amount,
                    'transfer_received',
                    'transfer_received_desc',
                    'user',
                    $lockedSender->id,
                    ['sender_username' => $lockedSender->username],
                    true
                );

                // Send Notification
                $msg = __('messages.received_pts_transfer', ['amount' => $amount, 'sender' => $lockedSender->username]);
                $notifications->send(
                    $lockedRecipient,
                    $msg,
                    url('/history'),
                    'item'
                );

                $successMsg = __('messages.transfer_successful', ['amount' => $amount, 'recipient' => $lockedRecipient->username]);

                return redirect()->route('dashboard')->with('MSG', $successMsg);
            });
        } catch (\Exception $e) {
            \Log::error('PTS Transfer Error: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('errMSG', __('messages.error_occurred'));
        }
    }

    public function generateVoucher(Request $request, PointLedgerService $ledger)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $user = Auth::user();
        $amount = (float) $request->input('amount');

        try {
            return DB::transaction(function () use ($user, $amount, $ledger) {
                $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
                if ($lockedUser->pts < $amount) {
                    return redirect()->back()->with('errMSG', __('tnopmtrnon') . " : " . $lockedUser->pts);
                }

                $code = strtoupper(Str::random(12));

                // Deduct from user
                $transaction = $ledger->award(
                    $lockedUser,
                    -$amount,
                    'voucher_generated',
                    'voucher_generated_desc',
                    'voucher',
                    null,
                    ['code' => $code],
                    true
                );

                // Create Voucher
                $voucher = PtsVoucher::create([
                    'user_id' => $lockedUser->id,
                    'code' => $code,
                    'amount' => $amount,
                    'is_used' => false,
                ]);

                // Update ledger reference
                $transaction->update([
                    'reference_type' => PtsVoucher::class,
                    'reference_id' => $voucher->id
                ]);

                $successMsg = __('messages.voucher_generated_success');

                return redirect()->route('dashboard')->with('MSG', $successMsg);
            });
        } catch (\Exception $e) {
            \Log::error('Voucher Generation Error: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('errMSG', __('messages.error_occurred'));
        }
    }

    public function claimVoucher(Request $request, PointLedgerService $ledger, NotificationService $notifications)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $user = Auth::user();
        $code = trim($request->input('code'));

        try {
            return DB::transaction(function () use ($user, $code, $ledger, $notifications) {
                // Find voucher with lock
                $voucher = PtsVoucher::where('code', $code)->lockForUpdate()->first();

                if (!$voucher) {
                    return redirect()->back()->with('errMSG', __('messages.invalid_voucher_code'));
                }

                if ($voucher->is_used) {
                    return redirect()->back()->with('errMSG', __('messages.voucher_already_used'));
                }

                $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

                // Award to user
                $ledger->award(
                    $lockedUser,
                    $voucher->amount,
                    'voucher_claimed',
                    'voucher_claimed_desc',
                    PtsVoucher::class,
                    $voucher->id,
                    ['generator_username' => $voucher->generator->username],
                    true
                );

                // Mark voucher as used
                $voucher->update([
                    'is_used' => true,
                    'used_by' => $lockedUser->id,
                    'used_at' => now(),
                ]);

                // Notify Generator
                $msg = __('messages.voucher_claimed_by', ['amount' => $voucher->amount, 'claimer' => $lockedUser->username]);
                $notifications->send(
                    $voucher->generator,
                    $msg,
                    url('/history'),
                    'item'
                );

                $successMsg = __('messages.voucher_claimed_success', ['amount' => $voucher->amount]);

                return redirect()->route('dashboard')->with('MSG', $successMsg);
            });
        } catch (\Exception $e) {
            \Log::error('Voucher Claim Error: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('errMSG', __('messages.error_occurred'));
        }
    }
}
