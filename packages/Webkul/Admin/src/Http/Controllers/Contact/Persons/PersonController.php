<?php

namespace Webkul\Admin\Http\Controllers\Contact\Persons;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Prettus\Repository\Criteria\RequestCriteria;
use Webkul\Activity\Models\Activity;
use Webkul\Activity\Models\File;
use Webkul\Admin\DataGrids\Contact\PersonDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\AttributeForm;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Admin\Http\Resources\PersonResource;
use Webkul\Attribute\Models\AttributeValue;
use Webkul\Contact\Models\RelatedContact;
use Webkul\Contact\Repositories\PersonRepository;

class PersonController extends Controller
{
    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct(protected PersonRepository $personRepository)
    {
        request()->request->add(['entity_type' => 'persons']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {

            return datagrid(PersonDataGrid::class)->process();
        }

        return view('admin::contacts.persons.index');
    }

    public function nextCrmCode($return_number = false)
    {

//        $maxId = DB::table('persons')->max('id') ?? 0;
//        return response()->json(['next_crm_code' => $maxId + 1]);

        $nextCrm = DB::selectOne("
    SELECT
        COALESCE(
            (
                SELECT MIN(p1.crm + 1)
                FROM persons p1
                LEFT JOIN persons p2
                  ON p2.crm = p1.crm + 1
                WHERE p2.crm IS NULL
                  AND p1.crm >= 1
            ),
            COALESCE((SELECT MAX(crm) + 1 FROM persons), 1)
        ) AS next_crm
");

// Access the value:
        $maxId = $nextCrm->next_crm;
        if ($return_number) {
            return $maxId;
        }
        return response()->json(['next_crm_code' => $maxId]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin::contacts.persons.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttributeForm $request): RedirectResponse|JsonResponse
    {

        $data = $request->all();
        $relatedContacts = [];
        if ($request->has('related_contacts')) {
            $relatedContacts = $request->input('related_contacts');

            // Remove 'related_contacts' from the request instance
            $request->request->remove('related_contacts');

            // Now $relatedContacts holds the data, and it's no longer in $request
            // dd($relatedContacts);


            foreach ($relatedContacts as $index => $relatedContact) {
                if ($request->has("mobile_number_$index")) {
                    if ($request->get("mobile_number_$index") !== "+971") {

                        $mobile_numbers = $this->ensureJsonArr($relatedContact['mobile_numbers']);

                        $mobile_numbers[] = $request->get("mobile_number_$index");

                        $relatedContacts[$index]['mobile_numbers'] = json_encode($mobile_numbers, true);

                        unset($data["mobile_number_$index"]);
                    }
                }
                if ($request->has("email_$index")) {
                    if (!empty($request->get("email_$index"))) {

                        $emails = $this->ensureJsonArr($relatedContact['emails']);

                        $emails[] = $request->get("email_$index");

                        $relatedContacts[$index]['emails'] = json_encode($emails, true);


                        unset($data["email_$index"]);
                    }
                }


            }
        }

        Event::dispatch('contacts.person.create.before');

        $person = $this->personRepository->create($data);

        if (empty($request->get('crm'))) {
            $crm_code = $this->nextCrmCode(true);

        } else {
            $crm_code = $request->get('crm');
        }
        $person->crm = $crm_code;
        $person->save();

        Event::dispatch('contacts.person.create.after', $person);


        foreach ($relatedContacts as $index => $contactData) {
            if (!empty($contactData['id'])) {
                // Update existing
                $existingContact = RelatedContact::find($contactData['id']);
                if ($existingContact) {
                    $existingContact->update(['name' => $contactData['name'], 'person_id' => $person->id, 'type' => $contactData['type'], 'eid_expiry' => $contactData['eid_expiry'], 'mobile_numbers' => $contactData['mobile_numbers'], 'emails' => $contactData['emails'],]);
                }
            } else {
                // Create new
                RelatedContact::create(['name' => $contactData['name'], 'person_id' => $person->id, 'type' => $contactData['type'], 'eid_expiry' => $contactData['eid_expiry'], 'mobile_numbers' => $contactData['mobile_numbers'], 'emails' => $contactData['emails'],]);
            }
        }


        if (request()->ajax()) {
            return response()->json(['data' => $person, 'message' => trans('admin::app.contacts.persons.index.create-success'),]);
        }

        session()->flash('success', trans('admin::app.contacts.persons.index.create-success'));

        return redirect()->route('admin.contacts.persons.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {

//        $person = $this->personRepository->findOrFail($id);
        $person = $this->personRepository->where('crm', '=', $id)->first();

        $user = auth()->user();
        $allowedFields = $user->role->visible_person_fields ?? [];

        // Always include 'id' for routing/model logic
        if (!in_array('id', $allowedFields)) {
            $allowedFields[] = 'id';
        }

        //  $person = $this->personRepository->getModel()->select($allowedFields)->findOrFail($id);


        return view('admin::contacts.persons.view', ['person' => (object)$person,]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {

//        $person = $this->personRepository->findOrFail($id);
        $person = $this->personRepository->where('crm', '=', $id)->first();

        return view('admin::contacts.persons.edit', compact('person'));
    }

    private function ensureJsonArr($input)
    {
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

    /**
     * Update the specified resource in storage.
     */
    public function update(AttributeForm $request, int $id): RedirectResponse|JsonResponse
    {
        $request->merge(['entity_id' => $id]);

        $data = $request->all();
        $relatedContacts = $request->related_contacts;

        foreach ($relatedContacts as $index => $relatedContact) {
            if ($request->has("mobile_number_$index")) {
                if ($request->get("mobile_number_$index") !== "+971") {

                    $mobile_numbers = $this->ensureJsonArr($relatedContact['mobile_numbers']);

                    $mobile_numbers[] = $request->get("mobile_number_$index");

                    $rc = RelatedContact::findorfail($relatedContact['id']);
                    $rc->mobile_numbers = json_encode($mobile_numbers, true);
                    $rc->save();

                    unset($data["mobile_number_$index"]);
                }
            }
            if ($request->has("email_$index")) {
                if (!empty($request->get("email_$index"))) {

                    $emails = $this->ensureJsonArr($relatedContact['emails']);

                    $emails[] = $request->get("email_$index");

                    $rc = RelatedContact::findorfail($relatedContact['id']);
                    $rc->emails = json_encode($emails, true);
                    $rc->save();

                    unset($data["email_$index"]);
                }
            }


        }
        $person = $this->personRepository->where('crm', '=', $id)->first();

        Event::dispatch('contacts.person.update.before', $person->id);


        //$person = $this->personRepository->update($data, $id);
        $person = $this->personRepository->update($data, $person->id);
        if (empty($request->get('crm'))) {
            $crm_code = $this->nextCrmCode(true);

        } else {
            $crm_code = $request->get('crm');
        }
        $person->crm = $crm_code;
        $person->save();
        Event::dispatch('contacts.person.update.after', $person);

        if (request()->ajax()) {
            return response()->json(['data' => $person, 'message' => trans('admin::app.contacts.persons.index.update-success'),], 200);
        }

        session()->flash('success', trans('admin::app.contacts.persons.index.update-success'));

        return redirect()->route('admin.contacts.persons.index');
    }

    /**
     * Search person results.
     */
    public function search(): JsonResource
    {
        if ($userIds = bouncer()->getAuthorizedUserIds()) {
            $persons = $this->personRepository->pushCriteria(app(RequestCriteria::class))->findWhereIn('user_id', $userIds);
        } else {
            $persons = $this->personRepository->pushCriteria(app(RequestCriteria::class))->all();
        }

        return PersonResource::collection($persons);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        //$person = $this->personRepository->findOrFail($id);
        $person = $this->personRepository->where('crm', '=', $id)->first();

        try {
            Event::dispatch('contacts.person.delete.before', $person->id);

            $person->delete($person->id);

            Event::dispatch('contacts.person.delete.after', $person->id);

            return response()->json(['message' => trans('admin::app.contacts.persons.index.delete-success'),], 200);
        } catch (Exception $exception) {
            return response()->json(['message' => trans('admin::app.contacts.persons.index.delete-failed'),], 400);
        }
    }

    /**
     * Mass Delete the specified resources.
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $persons = $this->personRepository->findWhereIn('id', $massDestroyRequest->input('indices'));

        foreach ($persons as $person) {
            Event::dispatch('contact.person.delete.before', $person);

            $this->personRepository->delete($person->id);

            Event::dispatch('contact.person.delete.after', $person);
        }

        return response()->json(['message' => trans('admin::app.contacts.persons.index.delete-success'),]);
    }


    public function duplicate(int $id): JsonResponse|RedirectResponse
    {
        $originalPerson = $this->personRepository->where('crm', $id)->first();

        if (!$originalPerson) {
            return response()->json(['message' => 'Person not found'], 404);
        }

        DB::beginTransaction();
        $mapIds = [];

        try {
            // 1️⃣ Duplicate Person (excluding ID)
            $newPerson = $originalPerson->replicate();

            // 2️⃣ Set CRM code from request if provided, otherwise generate new
            $crmFromRequest = request()->input('crm');

            $newPerson->crm = $crmFromRequest ?: $this->nextCrmCode(true);

            $newPerson->save();

            $mapIds[$originalPerson->id] = $newPerson->id;

            // 3️⃣ Duplicate Related Contacts
            foreach ($originalPerson->relatedContacts as $contact) {
                $newContact = $contact->replicate();
                $newContact->person_id = $newPerson->id;
                $newContact->save();
                $mapIds[$contact->id] = $newContact->id;
            }

            // 4️⃣ Duplicate Activities
            $activityIds = $originalPerson->activities()->pluck('activity_id')->toArray();
            $newPerson->activities()->sync($activityIds);

            // 5️⃣ Duplicate Tags
            $tagIds = $originalPerson->tags()->pluck('tag_id')->toArray();
            $newPerson->tags()->sync($tagIds);

            // 6️⃣ Duplicate Attributes
            // Option A: if attributes are JSON on person table

            $activities = $originalPerson->activities()->get();

            foreach ($activities as $activity) {

                $newActivity = new Activity();
                $newActivity->title = $activity->title;
                $newActivity->type = $activity->type;
                $newActivity->location = $activity->location;
                $newActivity->comment = $activity->comment;
                $newActivity->additional = $activity->additional;
                $newActivity->schedule_from = $activity->schedule_from;
                $newActivity->schedule_to = $activity->schedule_to;
                $newActivity->is_done = $activity->is_done;
                $newActivity->user_id = $activity->user_id;
                $newActivity->save();

                DB::table('person_activities')->insert(
                    [
                        'activity_id'=>$newActivity->id,
                        'person_id'=>$newPerson->id,
                    ]
                );

                $files = File::where('activity_id', $activity->id)->get();

                foreach ($files as $file) {
                    if(isset($mapIds[$file->entity_id])){
                        $newFile = $file->replicate();
                        $newFile->activity_id = $newActivity->id;
                        $newFile->entity_id = $mapIds[$file->entity_id];
                        $newFile->save();
                    }
                }
            }

            $originalAttributes = AttributeValue::where('entity_type', 'persons')->where('entity_id', $originalPerson->id)->get();

            $originalAttributes->each(function ($attributeValue) use ($newPerson) {
                $newAttributeValue = $attributeValue->replicate();
                $newAttributeValue->entity_id = $newPerson->id;

                // Temporarily disable unique validation if your repository checks it
                $newAttributeValue->saveQuietly(); // Bypasses events/validation
            });


            DB::commit();

            return response()->json(['data' => $newPerson, 'message' => 'Person duplicated successfully with all related data and attributes',]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Duplication failed: ' . $e->getMessage(),], 500);
        }

    }


}
