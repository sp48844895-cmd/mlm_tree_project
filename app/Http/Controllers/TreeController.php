<?php

namespace App\Http\Controllers;

use App\Models\MlmTree;
use App\Models\User;
use Illuminate\Http\Request;

class TreeController extends Controller
{
    public function index()
    {
        $parents = MlmTree::whereNull('parent_id')->get();

        $tree = [];

        foreach ($parents as $parent) {
            $tree[] = $this->getNodeData($parent, 0);
        }

        return view('tree.index', [
            'tree' => $tree,
            'isAuthenticated' => auth()->check(),
            'userReferralLink' => $this->getReferralLink(),
        ]);
    }

    public function place(User $user, User $referrer = null)
    {
        $alreadyExists = MlmTree::where('user_id', $user->id)->first();

        if ($alreadyExists) {
            return;
        }

        $parentId = null;

        if ($referrer) {
            $referrerNode = MlmTree::firstOrCreate(
                ['user_id' => $referrer->id],
                ['parent_id' => null]
            );

            $parentId = $referrerNode->id;
        }

        MlmTree::create([
            'user_id' => $user->id,
            'parent_id' => $parentId,
        ]);
    }

    private function getNodeData($node, $level)
    {
        $user = User::find($node->user_id);

        $childrenNodes = MlmTree::where('parent_id', $node->id)->get();

        $children = [];
        $descendantsCount = 0;

        foreach ($childrenNodes as $childNode) {
            $childData = $this->getNodeData($childNode, $level + 1);
            $children[] = $childData;
            $descendantsCount += 1 + $childData['descendants'];
        }

        return [
            'user' => $user,
            'level' => $level,
            'children' => $children,
            'direct_count' => count($children),
            'descendants' => $descendantsCount,
        ];
    }

    private function getReferralLink()
    {
        $user = auth()->user();

        if (!$user || !$user->referral_code) {
            return null;
        }

        return route('register', ['ref' => $user->referral_code]);
    }
}

