<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;


class LeadController extends Controller
{
    // List all leads with search and pagination
    public function index(Request $request)
    {
        try {
            $query = Lead::with([
                'source:id,source_name', // Eager load lead source with name
                'status:id,stage_name', // Eager load lead status with name
                'assignedUser:id,name'
            ]);

            // Apply search filters
            if ($request->has('lead_source')) {
                $query->whereHas('source', function ($q) use ($request) {
                    $q->where('source_name', 'LIKE', "%{$request->lead_source}%");
                });
            }

            if ($request->has('lead_status')) {
                $query->whereHas('status', function ($q) use ($request) {
                    $q->where('stage_name', 'LIKE', "%{$request->lead_status}%");
                });
            }

            if ($request->has('assigned_to')) {
                $query->where('assigned_to', $request->assigned_to);
            }

            $leads = $query->paginate(10);

            return response()->json([
                'status' => 200,
                'message' => 'Leads fetched successfully',
                'leads' => collect($leads->items())->map(function ($lead) {
                    return [
                        'id' => $lead->id,
                        'name' => $lead->name,
                        'source' => $lead->source->source_name ?? null,
                        'status' => $lead->status->stage_name ?? null,
                        'company_name' => $lead->company_name,
                        'opportunity_amount' => $lead->opportunity_amount,
                        'assigned_user_name' => $lead->assignedUser->name ?? null,
                        'created_at' => $lead->created_at ? $lead->created_at->format('d-M-Y H:i:s') : null,
                        'updated_at' => $lead->updated_at ? $lead->updated_at->format('d-M-Y H:i:s') : null,
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Error fetching leads', 'error' => $e->getMessage()]);
        }
    }


    // Store a new lead
    public function store(Request $request)
    {

       // dd($request);

        try {
            $validator=Validator::make($request->all(),[
                'contact_id' => 'required|exists:tbl_contacts,id',
                'source' => 'required|string|max:100',
                'stage' => 'required|in:new,qualified,proposal,won,lost',
                'value' => 'required|numeric',
                'assigned_to' => 'required|exists:tbl_users,id',
            ],
            [
                'contact_id.required' => 'The contact ID is required.', 
                'assigned_to.required' => 'The Assign User is required.',
            ]
            
        );

            if($validator->fails()){
                return response()->json(['status'=>422,'message'=>$validator->errors()]);
            }

            $lead = Lead::create($request->all());

            return response()->json(['status' => 200, 'message' => 'Lead created successfully', 'lead' => $lead]);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error creating lead', 'error' => $e->getMessage()]);
        }
    }

    // Show a single lead
    public function show($id)
    {
        try {
            $lead = Lead::find($id);
            if (!$lead) {
                return response()->json(['status' => 404, 'message' => 'Lead not found']);
            }
            return response()->json(['status' => 200, 'message' => 'Lead fetched successfully', 'lead' => $lead]);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error fetching lead', 'error' => $e->getMessage()]);
        }
    }

    //Lead Update 
    public function update(Request $request, $id)
    {
        try {
            $lead = Lead::find($id);
            if (!$lead) {
                return response()->json(['message' => 'Lead not found'], 404);
            }

            $request->validate([
                'contact_id' => 'sometimes|exists:tbl_contacts,id',
                'source' => 'nullable|string|max:100',
                'stage' => 'sometimes|in:new,qualified,proposal,won,lost',
                'value' => 'nullable|numeric',
                'assigned_to' => 'nullable|exists:tbl_users,id',
            ]);

            $lead->update($request->all());

            return response()->json(['status' => 200, 'message' => 'Lead updated successfully', 'lead' => $lead]);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error fetching lead', 'error' => $e->getMessage()]);
        }
    }




    // Delete a lead
    public function destroy($id)
    {
        try {
            $lead = Lead::find($id);
            if (!$lead) {
                return response()->json(['status' => 404, 'message' => 'Lead not found']);
            }
            $lead->delete();
            return response()->json(['status' => 200, 'message' => 'Lead deleted successfully']);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error deleting lead', 'error' => $e->getMessage()]);
        }
    }
}
