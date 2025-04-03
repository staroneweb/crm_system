<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

use function Symfony\Component\VarDumper\Dumper\esc;

class TaskController extends Controller
{
    public function taskAdd(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'task_name' => 'required',
                'task_description' => 'required',
                'assigned_to' => 'required',
                'due_date'  => 'required',
                'duration'=>'required',
                'start_datetime'=>'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 422, 'message' => $validator->errors()]);
            }

            $due_date = Carbon::createFromFormat('d-m-Y', $request->due_date)->format('Y-m-d');
            $start_datetime = Carbon::createFromFormat('d-m-Y', $request->start_datetime)->format('Y-m-d');


            $task = new Tasks();
            $task->lead_id = $request->lead_id;
            $task->task_name = $request->task_name;
            $task->task_description = $request->task_description;
            $task->status_id = $request->status_id;
            $task->start_datetime = $start_datetime;
            $task->duration=$request->duration;
            $task->assigned_to = $request->assigned_to;
            $task->due_date = $due_date;

            if ($task->save()) {

                return response()->json(['status' => 200, 'message' => 'Task save successfully!']);
            } else {

                return response()->json(['status' => 500, 'message' => 'Task save failed!']);
            }
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while task add. Please try again later.']);
        }
    }
    public function taskEdit(Request $request)
    {

        try {

            $task_data = Tasks::with(['assignedUser','status'])->where('id', $request->task_id)->first();

            if (!$task_data) {
                return response()->json(['status' => 500, 'message' => 'Task Not Found!']);
            }

            $data[] = [

                'task_id'   => $task_data->id,
                'task_name' => $task_data->task_name,
                'task_description' => $task_data->task_description,
                'status'=> $task_data->status->status_name,
                'start_datetime' => Carbon::parse($task_data->start_datetime)->format('d-m-Y'),
                'assigned_to' => $task_data->assignedUser->name,
                'duration' => $task_data->duration,
                'due_date'    => Carbon::parse($task_data->due_date)->format('d-m-Y')

            ];

            return response()->json(['status' => 200, 'data' => $data]);
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while task edit. Please try again later.']);
        }
    }
    public function taskUpdate(Request $request)
    {

        try {
            $task_data = Tasks::with(['assignedUser'])->where('id', $request->task_id)->first();

            if (!$task_data) {
                return response()->json(['status' => 500, 'message' => 'Task Not Found!']);
            }

            $validator = Validator::make($request->all(), [
                'task_name' => 'required',
                'task_description' => 'required',
                'assigned_to' => 'required',
                'due_date'  => 'required',
                'duration'=>'required',
                'start_datetime'=>'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 422, 'message' => $validator->errors()]);
            }

            $due_date = Carbon::createFromFormat('d-m-Y', $request->due_date)->format('Y-m-d');
            $start_datetime = Carbon::createFromFormat('d-m-Y', $request->start_datetime)->format('Y-m-d');


            $task_data->task_name = $request->task_name;
            $task_data->task_description = $request->task_description;
            $task_data->status_id = $request->status_id;
            $task_data->start_datetime = $start_datetime;
            $task_data->duration=$request->duration;
            $task_data->assigned_to = $request->assigned_to;
            $task_data->due_date = $due_date;

            if ($task_data->save()) {

                return response()->json(['status' => 200, 'message' => 'Task update successfully!']);
            } else {

                return response()->json(['status' => 500, 'message' => 'Task update failed!']);
            }
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while task update. Please try again later.']);
        }
    }
    public function taskDelete(Request $request)
    {

        try {

            $task_data = Tasks::with(['assignedUser'])->where('id', $request->task_id)->first();

            if (!$task_data) {
                return response()->json(['status' => 500, 'message' => 'Task Not Found!']);
            }

            if ($task_data->delete()) {

                return response()->json(['status' => 200, 'message' => 'Task delete successfully!']);
            } else {

                return response()->json(['status' => 500, 'message' => 'Task delete failed!']);
            }
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while task delete. Please try again later.']);
        }
    }
    public function taskStatus(Request $request) {}
    public function taskList(Request $request)
    {

        try {

            $query = Tasks::with(['assignedUser','status']);

            if (preg_match('/\d{2}-\d{2}-\d{4}/', $request->search)) {

                $date = Carbon::createFromFormat('d-m-Y', $request->search)->format('Y-m-d');

            } else {

                $date = null;
            }


            if (isset($request->search)) {

                $query->where('task_name', 'like', '%' . $request->search . '%')
                    ->orwhere('task_description', 'like', '%' . $request->search . '%')
                    ->orwhere('duration', 'like', '%' . $request->search . '%')
                    ->orwhere('due_date', $date)
                    ->orwhere('start_datetime', $date)
                    ->orwhereHas('status', function ($q) use ($request) {
                        $q->where('status_name', 'like', '%' . $request->search . '%');
                    })
                    ->orwhereHas('assignedUser', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%');
                    });
            }

            $task_data = $query->get();

            if ($task_data->isEmpty()) {

                return response()->json(['status' => 200, 'message' => 'Data Not Found!']);
            }

            $data = [];

            foreach ($task_data as $t_d) {

                $data[] = [
                    'task_id' => $t_d->id,
                    'task_name'  => $t_d->task_name,
                    'task_description' => $t_d->task_description,
                    'status'=>$t_d->status->status_name,
                    'start_datetime'=>Carbon::parse($t_d->start_datetime)->format('d-m-Y'),
                    'assigned_to' => $t_d->assignedUser->name,
                    'due_date'  => Carbon::parse($t_d->due_date)->format('d-m-Y'),
                    'duration' => $t_d->duration,
                ];
            }

            return response()->json(['status' => 200, 'data' => $data]);
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while task list. Please try again later.']);
        }
    }
}
