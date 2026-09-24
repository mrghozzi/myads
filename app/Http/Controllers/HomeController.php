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
        
        $totalReferrals = \App\Models\Referral::where('uid', $user->id)->count();
        $referralStats = [
            'total' => $totalReferrals,
            'pts' => $totalReferrals * 10,
        ];
        
        $site_settings = Setting::first();

        try {
            $vouchers = PtsVoucher::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
            $vouchers = collect();
            \Log::error('Failed to load vouchers on home page: ' . $e->getMessage());
        }

        $smartDivisor = SmartAdsSettings::pointsDivisor();

        return view('theme::home', compact('user', 'bannerStats', 'linkStats', 'visitStats', 'smartAdStats', 'referralStats', 'site_settings', 'vouchers', 'smartDivisor'));
    }

    public function convertPoints(Request $request)
    {
        $user = Auth::user();
        $points = (int) $request->input('pts');
        $type = $request->input('to');
        $isAjax = $request->ajax() || $request->wantsJson();

        // SECURITY: Validate type against whitelist before doing anything
        $validTypes = ['link', 'banners', 'exchv', 'smartads'];
        if (!in_array($type, $validTypes, true)) {
            $err = __('messages.invalid_conversion_type');
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => $err], 422);
            }
            return redirect()->back()->with('errMSG', $err);
        }

        // Validation
        if ($points <= 0) {
            $err = __('messages.points_must_be_positive');
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => $err], 422);
            }
            return redirect()->back()->with('errMSG', $err);
        }

        // SECURITY: Use DB transaction with pessimistic locking to prevent race conditions / double-spend
        try {
            return DB::transaction(function () use ($user, $points, $type, $isAjax) {
                // Re-read user with lock to prevent concurrent manipulation
                $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

                if ($lockedUser->pts < $points) {
                    $err = __('messages.insufficient_points', ['current' => number_format($lockedUser->pts, 2)]);
                    if ($isAjax) {
                        return response()->json(['success' => false, 'message' => $err], 422);
                    }
                    return redirect()->back()->with('errMSG', $err);
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
                $le_go = 0;
                $msg = '';

                if ($type == "link") {
                    $le_go = $points / 2;
                    $lockedUser->nlink += $le_go;
                    $lockedUser->pts -= $points;
                    $lockedUser->save();
                    
                    $msg = __('messages.points_converted_link', [
                        'points' => $points,
                        'amount' => number_format($le_go, 0),
                    ]);

                } elseif ($type == "banners") {
                    $le_go = $points / 2;
                    $lockedUser->nvu += $le_go;
                    $lockedUser->pts -= $points;
                    $lockedUser->save();

                    $msg = __('messages.points_converted_banners', [
                        'points' => $points,
                        'amount' => number_format($le_go, 0),
                    ]);

                } elseif ($type == "exchv") {
                    $le_go = $points / 4;
                    $lockedUser->vu += $le_go;
                    $lockedUser->pts -= $points;
                    $lockedUser->save();

                    $msg = __('messages.points_converted_visits', [
                        'points' => $points,
                        'amount' => number_format($le_go, 0),
                    ]);

                } elseif ($type == "smartads") {
                    $divisor = SmartAdsSettings::pointsDivisor();
                    $le_go = $points / $divisor;
                    $lockedUser->nsmart += $le_go;
                    $lockedUser->pts -= $points;
                    $lockedUser->save();

                    $msg = __('messages.smart_points_converted', [
                        'points' => $points,
                        'credits' => rtrim(rtrim(number_format($le_go, 2, '.', ''), '0'), '.'),
                    ]);
                }

                if ($isAjax) {
                    return response()->json([
                        'success' => true,
                        'message' => $msg,
                        'points_spent' => $points,
                        'amount_gained' => $le_go,
                        'target_type' => $type,
                        'balances' => [
                            'pts' => (float) $lockedUser->pts,
                            'nlink' => (float) $lockedUser->nlink,
                            'nvu' => (float) $lockedUser->nvu,
                            'vu' => (float) $lockedUser->vu,
                            'nsmart' => (float) $lockedUser->nsmart,
                        ]
                    ]);
                }

                return redirect()->route('dashboard')->with('MSG', $msg);
            });
        } catch (\Exception $e) {
            \Log::error('Point Conversion Error: ' . $e->getMessage());
            $err = __('messages.error_occurred');
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => $err], 500);
            }
            return redirect()->route('dashboard')->with('errMSG', $err);
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
        $isAjax = $request->ajax() || $request->wantsJson();

        if (strtolower($sender->username) === strtolower($recipientUsername)) {
            $err = __('messages.cannot_transfer_to_self');
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => $err], 422);
            }
            return redirect()->back()->with('errMSG', $err);
        }

        try {
            return DB::transaction(function () use ($sender, $recipientUsername, $amount, $ledger, $notifications, $isAjax) {
                // Lock sender
                $lockedSender = User::where('id', $sender->id)->lockForUpdate()->first();
                if ($lockedSender->pts < $amount) {
                    $err = __('messages.insufficient_points', ['current' => number_format($lockedSender->pts, 2)]);
                    if ($isAjax) {
                        return response()->json(['success' => false, 'message' => $err], 422);
                    }
                    return redirect()->back()->with('errMSG', $err);
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

                $lockedSender->refresh();

                if ($isAjax) {
                    return response()->json([
                        'success' => true,
                        'message' => $successMsg,
                        'amount' => $amount,
                        'recipient' => $lockedRecipient->username,
                        'balances' => [
                            'pts' => (float) $lockedSender->pts,
                        ]
                    ]);
                }

                return redirect()->route('dashboard')->with('MSG', $successMsg);
            });
        } catch (\Exception $e) {
            \Log::error('PTS Transfer Error: ' . $e->getMessage());
            $err = __('messages.error_occurred');
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => $err], 500);
            }
            return redirect()->route('dashboard')->with('errMSG', $err);
        }
    }

    public function generateVoucher(Request $request, PointLedgerService $ledger)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $user = Auth::user();
        $amount = (float) $request->input('amount');
        $isAjax = $request->ajax() || $request->wantsJson();

        try {
            return DB::transaction(function () use ($user, $amount, $ledger, $isAjax) {
                $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

                if ($lockedUser->pts < $amount) {
                    $err = __('messages.insufficient_points', ['current' => number_format($lockedUser->pts, 2)]);
                    if ($isAjax) {
                        return response()->json(['success' => false, 'message' => $err], 422);
                    }
                    return redirect()->back()->with('errMSG', $err);
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

                $lockedUser->refresh();

                if ($isAjax) {
                    return response()->json([
                        'success' => true,
                        'message' => $successMsg,
                        'balances' => [
                            'pts' => (float) $lockedUser->pts,
                        ],
                        'voucher' => [
                            'id' => $voucher->id,
                            'code' => $voucher->code,
                            'amount' => (float) $voucher->amount,
                            'is_used' => false,
                            'created_at' => 'Just now',
                        ]
                    ]);
                }

                return redirect()->route('dashboard')->with('MSG', $successMsg);
            });
        } catch (\Exception $e) {
            \Log::error('Voucher Generation Error: ' . $e->getMessage());
            $err = __('messages.error_occurred');
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => $err], 500);
            }
            return redirect()->route('dashboard')->with('errMSG', $err);
        }
    }

    public function claimVoucher(Request $request, PointLedgerService $ledger, NotificationService $notifications)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $user = Auth::user();
        $code = strtoupper(trim($request->input('code')));
        $isAjax = $request->ajax() || $request->wantsJson();

        try {
            return DB::transaction(function () use ($user, $code, $ledger, $notifications, $isAjax) {
                // Find voucher with lock
                $voucher = PtsVoucher::where('code', $code)->lockForUpdate()->first();

                if (!$voucher) {
                    $err = __('messages.invalid_voucher_code');
                    if ($isAjax) {
                        return response()->json(['success' => false, 'message' => $err], 422);
                    }
                    return redirect()->back()->with('errMSG', $err);
                }

                if ($voucher->is_used) {
                    $err = __('messages.voucher_already_used');
                    if ($isAjax) {
                        return response()->json(['success' => false, 'message' => $err], 422);
                    }
                    return redirect()->back()->with('errMSG', $err);
                }

                $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

                // Award to user
                $generatorName = $voucher->generator ? $voucher->generator->username : 'System';
                $ledger->award(
                    $lockedUser,
                    $voucher->amount,
                    'voucher_claimed',
                    'voucher_claimed_desc',
                    PtsVoucher::class,
                    $voucher->id,
                    ['generator_username' => $generatorName],
                    true
                );

                // Mark voucher as used
                $voucher->update([
                    'is_used' => true,
                    'used_by' => $lockedUser->id,
                    'used_at' => now(),
                ]);

                // Notify Generator if not self
                if ($voucher->generator && $voucher->generator->id !== $lockedUser->id) {
                    $msg = __('messages.voucher_claimed_by', ['amount' => $voucher->amount, 'claimer' => $lockedUser->username]);
                    $notifications->send(
                        $voucher->generator,
                        $msg,
                        url('/history'),
                        'item'
                    );
                }

                $successMsg = __('messages.voucher_claimed_success', ['amount' => $voucher->amount]);

                $lockedUser->refresh();

                if ($isAjax) {
                    return response()->json([
                        'success' => true,
                        'message' => $successMsg,
                        'amount' => (float) $voucher->amount,
                        'code' => $voucher->code,
                        'balances' => [
                            'pts' => (float) $lockedUser->pts,
                        ]
                    ]);
                }

                return redirect()->route('dashboard')->with('MSG', $successMsg);
            });
        } catch (\Exception $e) {
            \Log::error('Voucher Claim Error: ' . $e->getMessage());
            $err = __('messages.error_occurred');
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => $err], 500);
            }
            return redirect()->route('dashboard')->with('errMSG', $err);
        }
    }
}

