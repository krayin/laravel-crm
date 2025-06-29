<?php

namespace App\Http\Controllers;

use App\Models\RelatedContact;
use Illuminate\Http\Request;

class RelatedContactController extends Controller
{

    public function store(Request $request)
    {

        $relatedContact = RelatedContact::create([
            'person_id' => $request->person_id,
            'name' => $request->name,
            'type' => $request->type ?? null,
            'eid_expiry' =>$request->eid_expiry ?? null,
            'mobile_numbers' => json_encode($request->mobile_numbers ?? []),
            'emails' => json_encode($request->emails ?? []),
        ]);

        return response()->json([
            'message' => 'Related contact created successfully.',
            'relatedContact' => $relatedContact,
        ]);
    }

    public function update(Request $request, RelatedContact $relatedContact)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string',
            'eid_expiry' => 'nullable|date',
            'mobile_numbers' => 'nullable|array',
            'emails' => 'nullable|array',
        ]);

        $relatedContact->update([
            'name' => $validated['name'],
            'type' => $validated['type'] ?? null,
            'eid_expiry' => $validated['eid_expiry'] ?? null,
            'mobile_numbers' => json_encode($validated['mobile_numbers'] ?? []),
            'emails' => json_encode($validated['emails'] ?? []),
        ]);

        return response()->json(['message' => 'Related contact updated successfully.']);
    }

    public function destroy(RelatedContact $relatedContact)
    {
        $relatedContact->delete();

        return response()->json(['message' => 'Related contact deleted successfully.']);
    }
}
