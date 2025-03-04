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
                'title' => 'required',
                'description' => 'required',
                'assigned_to' => 'required',
                'due_date'  => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 422, 'message' => $validator->errors()]);
            }

            $due_date = Carbon::createFromFormat('d-m-Y', $request->due_date)->format('Y-m-d');

            $task = new Tasks();
            $task->title = $request->title;
            $task->description = $request->description;
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

            $task_data = Tasks::with(['assignedUser'])->where('id', $request->task_id)->first();

            if (!$task_data) {
                return response()->json(['status' => 500, 'message' => 'Task Not Found!']);
            }

            $data[] = [

                'task_id'   => $task_data->id,
                'task_title' => $task_data->title,
                'description' => $task_data->description,
                'assigned_to' => $task_data->assignedUser->name,
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
                'title' => 'required',
                'description' => 'required',
                'assigned_to' => 'required',
                'due_date'  => 'required',
                'status'   => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 422, 'message' => $validator->errors()]);
            }

            $due_date = Carbon::createFromFormat('d-m-Y', $request->due_date)->format('Y-m-d');


            $task_data->title = $request->title;
            $task_data->description = $request->description;
            $task_data->assigned_to = $request->assigned_to;
            $task_data->due_date = $due_date;
            $task_data->status = $request->status;

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

            $query = Tasks::with(['assignedUser']);

            if (preg_match('/\d{2}-\d{2}-\d{4}/', $request->search)) {

                $due_date = Carbon::createFromFormat('d-m-Y', $request->search)->format('Y-m-d');

            } else {

                $due_date = null;
            }


            if (isset($request->search)) {

                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orwhere('description', 'like', '%' . $request->search . '%')
                    ->orwhere('status', 'like', '%' . $request->search . '%')
                    ->orwhere('due_date', $due_date)
                    ->orwhereHas('assignedUser', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%');
                    });
            }

            $task_data = $query->get();

            if (!$task_data) {

                return response()->json(['status' => 200, 'message' => 'Data Not Found!']);
            }

            $data = [];

            foreach ($task_data as $t_d) {

                $data[] = [
                    'task_id' => $t_d->id,
                    'title'  => $t_d->title,
                    'description' => $t_d->description,
                    'assigned_to' => $t_d->assignedUser->name,
                    'due_date'  => Carbon::parse($t_d->due_date)->format('d-m-Y'),
                    'status' => $t_d->status
                ];
            }

            return response()->json(['status' => 200, 'data' => $data]);
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while task list. Please try again later.']);
        }
    }
}
