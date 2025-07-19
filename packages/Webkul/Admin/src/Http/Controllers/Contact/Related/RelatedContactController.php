<?php

namespace Webkul\Admin\Http\Controllers\Contact\Related;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Contact\RelatedContactDataGrid;
use Webkul\Contact\Models\RelatedContact;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\Contact\Repositories\RelatedContactRepository;

class RelatedContactController extends Controller
{

    public function __construct(protected RelatedContactRepository $relatedContactRepository)
    {
        request()->request->add(['entity_type' => 'relatedContact']);
    }
    public function index()
    {
        if (request()->ajax()) {
            return datagrid(RelatedContactDataGrid::class)->process();
        }
        return view('admin::contacts.related.index');

    }
    private function ensureJsonArr($input) {
        // If input is already an array or object, return as is
        if (is_array($input) || is_object($input)) {
            return $input;
        }

        // If input is a string, check if it's valid JSON
        if (is_string($input)) {
            $decoded = json_decode($input, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // Valid JSON string, return decoded array/object
                return $decoded;
            } else {
                // Not valid JSON, treat as plain string: wrap in array
                return [];
            }
        }

        // For anything else, return as is (or you can customize)
        return [];
    }

    public function store(Request $request)
    {
        $redirect = $request->boolean('redirect');

        if(!empty($request->mobile_number) && empty($request->mobile_numbers)){
            $request->mobile_numbers=[$request->mobile_number];
        }

        if(!empty($request->email) && empty($request->emails)){
            $request->emails=[$request->email];
        }

        $mobile_numbers =$this->ensureJsonArr($request->mobile_numbers);
        $emails =$this->ensureJsonArr($request->emails);




        $relatedContact = RelatedContact::create([
            'person_id' => $request->person_id,
            'name' => $request->name,
            'type' => $request->type ?? null,
            'eid_expiry' =>$request->eid_expiry ?? null,
            'mobile_numbers' => json_encode($mobile_numbers),
            'emails' => json_encode($emails),
        ]);

        if ($redirect) {
            return redirect()->route('admin.contacts.related-contacts.index')
                ->with('success', 'Related contact created successfully.');
        }

        return response()->json([
            'message' => 'Related contact created successfully.',
            'relatedContact' => $relatedContact,
        ]);
    }

    public function update(Request $request, RelatedContact $relatedContact)
    {
        $redirect = $request->boolean('redirect');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'string',
        ]);

        if( !empty($request->mobile_number) && $request->mobile_number !=="+971" && (empty($request->mobile_numbers) || $request->mobile_numbers="[]")){
            $request->mobile_numbers=[$request->mobile_number];
        }

        if(!empty($request->email) && ( empty($request->emails) || $request->emails="[]")){
            $request->emails=[$request->email];
        }

        $mobile_numbers =$this->ensureJsonArr($request->mobile_numbers);
        $emails =$this->ensureJsonArr($request->emails);


        $relatedContact->update([
            'name' => $validated['name'],
            'type' => $validated['type'] ?? null,
            'eid_expiry' => $request->eid_expiry ?? null,
            'mobile_numbers' => json_encode($mobile_numbers ?? []),
            'emails' => json_encode($emails ?? []),
        ]);


        if ($redirect) {
            return redirect()->route('admin.contacts.related-contacts.edit',[$relatedContact->id])
                ->with('success', 'Updated successfully.');
        }

        return response()->json(['message' => 'Updated successfully.']);
    }

    public function destroy(RelatedContact $relatedContact)
    {
        $relatedContact->delete();

        return response()->json(['message' => 'Related contact deleted successfully.']);
    }

    public function show(int $id): View
    {
        $relatedContact = $this->relatedContactRepository->findOrFail($id);

//        $user = auth()->user();
//        $allowedFields = $user->role->visible_person_fields ?? [];
//
//        // Always include 'id' for routing/model logic
//        if (!in_array('id', $allowedFields)) {
//            $allowedFields[] = 'id';
//        }


        return view('admin::contacts.related.view', [
            'relatedContact' => (object) $relatedContact,
        ]);
    }


    public function create(): View
    {
        return view('admin::contacts.related.create');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {

        $relatedContact = $this->relatedContactRepository->findOrFail($id);

        return view('admin::contacts.related.edit', compact('relatedContact'));
    }


}
