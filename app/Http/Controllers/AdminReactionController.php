<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Option;
use Illuminate\Http\Request;
use App\Services\PointLedgerService;
use Illuminate\Support\Facades\DB;

class AdminReactionController extends Controller
{
    public function index()
    {
        $reactions = Like::select('like.*', 'options.name as emoji')
            ->leftJoin('options', function($join) {
                $join->on('options.o_parent', '=', 'like.id')
                     ->where('options.o_type', '=', 'data_reaction');
            })
            ->with(['user'])
            ->orderBy('like.id', 'desc')
            ->paginate(20);

        return view('admin::admin.reactions.index', compact('reactions'));
    }

    public function destroy($id, PointLedgerService $pointLedger)
    {
        $like = Like::findOrFail($id);
        $uid = $like->uid;
        $sid = $like->sid;
        $type = $like->type;

        DB::beginTransaction();
        try {
            // Delete the option record
            Option::where('o_parent', $id)->where('o_type', 'data_reaction')->delete();
            
            // Delete the like record
            $like->delete();

            // Deduct points (optional, but consistent with legacy logic)
            // Legacy: deducting points from the one who gave the reaction
            $pointLedger->award($uid, -2, 'reaction_deleted_by_admin', 'reaction_deleted', 'reaction', $id);

            DB::commit();
            return redirect()->back()->with('success', __('messages.settings_saved'));
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return redirect()->back()->with('error', __('messages.error_occurred'));
        }
    }
}
